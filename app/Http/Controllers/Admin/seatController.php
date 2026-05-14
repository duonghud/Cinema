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

    // ================= LIST =================
    public function index(Request $request)
    {
        $rooms = ScreeningRoom::all();

        $roomID = $request->roomID ?? optional($rooms->first())->roomID;

        $room = ScreeningRoom::where('roomID', $roomID)->first();

        if (!$room) {
            // fallback nếu lỗi
            $room = $rooms->first();
            $roomID = $room?->roomID;
        }

        $seatTypes = SeatType::all();
        $search = trim((string) $request->input('search'));

        $seats = Seat::with(['seatType', 'screeningRoom'])
            ->where('roomID', $roomID)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($seatQuery) use ($search) {
                    $seatQuery->where('seatID', 'like', "%{$search}%")
                        ->orWhere('rowSeat', 'like', "%{$search}%")
                        ->orWhere('colSeat', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(rowSeat, colSeat) LIKE ?", ["%{$search}%"])
                        ->orWhereHas('seatType', function ($seatTypeQuery) use ($search) {
                            $seatTypeQuery->where('seatTypeName', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('rowSeat')
            ->orderBy('colSeat')
            ->paginate(5)
            ->withQueryString();

        return view('admins.manageCinema.seat.index', compact(
            'seats',
            'room',
            'rooms',
            'seatTypes',
            'roomID'
        ));
    }

    // ================= CREATE =================
    public function create()
    {
        $rooms = ScreeningRoom::all();
        $seatTypes = SeatType::all();

        return view('admins.manageCinema.seat.create', compact('rooms', 'seatTypes'));
    }

    // ================= STORE (FORM) =================
    public function store(Request $request)
    {
        $request->validate([
            'rowSeat' => 'required|string|max:2',
            'colSeat' => 'required|integer|min:1|max:30',
            'roomID' => 'required|exists:screening_rooms,roomID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID'
        ]);

        // 
        if (ShowTime::where('roomID', $request->roomID)->exists()) {
            return back()->with('error', 'Phòng đang có suất chiếu');
        }

        $exists = Seat::where('roomID', $request->roomID)
            ->where('rowSeat', $request->rowSeat)
            ->where('colSeat', $request->colSeat)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ghế đã tồn tại');
        }

        Seat::create($request->all());

        return redirect()->route('seat.index')
            ->with('success', 'Tạo ghế thành công');
    }

    // ================= STORE AJAX =================
    public function storeAjax(Request $request)
    {
        $request->validate([
            'rowSeat' => 'required|string|max:2',
            'colSeat' => 'required|integer|min:1|max:30',
            'roomID' => 'required|exists:screening_rooms,roomID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID'
        ]);

        if (ShowTime::where('roomID', $request->roomID)->exists()) {
            return response()->json([
                'error' => 'Phòng đang có suất chiếu không thể tạo ghế'
            ], 400);
        }

        $exists = Seat::where('roomID', $request->roomID)
            ->where('rowSeat', $request->rowSeat)
            ->where('colSeat', $request->colSeat)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Ghế đã tồn tại'], 400);
        }

        $seat = Seat::create($request->all());

        return response()->json($seat);
    }

    // ================= EDIT =================
    public function edit(string $id)
    {
        $seat = Seat::findOrFail($id);
        $rooms = ScreeningRoom::all();
        $seatTypes = SeatType::all();

        return view('admins.manageCinema.seat.edit', compact(
            'seat',
            'rooms',
            'seatTypes'
        ));
    }

    // ================= UPDATE =================
    public function update(Request $request, string $id)
    {
        $request->validate([
            'rowSeat' => 'required|string|max:2',
            'colSeat' => 'required|integer|min:1|max:30',
            'roomID' => 'required|exists:screening_rooms,roomID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID'
        ]);

        $seat = Seat::findOrFail($id);

        if (ShowTime::where('roomID', $seat->roomID)->exists()) {
            return back()->with('error', 'Phòng đang có suất chiếu');
        }

        $seat->update($request->all());

        return redirect()->route('seat.index')
            ->with('success', 'Cập nhật ghế thành công');
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

        return redirect()->route('seat.index')
            ->with('success', 'Xóa ghế thành công');
    }

    // ================= DELETE AJAX =================
    public function deleteAjax($id)
    {
        $seat = Seat::findOrFail($id);

        if (ShowTime::where('roomID', $seat->roomID)->exists()) {
            return response()->json([
                'error' => 'Phòng đang có suất chiếu không thể xóa'
            ], 400);
        }

        if (Ticket::where('seatID', $id)->exists()) {
            return response()->json([
                'error' => 'Ghế đã có vé'
            ], 400);
        }

        $seat->delete();

        return response()->json(['success' => true]);
    }

    // ================= LOAD SEAT AJAX =================
    public function getSeatsByRoom($roomID)
    {
        $seats = Seat::with('seatType')
            ->where('roomID', $roomID)
            ->get();

        return response()->json($seats);
    }

    public function updateMultiple(Request $request)
    {
        $request->validate([
            'seatIDs'    => 'required|array|min:1',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID'
        ]);

        // Lấy ghế đầu tiên để xác định phòng
        $firstSeat = Seat::whereIn('seatID', $request->seatIDs)->firstOrFail();
        $roomID = $firstSeat->roomID;

        // Không cho sửa nếu phòng đã có suất chiếu
        if (ShowTime::where('roomID', $roomID)->exists()) {
            return redirect()
                ->route('seat.index', ['roomID' => $roomID])
                ->with('error', 'Phòng đang có suất chiếu');
        }

        $newSeatTypeID = (int) $request->seatTypeID;
        $selectedSeatIDs = $request->seatIDs;

        $vipTypeID = seatType::where('seatTypeName', 'VIP')
            ->value('seatTypeID');

        $normalTypeID = seatType::where('seatTypeName', 'Thường')
            ->value('seatTypeID');

        $doubleTypeID = seatType::where('seatTypeName', 'Đôi')
            ->value('seatTypeID');

        $limits = [];

        if ($vipTypeID) {
            $limits[$vipTypeID] = Seat::where('roomID', $roomID)
                ->where('seatTypeID', $vipTypeID)
                ->count();
        }

        if ($normalTypeID) {
            $limits[$normalTypeID] = Seat::where('roomID', $roomID)
                ->where('seatTypeID', $normalTypeID)
                ->count();
        }

        if ($doubleTypeID) {
            $limits[$doubleTypeID] = Seat::where('roomID', $roomID)
                ->where('seatTypeID', $doubleTypeID)
                ->count();
        }

        if (isset($limits[$newSeatTypeID])) {

            $maxAllowed = $limits[$newSeatTypeID];

            // Tổng số ghế hiện tại của loại này trong phòng
            $currentCount = Seat::where('roomID', $roomID)
                ->where('seatTypeID', $newSeatTypeID)
                ->count();

            // Số ghế được chọn mà đã thuộc loại này
            $alreadySameTypeCount = Seat::whereIn('seatID', $selectedSeatIDs)
                ->where('seatTypeID', $newSeatTypeID)
                ->count();

            $increaseCount = count($selectedSeatIDs) - $alreadySameTypeCount;

            $finalCount = $currentCount + $increaseCount;

            if ($finalCount > $maxAllowed) {

                $seatTypeName = seatType::find($newSeatTypeID)?->seatTypeName
                    ?? 'loại ghế này';

                return redirect()
                    ->route('seat.index', ['roomID' => $roomID])
                    ->with(
                        'error',
                        "Số lượng ghế {$seatTypeName} vượt quá giới hạn cho phép ({$maxAllowed} ghế)."
                    );
            }
        }

        // Cập nhật loại ghế
        Seat::whereIn('seatID', $selectedSeatIDs)
            ->update([
                'seatTypeID' => $newSeatTypeID
            ]);

        return redirect()
            ->route('seat.index', ['roomID' => $roomID])
            ->with('success', 'Cập nhật thành công');
    }



    public function ajaxUpdateType(Request $request, int $id)
    {
        $request->validate([
            'seatTypeID' => 'required|integer|in:1,2,3,4',
        ]);

        $seat = Seat::findOrFail($id);

        // Chỉ cho phép đổi ghế thường (2) → VIP (1)
        // Ghế đôi (3) và bảo trì (4) không được đổi
        if (in_array($seat->seatTypeID, [3, 4])) {
            return response()->json([
                'success' => false,
                'message' => 'Ghế đôi hoặc bảo trì không thể thay đổi loại.',
            ], 422);
        }

        $seat->seatTypeID = $request->seatTypeID;
        $seat->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật loại ghế thành công.',
            'seat'    => $seat,
        ]);
    }

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

        // Đảm bảo 2 ghế cùng hàng (cùng rowSeat)
        if ($seatA->rowSeat !== $seatB->rowSeat) {
            return response()->json([
                'success' => false,
                'message' => 'Hai ghế phải cùng hàng mới có thể đổi vị trí.',
            ], 422);
        }

        // Dùng transaction để tránh trùng colSeat tạm thời
        DB::transaction(function () use ($seatA, $seatB, $request) {
            // Dùng colSeat = 0 làm giá trị tạm (tránh unique constraint nếu có)
            $seatA->colSeat = 0;
            $seatA->save();

            $seatB->colSeat = $request->colSeat_a; // B lấy vị trí của A
            $seatB->save();

            $seatA->colSeat = $request->colSeat_b; // A lấy vị trí của B
            $seatA->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Hoán đổi vị trí ghế thành công.',
            'seatA'   => $seatA->fresh(),
            'seatB'   => $seatB->fresh(),
        ]);
    }



    public function editMultiple(Request $request)
    {
        $ids = explode(',', $request->seatIDs);

        $seats = Seat::whereIn('seatID', $ids)->get();
        $seatTypes = SeatType::all();
        $rooms = ScreeningRoom::all();

        $roomID = $seats->first()?->roomID;

        return view('admins.manageCinema.seat.edit-multiple', compact(
            'seats',
            'seatTypes',
            'rooms',
            'roomID'
        ));
    }

    // ================= UPDATE TYPE =================
    public function updateType(Request $request)
    {
        $request->validate([
            'seatIDs' => 'required|array|min:1',
            'seatIDs.*' => 'exists:seats,seatID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID'
        ]);

        $firstSeat = Seat::whereIn('seatID', $request->seatIDs)->first();

        if (!$firstSeat) {
            return response()->json(['error' => 'Không tìm thấy ghế'], 404);
        }

        // check showtime
        if (ShowTime::where('roomID', $firstSeat->roomID)->exists()) {
            return response()->json([
                'error' => 'Phòng đang có suất chiếu không thể sửa'
            ], 400);
        }

        // chỉ update ghế chưa có vé
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
            'roomID' => $firstSeat->roomID
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

        return view('system.seatSelect', compact(
            'seats',
            'showtime',
            'bookedSeats'
        ));
    }
}
