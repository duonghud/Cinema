<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Seat;
use App\Models\Admin\screeningRoom;
use App\Models\Admin\SeatType;

class SeatController extends Controller
{

    // LIST
    public function index()
    {
        $seats = Seat::with(['screeningRoom','seatType'])->get();
        $rooms = screeningRoom::all();
        $seatTypes = SeatType::all();

        return view('admins.manageCinema.seat.index', compact('seats','rooms','seatTypes'));
    }

    // FORM CREATE
    public function create()
    {
        $rooms = screeningRoom::all();
        $seatTypes = SeatType::all();

        return view('admins.manageCinema.seat.create', compact('rooms','seatTypes'));
    }

    // STORE
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
            ->with('success','Tạo thêm ghế thành công');
    }

    // EDIT FORM
    public function edit(string $id)
    {
        $seat = Seat::findOrFail($id);
        $rooms = screeningRoom::all();
        $seatTypes = SeatType::all();

        return view('admins.manageCinema.seat.edit', compact('seat','rooms','seatTypes'));
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
         ->with('success','Cập nhập ghế thành công');

    }

    // DELETE
    public function destroy(string $id)
    {
        Seat::destroy($id);

        return redirect()->route('seat.index')
            ->with('success','Xóa ghế thành công');
    }

    
}