<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\Seat;
use App\Models\Admin\ScreeningRoom;
use App\Models\Admin\SeatType;
use App\Models\Admin\Ticket;
use App\Models\Admin\ShowTime;

class SeatController extends Controller
{
    private function findSeatTypeIdByKeywords(array $keywords): ?int
    {
        $query = SeatType::query();
        foreach ($keywords as $index => $keyword) {
            $method = $index === 0 ? 'where' : 'orWhere';
            $query->{$method}('seatTypeName', 'like', '%' . $keyword . '%');
        }
        return $query->value('seatTypeID');
    }

    private function getCoupleTypeId(): int
    {
        return $this->findSeatTypeIdByKeywords(['đôi', 'Đôi', 'couple', 'double']) ?? 3;
    }

    private function getMaintenanceTypeId(): int
    {
        return $this->findSeatTypeIdByKeywords(['bảo trì', 'Bảo trì', 'maintenance']) ?? 4;
    }

    private function getNormalTypeId(): int
    {
        return $this->findSeatTypeIdByKeywords(['thường', 'Thường', 'normal']) ?? 2;
    }

    // ================= LIST =================
    public function index(Request $request)
    {
        $rooms  = ScreeningRoom::all();
        $roomID = (int) ($request->input('roomID') ?? optional($rooms->first())->roomID);
        $room   = ScreeningRoom::find($roomID);

        if (!$room && $rooms->isNotEmpty()) {
            $room   = $rooms->first();
            $roomID = (int) $room->roomID;
        }

        $seatTypes = SeatType::all();

        $specialTypeIds = [
            'vip'         => $this->findSeatTypeIdByKeywords(['vip']) ?? 1,
            'normal'      => $this->getNormalTypeId(),
            'couple'      => $this->getCoupleTypeId(),
            'maintenance' => $this->getMaintenanceTypeId(),
        ];
        $seatTypeNames = $seatTypes->pluck('seatTypeName', 'seatTypeID')->toArray();

        if (!$room) {
            return view('admins.manageCinema.seat.index', [
                'seats'          => collect(),
                'room'           => null,
                'rooms'          => $rooms,
                'seatTypes'      => $seatTypes,
                'roomID'         => 0,
                'specialTypeIds' => $specialTypeIds,
                'seatTypeNames'  => $seatTypeNames,
            ]);
        }

        $search = trim((string) $request->input('search'));

        $seats = Seat::with(['seatType', 'screeningRoom'])
            ->where('roomID', $roomID)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('seatID',  'like', "%{$search}%")
                      ->orWhere('rowSeat', 'like', "%{$search}%")
                      ->orWhere('colSeat', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT(rowSeat, colSeat) LIKE ?", ["%{$search}%"])
                      ->orWhereHas('seatType', fn($t) => $t->where('seatTypeName', 'like', "%{$search}%"));
                });
            })
            ->orderBy('rowSeat')
            ->orderBy('colSeat')
            ->paginate(5)
            ->withQueryString();

        return view('admins.manageCinema.seat.index', compact(
            'seats', 'room', 'rooms', 'seatTypes', 'roomID', 'specialTypeIds', 'seatTypeNames'
        ));
    }

    // ================= CREATE =================
    public function create()
    {
        $rooms     = ScreeningRoom::all();
        $seatTypes = SeatType::all();
        return view('admins.manageCinema.seat.create', compact('rooms', 'seatTypes'));
    }

    // ================= STORE (FORM) =================
    public function store(Request $request)
    {
        $request->validate([
            'rowSeat'    => 'required|string|max:2',
            'colSeat'    => 'required|integer|min:1|max:30',
            'roomID'     => 'required|exists:screening_rooms,roomID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID',
        ]);

        if (ShowTime::where('roomID', $request->roomID)->exists()) {
            return back()->with('error', 'Phòng đang có suất chiếu');
        }

        $exists = Seat::where('roomID',  $request->roomID)
            ->where('rowSeat', $request->rowSeat)
            ->where('colSeat', $request->colSeat)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ghế đã tồn tại');
        }

        Seat::create($request->all());
        return redirect()->route('seat.index')->with('success', 'Tạo ghế thành công');
    }

    // ================= STORE AJAX =================
    public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'rowSeat'    => 'required|string|max:2',
            'colSeat'    => 'required|integer|min:1|max:30',
            'roomID'     => 'required|exists:screening_rooms,roomID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID',
        ]);

        if (ShowTime::where('roomID', $validated['roomID'])->exists()) {
            return response()->json(['error' => 'Phòng đang có suất chiếu không thể tạo ghế'], 400);
        }

        $exists = Seat::where('roomID',  $validated['roomID'])
            ->where('rowSeat', $validated['rowSeat'])
            ->where('colSeat', $validated['colSeat'])
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Ghế đã tồn tại'], 400);
        }

        $seat = Seat::create($validated);
        return response()->json($seat);
    }

    // ================= UPDATE MULTIPLE =================
    public function updateMultiple(Request $request)
    {
        $request->validate([
            'seatIDs'    => 'required|array|min:1',
            'seatIDs.*'  => 'exists:seats,seatID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID',
        ]);

        $firstSeat = Seat::whereIn('seatID', $request->seatIDs)->firstOrFail();
        $roomID    = $firstSeat->roomID;

        if (ShowTime::where('roomID', $roomID)->exists()) {
            return redirect()
                ->route('seat.index', ['roomID' => $roomID])
                ->with('error', 'Phòng đang có suất chiếu');
        }

        Seat::whereIn('seatID', $request->seatIDs)
            ->update(['seatTypeID' => $request->seatTypeID]);

        return redirect()
            ->route('seat.index', ['roomID' => $roomID])
            ->with('success', 'Cập nhật thành công');
    }

    // ================= EDIT =================
    public function edit(string $id)
    {
        $seat      = Seat::findOrFail($id);
        $rooms     = ScreeningRoom::all();
        $seatTypes = SeatType::all();
        return view('admins.manageCinema.seat.edit', compact('seat', 'rooms', 'seatTypes'));
    }

    // ================= UPDATE =================
    public function update(Request $request, string $id)
    {
        $request->validate([
            'rowSeat'    => 'required|string|max:2',
            'colSeat'    => 'required|integer|min:1|max:30',
            'roomID'     => 'required|exists:screening_rooms,roomID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID',
        ]);

        $seat = Seat::findOrFail($id);

        if (ShowTime::where('roomID', $seat->roomID)->exists()) {
            return back()->with('error', 'Phòng đang có suất chiếu');
        }

        $seat->update($request->all());
        return redirect()->route('seat.index')->with('success', 'Cập nhật ghế thành công');
    }

    // ================= DELETE (FORM) =================
    public function destroy(string $id)
    {
        $seat = Seat::findOrFail($id);

        if (ShowTime::where('roomID', $seat->roomID)->exists()) {
            return back()->with('error', 'Phòng đang có suất chiếu');
        }

        if (Ticket::where('seatID', $id)->exists()) {
            return back()->with('error', 'Ghế đã có vé');
        }

        $seat->delete();
        return redirect()->route('seat.index')->with('success', 'Xóa ghế thành công');
    }

    // ================= DELETE AJAX =================
    public function deleteAjax($id)
    {
        $seat = Seat::findOrFail($id);

        if (ShowTime::where('roomID', $seat->roomID)->exists()) {
            return response()->json(['error' => 'Phòng đang có suất chiếu không thể xóa'], 400);
        }

        if (Ticket::where('seatID', $id)->exists()) {
            return response()->json(['error' => 'Ghế đã có vé'], 400);
        }

        $seat->delete();
        return response()->json(['success' => true]);
    }

    // ================= LOAD SEAT AJAX =================
    public function getSeatsByRoom($roomID)
    {
        $seats = Seat::with('seatType')
            ->where('roomID', $roomID)
            ->orderBy('rowSeat')
            ->orderBy('colSeat')
            ->get();

        return response()->json($seats);
    }

    // ================= AJAX SWAP COL =================
    public function ajaxSwapCol(Request $request)
    {
        $request->validate([
            'seatID_a'  => 'required|integer|exists:seats,seatID',
            'colSeat_a' => 'required|integer|min:1',
            'seatID_b'  => 'required|integer|exists:seats,seatID',
            'colSeat_b' => 'required|integer|min:1',
        ]);

        $seatA = Seat::findOrFail($request->seatID_a);
        $seatB = Seat::findOrFail($request->seatID_b);

        if ($seatA->rowSeat !== $seatB->rowSeat) {
            return response()->json([
                'success' => false,
                'message' => 'Hai ghế phải cùng hàng mới có thể đổi vị trí.',
            ], 422);
        }

        DB::transaction(function () use ($seatA, $seatB, $request) {
            $seatA->colSeat = 0;
            $seatA->save();
            $seatB->colSeat = $request->colSeat_a;
            $seatB->save();
            $seatA->colSeat = $request->colSeat_b;
            $seatA->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Hoán đổi vị trí ghế thành công.',
            'seatA'   => $seatA->fresh(),
            'seatB'   => $seatB->fresh(),
        ]);
    }

    // ================= AJAX SWAP TYPE =================
    public function ajaxSwapType(Request $request)
    {
        $v = $request->validate([
            'seatID_a' => 'required|integer|exists:seats,seatID',
            'seatID_b' => 'required|integer|exists:seats,seatID',
        ]);

        $seatA = Seat::findOrFail($v['seatID_a']);
        $seatB = Seat::findOrFail($v['seatID_b']);

        if ($seatA->roomID !== $seatB->roomID) {
            return response()->json(['success' => false, 'message' => 'Hai ghế phải cùng phòng'], 422);
        }

        if (DB::table('tickets')->whereIn('seatID', [$seatA->seatID, $seatB->seatID])->exists()) {
            return response()->json(['success' => false, 'message' => 'Ghế đã có vé, không thể hoán đổi'], 422);
        }

        DB::transaction(function () use ($seatA, $seatB) {
            [$seatA->seatTypeID, $seatB->seatTypeID] = [$seatB->seatTypeID, $seatA->seatTypeID];
            $seatA->save();
            $seatB->save();
        });

        return response()->json(['success' => true]);
    }

    // ================= AJAX UPDATE TYPE (1 ghế) =================
    public function ajaxUpdateType(Request $request)
    {
        $validated = $request->validate([
            'seatID'     => 'required|integer|exists:seats,seatID',
            'seatTypeID' => 'required|integer|exists:seat_types,seatTypeID',
        ]);

        $seat              = Seat::findOrFail($validated['seatID']);
        $newTypeID         = (int) $validated['seatTypeID'];
        $maintenanceTypeID = $this->getMaintenanceTypeId();

        // Chỉ chặn vé khi không phải chuyển sang bảo trì
        if ($newTypeID !== $maintenanceTypeID) {
            if (DB::table('tickets')->where('seatID', $seat->seatID)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ghế đã có vé, không thể thay đổi loại.',
                ], 422);
            }
        }

        $seat->seatTypeID = $newTypeID;
        $seat->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật loại ghế thành công.',
            'seat'    => $seat->fresh(),
        ]);
    }

    // ================= AJAX BATCH UPDATE TYPE =================
    public function ajaxBatchUpdateType(Request $request)
    {
        $request->validate([
            'seatIDs'    => 'required|array|min:1',
            'seatIDs.*'  => 'integer|exists:seats,seatID',
            'seatTypeID' => 'required|integer|exists:seat_types,seatTypeID',
        ]);

        $newTypeID         = (int) $request->seatTypeID;
        $ids               = array_map('intval', $request->seatIDs);
        $maintenanceTypeID = $this->getMaintenanceTypeId();
        $isMaintenance     = $newTypeID === $maintenanceTypeID;
        $validSeatIds      = [];

        $selectedSeats = Seat::whereIn('seatID', $ids)->get();

        foreach ($selectedSeats as $seat) {
            if (!$isMaintenance && DB::table('tickets')->where('seatID', $seat->seatID)->exists()) {
                continue;
            }
            $validSeatIds[] = $seat->seatID;
        }

        $updated = count($validSeatIds);
        $skipped = count($ids) - $updated;

        if ($updated === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ghế nào hợp lệ để cập nhật (ghế có vé bị bỏ qua).',
            ]);
        }

        Seat::whereIn('seatID', $validSeatIds)->update(['seatTypeID' => $newTypeID]);

        $typeName = SeatType::find($newTypeID)?->seatTypeName ?? 'loại mới';
        $msg      = "Đã cập nhật {$updated} ghế sang {$typeName}.";
        if ($skipped > 0) $msg .= " Bỏ qua {$skipped} ghế đã có vé.";

        return response()->json(['success' => true, 'updated' => $updated, 'message' => $msg]);
    }

    // ================= EDIT MULTIPLE =================
    public function editMultiple(Request $request)
    {
        $ids       = explode(',', $request->seatIDs);
        $seats     = Seat::whereIn('seatID', $ids)->get();
        $seatTypes = SeatType::all();
        $rooms     = ScreeningRoom::all();
        $roomID    = $seats->first()?->roomID;

        return view('admins.manageCinema.seat.edit-multiple', compact(
            'seats', 'seatTypes', 'rooms', 'roomID'
        ));
    }

    // ================= UPDATE TYPE =================
    public function updateType(Request $request)
    {
        $request->validate([
            'seatIDs'    => 'required|array|min:1',
            'seatIDs.*'  => 'exists:seats,seatID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID',
        ]);

        $firstSeat = Seat::whereIn('seatID', $request->seatIDs)->first();

        if (!$firstSeat) {
            return response()->json(['error' => 'Không tìm thấy ghế'], 404);
        }

        if (ShowTime::where('roomID', $firstSeat->roomID)->exists()) {
            return response()->json(['error' => 'Phòng đang có suất chiếu không thể sửa'], 400);
        }

        $validSeatIDs = Seat::whereIn('seatID', $request->seatIDs)
            ->whereNotIn('seatID', function ($q) {
                $q->select('seatID')->from('tickets');
            })
            ->pluck('seatID');

        $updated = Seat::whereIn('seatID', $validSeatIDs)
            ->update(['seatTypeID' => $request->seatTypeID]);

        return response()->json([
            'success' => true,
            'updated' => $updated,
            'skipped' => count($request->seatIDs) - $updated,
            'roomID'  => $firstSeat->roomID,
        ]);
    }

    // ================= CLIENT SELECT =================
    public function selectSeat($showtimeID)
    {
        $showtime = ShowTime::findOrFail($showtimeID);

        $seats = Seat::where('roomID', $showtime->roomID)
            ->orderBy('rowSeat')
            ->orderBy('colSeat')
            ->get();

        $bookedSeats = Ticket::where('showTimeID', $showtimeID)
            ->pluck('seatID')
            ->toArray();

        return view('system.seatSelect', compact('seats', 'showtime', 'bookedSeats'));
    }
}