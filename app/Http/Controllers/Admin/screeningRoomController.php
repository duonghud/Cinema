<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Admin\screeningRoom;
use App\Models\Admin\screenType;
use App\Models\Admin\Seat;
use App\Models\Admin\seatType;
use App\Models\Admin\showTime;

class screeningRoomController extends Controller
{
    public function index(Request $request)
    {
        $search     = trim((string) $request->input('search'));
        $screenType = $request->input('screenType');

        $room = screeningRoom::with('screenType')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('roomID',   'like', "%{$search}%")
                      ->orWhere('roomName', 'like', "%{$search}%")
                      ->orWhere('capacity', 'like', "%{$search}%")
                      ->orWhereHas('screenType', function ($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->when($screenType, function ($query) use ($screenType) {
                $query->where('screenTypeID', $screenType);
            })
            ->paginate(5)
            ->withQueryString();

        $screenTypes = screenType::all();

        return view(
            'admins.manageCinema.screeningRoom.index',
            compact('room', 'screenTypes')
        );
    }

    public function create()
    {
        $screenTypes = screenType::all();
        $seatTypes   = seatType::all();

        $defaultVipTypeID    = $seatTypes->first(fn($s) => $this->isVip($s->seatTypeName))?->seatTypeID;
        $defaultNormalTypeID = $seatTypes->first(fn($s) => $this->isNormal($s->seatTypeName))?->seatTypeID;
        $defaultDoubleTypeID = $seatTypes->first(fn($s) => $this->isDouble($s->seatTypeName))?->seatTypeID;

        return view(
            'admins.manageCinema.screeningRoom.create',
            compact('screenTypes', 'seatTypes', 'defaultVipTypeID', 'defaultNormalTypeID', 'defaultDoubleTypeID')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'roomName'         => 'required|string|max:100|unique:screening_rooms,roomName',
            'rows'             => 'required|integer|min:1|max:26',
            'cols'             => 'required|integer|min:1|max:50',

            'vipSeats'         => 'required|integer|min:0',
            'normalSeats'      => 'required|integer|min:0',
            'doubleSeats'      => ['required', 'integer', 'min:0', function ($attr, $value, $fail) {
                if ((int) $value > 0 && (int) $value % 2 !== 0) {
                    $fail('Số ghế đôi phải là số chẵn (ghế đôi luôn đi theo cặp 2 chỗ).');
                }
            }],

            'vipSeatTypeID'    => 'required|exists:seat_types,seatTypeID',
            'normalSeatTypeID' => 'required|exists:seat_types,seatTypeID',
            'doubleSeatTypeID' => 'required|exists:seat_types,seatTypeID',

            'screenTypeID'     => 'required|exists:screen_types,screenTypeID',
        ], [
            'roomName.unique' => 'Tên phòng đã tồn tại',
            'cols.max'        => 'Số cột tối đa là 50',
            'rows.max'        => 'Số hàng tối đa là 26 (A-Z)',
        ]);

        $vipCount    = (int) $validated['vipSeats'];
        $normalCount = (int) $validated['normalSeats'];
        $doubleCount = (int) $validated['doubleSeats'];
        $totalSlots  = $vipCount + $normalCount + $doubleCount;
        $maxRows     = (int) $validated['rows'];
        $maxCols     = (int) $validated['cols'];

        if ($totalSlots <= 0) {
            return back()->withInput()
                ->withErrors(['capacity' => 'Phòng phải có ít nhất 1 ghế']);
        }

        if ($totalSlots > $maxRows * $maxCols) {
            return back()->withInput()
                ->withErrors(['vipSeats' => 'Tổng số ghế vượt quá số ô trong lưới (' . $totalSlots . '/' . ($maxRows * $maxCols) . ')']);
        }

        $room = ScreeningRoom::create([
            'roomName'     => $validated['roomName'],
            'capacity'     => $totalSlots,
            'screenTypeID' => $validated['screenTypeID'],
        ]);

        // Lưu cấu hình lưới vào Cache (persistent, không mất như session)
        Cache::forever("room_grid_{$room->roomID}", [
            'rows' => $maxRows,
            'cols' => $maxCols,
        ]);

        $seatQueue = [];
        for ($i = 0; $i < $vipCount;    $i++) $seatQueue[] = ['typeID' => (int) $validated['vipSeatTypeID']];
        for ($i = 0; $i < $normalCount; $i++) $seatQueue[] = ['typeID' => (int) $validated['normalSeatTypeID']];
        for ($i = 0; $i < $doubleCount; $i++) $seatQueue[] = ['typeID' => (int) $validated['doubleSeatTypeID']];

        $rows       = range('A', 'Z');
        $rowIndex   = 0;
        $currentCol = 1;
        $insertData = [];

        foreach ($seatQueue as $item) {
            if ($rowIndex >= $maxRows || $rowIndex >= count($rows)) break;

            $insertData[] = [
                'roomID'     => $room->roomID,
                'rowSeat'    => $rows[$rowIndex],
                'colSeat'    => $currentCol,
                'seatTypeID' => $item['typeID'],
            ];

            $currentCol++;
            if ($currentCol > $maxCols) {
                $currentCol = 1;
                $rowIndex++;
            }
        }

        if (!empty($insertData)) {
            Seat::insert($insertData);
        }

        return redirect()
            ->route('seat.index', ['roomID' => $room->roomID])
            ->with('success', 'Tạo phòng và ghế thành công');
    }

    public function edit(string $id)
    {
        $room        = screeningRoom::findOrFail($id);
        $seatTypes   = seatType::all();
        $screenTypes = screenType::all();

        $seats = Seat::where('roomID', $room->roomID)->get();

        $actualCols = $seats->max(fn($s) => (int) $s->colSeat) ?: 1;
        $actualRows = $seats->pluck('rowSeat')->unique()->count() ?: 1;

        $seatCounts = [
            'vipSeats'         => 0,
            'vipSeatTypeID'    => null,
            'normalSeats'      => 0,
            'normalSeatTypeID' => null,
            'doubleSeats'      => 0,
            'doubleSeatTypeID' => null,
        ];

        $grouped = $seats->groupBy('seatTypeID');

        foreach ($grouped as $typeID => $group) {
            $seatType = $seatTypes->firstWhere('seatTypeID', $typeID);
            if (!$seatType) continue;

            $name = mb_strtolower($seatType->seatTypeName, 'UTF-8');

            if ($this->isDouble($name)) {
                $seatCounts['doubleSeats']      = $group->count();
                $seatCounts['doubleSeatTypeID'] = $typeID;
            } elseif ($this->isVip($name)) {
                $seatCounts['vipSeats']      = $group->count();
                $seatCounts['vipSeatTypeID'] = $typeID;
            } else {
                $seatCounts['normalSeats']      = $group->count();
                $seatCounts['normalSeatTypeID'] = $typeID;
            }
        }

        // Đọc gridRows/gridCols từ Cache để pre-fill form edit
        $grid        = Cache::get("room_grid_{$room->roomID}");
        $actualRows  = max($actualRows, (int) ($grid['rows'] ?? 0));
        $actualCols  = max($actualCols, (int) ($grid['cols'] ?? 0));

        $defaultVipTypeID    = $seatCounts['vipSeatTypeID']
            ?? $seatTypes->first(fn($s) => $this->isVip($s->seatTypeName))?->seatTypeID;
        $defaultNormalTypeID = $seatCounts['normalSeatTypeID']
            ?? $seatTypes->first(fn($s) => $this->isNormal($s->seatTypeName))?->seatTypeID;
        $defaultDoubleTypeID = $seatCounts['doubleSeatTypeID']
            ?? $seatTypes->first(fn($s) => $this->isDouble($s->seatTypeName))?->seatTypeID;

        return view(
            'admins.manageCinema.screeningRoom.edit',
            compact(
                'room', 'seatTypes', 'screenTypes',
                'seatCounts', 'actualRows', 'actualCols',
                'defaultVipTypeID', 'defaultNormalTypeID', 'defaultDoubleTypeID'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $room = screeningRoom::findOrFail($id);

        $validated = $request->validate([
            'roomName'         => 'required|string|max:100',
            'rows'             => 'required|integer|min:1|max:26',
            'cols'             => 'required|integer|min:1|max:50',
            'screenTypeID'     => 'required|exists:screen_types,screenTypeID',
            'vipSeats'         => 'nullable|integer|min:0',
            'vipSeatTypeID'    => 'nullable|exists:seat_types,seatTypeID',
            'normalSeats'      => 'nullable|integer|min:0',
            'normalSeatTypeID' => 'nullable|exists:seat_types,seatTypeID',
            'doubleSeats'      => ['nullable', 'integer', 'min:0', function ($attr, $value, $fail) {
                if ((int) $value > 0 && (int) $value % 2 !== 0) {
                    $fail('Số ghế đôi phải là số chẵn (ghế đôi luôn đi theo cặp 2 chỗ).');
                }
            }],
            'doubleSeatTypeID' => 'nullable|exists:seat_types,seatTypeID',
        ], [
            'roomName.required'     => 'Tên phòng không được để trống.',
            'roomName.max'          => 'Tên phòng không được quá 100 ký tự.',
            'screenTypeID.required' => 'Vui lòng chọn loại phòng.',
            'screenTypeID.exists'   => 'Loại phòng không hợp lệ.',
            'rows.max'              => 'Số hàng tối đa là 26 (A-Z)',
            'cols.max'              => 'Số cột tối đa là 50',
        ]);

        $vipCount    = (int) ($validated['vipSeats']    ?? 0);
        $normalCount = (int) ($validated['normalSeats'] ?? 0);
        $doubleCount = (int) ($validated['doubleSeats'] ?? 0);
        $totalSlots  = $vipCount + $normalCount + $doubleCount;
        $maxRows     = (int) $validated['rows'];
        $maxCols     = (int) $validated['cols'];

        if ($totalSlots > $maxRows * $maxCols) {
            return back()->withInput()
                ->withErrors(['vipSeats' => 'Tổng số ghế vượt quá số ô trong lưới (' . $totalSlots . '/' . ($maxRows * $maxCols) . ')']);
        }

        $room->update([
            'roomName'     => $validated['roomName'],
            'screenTypeID' => $validated['screenTypeID'],
            'capacity'     => $totalSlots,
        ]);

        // Cập nhật cấu hình lưới vào Cache (ghi đè, tồn tại vĩnh viễn)
        Cache::forever("room_grid_{$room->roomID}", [
            'rows' => $maxRows,
            'cols' => $maxCols,
        ]);

        if ($totalSlots > 0) {
            Seat::where('roomID', $room->roomID)->delete();

            $seatQueue = [];
            for ($i = 0; $i < $vipCount;    $i++) $seatQueue[] = (int) ($validated['vipSeatTypeID']    ?? 0);
            for ($i = 0; $i < $normalCount; $i++) $seatQueue[] = (int) ($validated['normalSeatTypeID'] ?? 0);
            for ($i = 0; $i < $doubleCount; $i++) $seatQueue[] = (int) ($validated['doubleSeatTypeID'] ?? 0);

            $rows       = range('A', 'Z');
            $rowIndex   = 0;
            $currentCol = 1;
            $insertData = [];

            foreach ($seatQueue as $typeID) {
                if ($rowIndex >= $maxRows || $rowIndex >= count($rows)) break;

                $insertData[] = [
                    'roomID'     => $room->roomID,
                    'rowSeat'    => $rows[$rowIndex],
                    'colSeat'    => $currentCol,
                    'seatTypeID' => $typeID,
                ];

                $currentCol++;
                if ($currentCol > $maxCols) {
                    $currentCol = 1;
                    $rowIndex++;
                }
            }

            if (!empty($insertData)) {
                Seat::insert($insertData);
            }
        }

        return redirect()
            ->route('seat.index', ['roomID' => $room->roomID])
            ->with('success', 'Cập nhật phòng chiếu thành công.');
    }

    public function destroy(string $id)
    {
        $room = screeningRoom::findOrFail($id);

        if (showTime::where('roomID', $room->roomID)->exists()) {
            return redirect()
                ->route('screeningRoom.index')
                ->with('error', 'Phòng đang có suất chiếu, không thể xóa.');
        }

        Seat::where('roomID', $room->roomID)->delete();

        // Xoá cache lưới khi xoá phòng
        Cache::forget("room_grid_{$room->roomID}");

        $room->delete();

        return redirect()
            ->route('screeningRoom.index')
            ->with('success', 'Xóa phòng thành công.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: phân loại tên ghế
    |--------------------------------------------------------------------------
    */
    private function isDouble(string $name): bool
    {
        $name = mb_strtolower($name, 'UTF-8');
        return str_contains($name, 'đôi')
            || str_contains($name, 'double')
            || str_contains($name, 'couple');
    }

    private function isVip(string $name): bool
    {
        $name = mb_strtolower($name, 'UTF-8');
        return str_contains($name, 'vip');
    }

    private function isNormal(string $name): bool
    {
        return !$this->isDouble($name) && !$this->isVip($name);
    }
}