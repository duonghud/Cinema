<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin\Seat;
use App\Models\Admin\ScreeningRoom;
use App\Models\Admin\SeatType;
use App\Models\Admin\Ticket;
use App\Models\Admin\ShowTime;

class SeatController extends Controller
{

    // LIST
    public function index(Request $request)
    {
        $rooms = ScreeningRoom::all();
        $seatTypes = SeatType::all();
        $search = trim((string) $request->input('search'));

        $roomID = $request->roomID ?? $rooms->first()->roomID;

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

        return view(
            'admins.manageCinema.seat.index',
            compact('seats', 'rooms', 'seatTypes', 'roomID')
        );
    }


    // CREATE
    public function create()
    {
        $rooms = ScreeningRoom::all();
        $seatTypes = SeatType::all();

        return view(
            'admins.manageCinema.seat.create',
            compact('rooms', 'seatTypes')
        );
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

        $exists = Seat::where('roomID', $request->roomID)
            ->where('rowSeat', $request->rowSeat)
            ->where('colSeat', $request->colSeat)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ghế đã tồn tại');
        }

        Seat::create([
            'rowSeat' => $request->rowSeat,
            'colSeat' => $request->colSeat,
            'roomID' => $request->roomID,
            'seatTypeID' => $request->seatTypeID
        ]);

        return redirect()->route('seat.index')
            ->with('success', 'Tạo ghế thành công');
    }


    // EDIT
    public function edit(string $id)
    {
        $seat = Seat::findOrFail($id);
        $rooms = ScreeningRoom::all();
        $seatTypes = SeatType::all();

        return view(
            'admins.manageCinema.seat.edit',
            compact('seat', 'rooms', 'seatTypes')
        );
    }


    // UPDATE
    public function update(Request $request, string $id)
    {
        $request->validate([
            'rowSeat' => 'required',
            'colSeat' => 'required',
            'roomID' => 'required',
            'seatTypeID' => 'required'
        ]);

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
        $seat = Seat::findOrFail($id);

        $hasTicket = Ticket::where('seatID', $id)->exists();

        if ($hasTicket) {
            return back()->with('error', 'Ghế đã có vé');
        }

        $seat->delete();

        return redirect()->route('seat.index')
            ->with('success', 'Xóa ghế thành công');
    }


    // SELECT SEAT (CLIENT)
    public function selectSeat($showtimeID)
    {

        // lấy suất chiếu
        $showtime = ShowTime::findOrFail($showtimeID);

        // lấy ghế theo phòng
        $seats = Seat::where('roomID', $showtime->roomID)
            ->orderBy('rowSeat')
            ->orderBy('colSeat')
            ->get();

        // ghế đã đặt
        $bookedSeats = Ticket::where('showTimeID', $showtimeID)
            ->pluck('seatID')
            ->toArray();

        return view(
            'system.seatSelect',
            compact(
                'seats',
                'showtime',
                'bookedSeats'
            )
        );
    }
}
