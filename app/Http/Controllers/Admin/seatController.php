<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Seat;
use App\Models\Admin\screeningRoom;
use App\Models\Admin\SeatType;
use App\Models\Admin\Ticket;

class SeatController extends Controller
{
    // LIST
    public function index()
    {
        $rooms = screeningRoom::all();
        $seatTypes = SeatType::all();

        $seats = Seat::with(['seatType', 'screeningRoom'])
            ->orderBy('rowSeat')
            ->orderBy('colSeat')
            ->get();

        return view('admins.manageCinema.seat.index', compact('seats', 'rooms', 'seatTypes'));
    }

    // FORM CREATE (tạo thủ công)
    public function create()
    {
        $rooms = screeningRoom::all();
        $seatTypes = SeatType::all();

        return view('admins.manageCinema.seat.create', compact('rooms', 'seatTypes'));
    }

    // // STORE (tạo 1 ghế)
    public function store(Request $request)
    {
        $request->validate([
            'rowSeat' => 'required',
            'colSeat' => 'required',
            'roomID' => 'required',
            'seatTypeID' => 'required'
        ]);

        Seat::create([
            'rowSeat' => $request->rowSeat,
            'colSeat' => $request->colSeat,
            'roomID' => $request->roomID,
            'seatTypeID' => $request->seatTypeID
        ]);

        return redirect()->route('seat.index')
            ->with('success', 'Tạo thêm ghế thành công');
    }

    // public function generate(Request $request)
    // {
    //     $request->validate([
    //         'roomID' => 'required|exists:screening_rooms,roomID'
    //     ]);

    //     $room = ScreeningRoom::findOrFail($request->roomID);

    //     // Kiểm tra vé đã tồn tại
    //     $hasTicket = Ticket::whereIn(
    //         'seatID',
    //         Seat::where('roomID', $room->roomID)->pluck('seatID')
    //     )->exists();

    //     if ($hasTicket) {
    //         return redirect()->back()->with('error', 'Phòng này đã có vé → không thể tạo lại ghế');
    //     }

    //     $capacity = $room->capacity;
    //     $seatsPerRow = 10;
    //     $rows = ceil($capacity / $seatsPerRow);

    //     $normal = SeatType::where('seatTypeName', 'normal')->first();
    //     $vip = SeatType::where('seatTypeName', 'vip')->first();
    //     $couple = SeatType::where('seatTypeName', 'couple')->first();

    //     if (!$normal) {
    //         return redirect()->back()->with('error', 'Chưa có seatType "normal" trong DB');
    //     }

    //     $count = 1;

    //     for ($i = 0; $i < $rows; $i++) {
    //         $row = chr(65 + $i);

    //         for ($j = 1; $j <= $seatsPerRow; $j++) {
    //             if ($count > $capacity) break;

    //             if ($i >= $rows - 2 && $vip) {
    //                 $typeID = $vip->seatTypeID;
    //             } elseif ($j >= 9 && $couple) {
    //                 $typeID = $couple->seatTypeID;
    //             } else {
    //                 $typeID = $normal->seatTypeID;
    //             }

    //             Seat::create([
    //                 'rowSeat' => $row,
    //                 'colSeat' => $j,
    //                 'roomID' => $room->roomID,
    //                 'seatTypeID' => $typeID
    //             ]);

    //             $count++;
    //         }
    //     }

    //     return redirect()->route('seat.index')->with('success', 'Tạo ghế tự động thành công 🔥');
    // }



    // EDIT FORM
    public function edit(string $id)
    {
        $seat = Seat::findOrFail($id);
        $rooms = screeningRoom::all();
        $seatTypes = SeatType::all();

        return view('admins.manageCinema.seat.edit', compact('seat', 'rooms', 'seatTypes'));
    }

    // UPDATE
    public function update(Request $request, string $id)
    {
        $seat = Seat::findOrFail($id);

        $seat->update([
            'rowSeat' => $request->rowSeat,
            'colSeat' => $request->colSeat,
            'roomID' => $request->roomID,
            'seatTypeID' => $request->seatTypeID
        ]);

        return redirect()->route('seat.index')
            ->with('success', 'Cập nhật ghế thành công');
    }

    // DELETE
    public function destroy(string $id)
    {
        Seat::destroy($id);

        return redirect()->route('seat.index')
            ->with('success', 'Xóa ghế thành công');
    }
}
