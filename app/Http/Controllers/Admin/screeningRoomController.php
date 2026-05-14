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
        $search = trim((string) $request->input('search'));

        $room = screeningRoom::with('screenType')

            ->when($search, function ($query) use ($search) {

                $query->where('roomID', 'like', "%{$search}%")
                    ->orWhere('roomName', 'like', "%{$search}%")
                    ->orWhere('capacity', 'like', "%{$search}%")

                    ->orWhereHas('screenType', function ($screenTypeQuery) use ($search) {

                        $screenTypeQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
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

        $seatTypes = seatType::all();

        return view(
            'admins.manageCinema.screeningRoom.create',
            compact(
                'screenTypes',
                'seatTypes'
            )
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([

            'roomName' => 'required|string|max:100',

            'vipSeats' => 'required|integer|min:0',
            'normalSeats' => 'required|integer|min:0',
            'doubleSeats' => 'required|integer|min:0',

            'vipSeatTypeID' => 'required|exists:seat_types,seatTypeID',
            'normalSeatTypeID' => 'required|exists:seat_types,seatTypeID',
            'doubleSeatTypeID' => 'required|exists:seat_types,seatTypeID',

            'screenTypeID' => 'required|exists:screen_types,screenTypeID',
        ]);



        $capacity =
            $request->vipSeats +
            $request->normalSeats +
            $request->doubleSeats;



        $room = screeningRoom::create([

            'roomName' => $request->roomName,

            'capacity' => $capacity,

            'screenTypeID' => $request->screenTypeID,
        ]);


        $maxCols = 14;

        $rows = range('A', 'Z');

        $rowIndex = 0;

        $currentCol = 1;

        $createSeats = function (
            $totalSeats,
            $seatTypeID
        ) use (
            &$rowIndex,
            &$currentCol,
            $rows,
            $maxCols,
            $room
        ) {

            for ($i = 0; $i < $totalSeats; $i++) {

                Seat::create([

                    'roomID' => $room->roomID,

                    'rowSeat' => $rows[$rowIndex],

                    'colSeat' => $currentCol,

                    'seatTypeID' => $seatTypeID,
                ]);

                $currentCol++;

                if ($currentCol > $maxCols) {

                    $currentCol = 1;

                    $rowIndex++;
                }
            }
        };


        $createSeats(
            $request->vipSeats,
            $request->vipSeatTypeID
        );

        $createSeats(
            $request->normalSeats,
            $request->normalSeatTypeID
        );


        $createSeats(
            $request->doubleSeats,
            $request->doubleSeatTypeID
        );

        return redirect()->route('seat.index', [

            'roomID' => $room->roomID

        ])->with(
            'success',
            'Tạo phòng + ghế thành công'
        );
    }


    public function edit(string $id)
    {
        $room = screeningRoom::findOrFail($id);
        $seatTypes = seatType::all();
        $screenTypes = screenType::all();

        $seatCounts = [
            'vipSeats' => 0,
            'vipSeatTypeID' => null,
            'normalSeats' => 0,
            'normalSeatTypeID' => null,
            'doubleSeats' => 0,
            'doubleSeatTypeID' => null,
        ];

        $seats = Seat::where('roomID', $room->roomID)
            ->selectRaw('seatTypeID, COUNT(*) as total')
            ->groupBy('seatTypeID')
            ->get();

        // Gán dữ liệu vào mảng seatCounts
        foreach ($seats as $seat) {
            $seatType = seatType::find($seat->seatTypeID);

            if (!$seatType) {
                continue;
            }

            $name = strtolower($seatType->seatTypeName);

            if (str_contains($name, 'vip')) {
                $seatCounts['vipSeats'] = $seat->total;
                $seatCounts['vipSeatTypeID'] = $seat->seatTypeID;
            } elseif (str_contains($name, 'đôi') || str_contains($name, 'double') || str_contains($name, 'couple')) {
                $seatCounts['doubleSeats'] = $seat->total;
                $seatCounts['doubleSeatTypeID'] = $seat->seatTypeID;
            } else {
                $seatCounts['normalSeats'] = $seat->total;
                $seatCounts['normalSeatTypeID'] = $seat->seatTypeID;
            }
        }

        return view(
            'admins.manageCinema.screeningRoom.edit',
            compact(
                'room',
                'seatTypes',
                'screenTypes',
                'seatCounts'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $room = screeningRoom::findOrFail($id);

        $validated = $request->validate([
            'roomName' => 'required|string|max:100',
            'screenTypeID' => 'required|exists:screen_types,screenTypeID',

            'vipSeats' => 'nullable|integer|min:0',
            'vipSeatTypeID' => 'nullable|exists:seat_types,seatTypeID',

            'normalSeats' => 'nullable|integer|min:0',
            'normalSeatTypeID' => 'nullable|exists:seat_types,seatTypeID',

            'doubleSeats' => 'nullable|integer|min:0',
            'doubleSeatTypeID' => 'nullable|exists:seat_types,seatTypeID',
        ], [
            'roomName.required' => 'Tên phòng không được để trống.',
            'roomName.max' => 'Tên phòng không được quá 100 ký tự.',

            'screenTypeID.required' => 'Vui lòng chọn loại phòng.',
            'screenTypeID.exists' => 'Loại phòng không hợp lệ.',
        ]);
        $capacity =
            ($request->vipSeats ?? 0) +
            ($request->normalSeats ?? 0) +
            ($request->doubleSeats ?? 0);

        $room->update([
            'roomName' => $validated['roomName'],
            'screenTypeID' => $validated['screenTypeID'],
            'capacity' => $capacity,
        ]);

        return redirect()
            ->route('seat.index', [
                'roomID' => $room->roomID
            ])
            ->with(
                'success',
                'Cập nhật phòng chiếu thành công.'
            );
    }
    public function destroy(string $id)
    {
        $room = screeningRoom::findOrFail($id);

        if (showTime::where('roomID', $room->roomID)->exists()) {
            return redirect()
                ->route('screeningRoom.index')
                ->with('error', 'Phòng đang có suất chiếu, không thể xóa.');
        }

        // Xóa toàn bộ ghế của phòng
        Seat::where('roomID', $room->roomID)->delete();

        // Xóa phòng
        $room->delete();

        return redirect()
            ->route('screeningRoom.index')
            ->with('success', 'Xóa phòng thành công.');
    }
}
