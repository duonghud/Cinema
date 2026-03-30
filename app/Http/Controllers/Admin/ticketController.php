<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Ticket;
use App\Models\Admin\ShowTime;
use App\Models\Admin\Seat;

class TicketController extends Controller
{
    // LIST
    public function index()
    {
        $tickets = Ticket::with(['showTime', 'seat'])->get();
        return view('admins.ticket.index', compact('tickets'));
    }

    // FORM CREATE
    public function create()
    {
        $showTimes = ShowTime::all();
        $seats = Seat::all();

        return view('admins.ticket.create', compact('showTimes', 'seats'));
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,booked',
            'showTimeID' => 'required|exists:show_times,showTimeID',
            'seatID' => 'required|exists:seats,seatID',
        ], [
            // PRICE
            'price.required' => 'Vui lòng nhập giá vé',
            'price.numeric' => 'Giá vé phải là số',
            'price.min' => 'Giá vé phải lớn hơn hoặc bằng 0',

            // STATUS
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',

            // SHOWTIME
            'showTimeID.required' => 'Vui lòng chọn suất chiếu',
            'showTimeID.exists' => 'Suất chiếu không tồn tại',

            // SEAT
            'seatID.required' => 'Vui lòng chọn ghế',
            'seatID.exists' => 'Ghế không tồn tại',
        ]);

        // Check trùng ghế
        $exists = Ticket::where('showTimeID', $request->showTimeID)
            ->where('seatID', $request->seatID)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'seatID' => 'Ghế này đã có vé trong suất chiếu này!'
            ])->withInput();
        }

        Ticket::create($request->only([
            'price',
            'status',
            'showTimeID',
            'seatID'
        ]));

        return redirect()->route('ticket.index')
            ->with('success', 'Thêm vé thành công');
    }

    // EDIT
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        $showTimes = ShowTime::all();
        $seats = Seat::all();

        return view('admins.ticket.edit', compact('ticket', 'showTimes', 'seats'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,booked',
            'showTimeID' => 'required|exists:show_times,showTimeID',
            'seatID' => 'required|exists:seats,seatID',
        ], [
            'price.required' => 'Vui lòng nhập giá vé',
            'price.numeric' => 'Giá vé phải là số',
            'price.min' => 'Giá vé phải lớn hơn hoặc bằng 0',

            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',

            'showTimeID.required' => 'Vui lòng chọn suất chiếu',
            'showTimeID.exists' => 'Suất chiếu không tồn tại',

            'seatID.required' => 'Vui lòng chọn ghế',
            'seatID.exists' => 'Ghế không tồn tại',
        ]);

        $exists = Ticket::where('showTimeID', $request->showTimeID)
            ->where('seatID', $request->seatID)
            ->where('ticketID', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'seatID' => 'Ghế này đã có vé trong suất chiếu này!'
            ])->withInput();
        }

        $ticket->update($request->only([
            'price',
            'status',
            'showTimeID',
            'seatID'
        ]));

        return redirect()->route('ticket.index')
            ->with('success', 'Cập nhật thành công');
    }

    // DELETE
    public function destroy($id)
    {
        Ticket::destroy($id);

        return redirect()->route('ticket.index')
            ->with('success', 'Xóa thành công');
    }

    public function generateTicketsByShowTime($showTimeId)
    {
        $showTime = ShowTime::findOrFail($showTimeId);

        // Lấy ghế theo phòng (nếu có roomID)
        $seats = Seat::all();

        foreach ($seats as $seat) {

            $exists = Ticket::where('showTimeID', $showTime->showTimeID)
                ->where('seatID', $seat->seatID)
                ->exists();

            if (!$exists) {

                // Giá mẫu (có thể custom)
                $price = 50000;

                Ticket::create([
                    'price' => $price,
                    'status' => 'available',
                    'showTimeID' => $showTime->showTimeID,
                    'seatID' => $seat->seatID,
                ]);
            }
        }

        return redirect()->route('ticket.index')
            ->with('success', 'Tạo vé tự động thành công!');
    }
}
