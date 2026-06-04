<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

        return view(
            'admins.manageCinema.screeningRoom.create',
            compact('screenTypes', 'seatTypes')
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

        /*
        |----------------------------------------------------------------------
        | TÍNH TỔNG GHẾ
        | Ghế đôi = 1 record = 1 ô lưới = 1 chỗ ngồi
        |----------------------------------------------------------------------
        */
        $vipCount    = (int) $validated['vipSeats'];
        $normalCount = (int) $validated['normalSeats'];
        $doubleCount = (int) $validated['doubleSeats'];

        // Tổng ô ghế = tổng chỗ ngồi (ghế đôi chỉ chiếm 1 ô)
        $totalSlots = $vipCount + $normalCount + $doubleCount;
        $capacity   = $totalSlots;

        if ($capacity <= 0) {
            return back()
                ->withInput()
                ->withErrors(['capacity' => 'Phòng phải có ít nhất 1 ghế']);
        }

        $maxRows = (int) $validated['rows'];
        $maxCols = (int) $validated['cols'];

        if ($totalSlots > $maxRows * $maxCols) {
            return back()
                ->withInput()
                ->withErrors(['vipSeats' => 'Tổng số ghế vượt quá số ô trong lưới (' . $totalSlots . '/' . ($maxRows * $maxCols) . ')']);
        }

        /*
        |----------------------------------------------------------------------
        | TẠO PHÒNG
        |----------------------------------------------------------------------
        */
        $room = ScreeningRoom::create([
            'roomName'     => $validated['roomName'],
            'capacity'     => $capacity,
            'screenTypeID' => $validated['screenTypeID'],
        ]);

        /*
        |----------------------------------------------------------------------
        | BUILD DANH SÁCH GHẾ
        | Mỗi ghế (kể cả ghế đôi) = 1 record, chiếm 1 ô lưới
        |----------------------------------------------------------------------
        */
        $seatQueue = [];

        for ($i = 0; $i < $vipCount; $i++) {
            $seatQueue[] = ['typeID' => (int) $validated['vipSeatTypeID']];
        }
        for ($i = 0; $i < $normalCount; $i++) {
            $seatQueue[] = ['typeID' => (int) $validated['normalSeatTypeID']];
        }
        for ($i = 0; $i < $doubleCount; $i++) {
            $seatQueue[] = ['typeID' => (int) $validated['doubleSeatTypeID']];
        }

        $rows       = range('A', 'Z');
        $rowIndex   = 0;
        $currentCol = 1;
        $insertData = [];

        foreach ($seatQueue as $item) {
            if ($rowIndex >= $maxRows || $rowIndex >= count($rows)) {
                break;
            }

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

        /*
        |----------------------------------------------------------------------
        | INSERT DATABASE
        |----------------------------------------------------------------------
        */
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

        $seatCounts = [
            'vipSeats'         => 0,
            'vipSeatTypeID'    => null,
            'normalSeats'      => 0,
            'normalSeatTypeID' => null,
            'doubleSeats'      => 0,
            'doubleSeatTypeID' => null,
        ];

        $seats = Seat::where('roomID', $room->roomID)
            ->selectRaw('seatTypeID, COUNT(*) as total')
            ->groupBy('seatTypeID')
            ->get();

        foreach ($seats as $seat) {
            $seatType = seatType::find($seat->seatTypeID);
            if (!$seatType) continue;

            $name = strtolower($seatType->seatTypeName);

            if (str_contains($name, 'vip')) {
                $seatCounts['vipSeats']      = $seat->total;
                $seatCounts['vipSeatTypeID'] = $seat->seatTypeID;
            } elseif (str_contains($name, 'đôi') || str_contains($name, 'double') || str_contains($name, 'couple')) {
                // Ghế đôi = 1 record = 1 chỗ, không chia 2 nữa
                $seatCounts['doubleSeats']      = (int) $seat->total;
                $seatCounts['doubleSeatTypeID'] = $seat->seatTypeID;
            } else {
                $seatCounts['normalSeats']      = $seat->total;
                $seatCounts['normalSeatTypeID'] = $seat->seatTypeID;
            }
        }

        return view(
            'admins.manageCinema.screeningRoom.edit',
            compact('room', 'seatTypes', 'screenTypes', 'seatCounts')
        );
    }

    public function update(Request $request, $id)
    {
        $room = screeningRoom::findOrFail($id);

        $validated = $request->validate([
            'roomName'         => 'required|string|max:100',
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
            'roomName.required'    => 'Tên phòng không được để trống.',
            'roomName.max'         => 'Tên phòng không được quá 100 ký tự.',
            'screenTypeID.required'=> 'Vui lòng chọn loại phòng.',
            'screenTypeID.exists'  => 'Loại phòng không hợp lệ.',
        ]);

        // Ghế đôi = 1 chỗ, cộng thẳng
        $capacity = ($request->vipSeats ?? 0)
                  + ($request->normalSeats ?? 0)
                  + ($request->doubleSeats ?? 0);

        $room->update([
            'roomName'     => $validated['roomName'],
            'screenTypeID' => $validated['screenTypeID'],
            'capacity'     => $capacity,
        ]);

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
        $room->delete();

        return redirect()
            ->route('screeningRoom.index')
            ->with('success', 'Xóa phòng thành công.');
    }
}