<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Ticket;
use App\Models\Admin\ShowTime;
use App\Models\Admin\Seat;

class ticketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['showTime', 'seat'])->get();
        return view('admins.ticket.index', compact('tickets'));
    }

    public function create()
    {
        $showTimes = ShowTime::all();
        $seats = Seat::all();

        return view('admins.ticket.create', compact('showTimes', 'seats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'price' => 'required|numeric',
            'status' => 'required',
            'showTimeID' => 'required|exists:show_times,showTimeID',
            'seatID' => 'required|exists:seats,seatID',
        ]);

        Ticket::create($request->all());

        return redirect()->route('ticket.index')
            ->with('success', 'Thêm vé thành công');
    }

    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        $showTimes = ShowTime::all();
        $seats = Seat::all();

        return view('admins.ticket.edit', compact('ticket', 'showTimes', 'seats'));
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'price' => 'required|numeric',
            'status' => 'required',
            'showTimeID' => 'required',
            'seatID' => 'required',
        ]);

        $ticket->update($request->all());

        return redirect()->route('ticket.index')
            ->with('success', 'Cập nhật vé thành công');
    }

    public function destroy($id)
    {
        Ticket::destroy($id);

        return redirect()->route('ticket.index')
            ->with('success', 'Xóa vé thành công');
    }
}