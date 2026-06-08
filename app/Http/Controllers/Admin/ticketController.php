<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Ticket;
use App\Models\Admin\ShowTime;
use App\Models\Admin\Seat;

class TicketController extends Controller
{

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $status = trim((string) $request->input('status'));
        $showTimeId = trim((string) $request->input('show_time_id'));
        $movieId = trim((string) $request->input('movie_id'));

        $tickets = Ticket::with([
            'showTime.movie',
            'showTime.room',
            'seat'
        ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ticketID', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%")
                        ->orWhereHas('seat', function ($seatQuery) use ($search) {
                            $seatQuery->where('seatID', 'like', "%{$search}%")
                                ->orWhere('rowSeat', 'like', "%{$search}%")
                                ->orWhere('colSeat', 'like', "%{$search}%")
                                ->orWhereRaw(
                                    "CONCAT(rowSeat, colSeat) LIKE ?",
                                    ["%{$search}%"]
                                );
                        })
                        ->orWhereHas('showTime', function ($showTimeQuery) use ($search) {
                            $showTimeQuery->where('showTimeID', 'like', "%{$search}%")
                                ->orWhere('showDate', 'like', "%{$search}%")
                                ->orWhereHas('movie', function ($movieQuery) use ($search) {
                                    $movieQuery->where('movieTitle', 'like', "%{$search}%");
                                })
                                ->orWhereHas('room', function ($roomQuery) use ($search) {
                                    $roomQuery->where('roomName', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($showTimeId, function ($query) use ($showTimeId) {
                $query->where('showTimeID', $showTimeId);
            })
            ->when($movieId, function ($query) use ($movieId) {
                $query->whereHas('showTime', function ($showTimeQuery) use ($movieId) {
                    $showTimeQuery->where('movieID', $movieId);
                });
            })
            ->orderByRaw("
            CASE
                WHEN status = 'booked' THEN 0
                ELSE 1
            END
        ")
            ->orderByDesc('ticketID')
            ->paginate(5)
            ->withQueryString();

        $showTimes = ShowTime::with('movie')
            ->orderByDesc('showDate')
            ->orderBy('startTime')
            ->get()
            ->mapWithKeys(function ($showTime) {
                $label = 'ST ' . $showTime->showTimeID;
                if ($showTime->movie?->movieTitle) {
                    $label .= ' - ' . $showTime->movie->movieTitle;
                }

                return [$showTime->showTimeID => $label];
            });

        $movies = ShowTime::with('movie')
            ->get()
            ->pluck('movie.movieTitle', 'movieID')
            ->filter()
            ->sort();

        return view('admins.ticket.index', [
            'tickets' => $tickets,
            'filters' => [
                [
                    'name' => 'status',
                    'all_label' => 'Tất cả trạng thái',
                    'options' => [
                        'available' => 'Còn trống',
                        'booked' => 'Đã thanh toán',
                    ],
                ],
                [
                    'name' => 'show_time_id',
                    'all_label' => 'Tất cả suất chiếu',
                    'options' => $showTimes->toArray(),
                ],
                [
                    'name' => 'movie_id',
                    'all_label' => 'Tất cả phim',
                    'options' => $movies->toArray(),
                ],
            ],
        ]);
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

    public function destroy($id)
    {
        Ticket::destroy($id);

        return redirect()->route('ticket.index')
            ->with('success', 'Xóa thành công');
    }

    public function generateTicketsByShowTime($showTimeId)
    {
        $showTime = ShowTime::with('room')->findOrFail($showTimeId);

        if (!$showTime->roomID) {
            return redirect()->route('ticket.index')
                ->with('error', 'Suất chiếu chưa được gán phòng chiếu.');
        }

        $seats = Seat::where('screeningRoomID', $showTime->roomID)->get();

        if ($seats->isEmpty()) {
            return redirect()->route('ticket.index')
                ->with('error', 'Phòng chiếu này chưa có ghế.');
        }

        $created = 0;

        foreach ($seats as $seat) {
            $exists = Ticket::where('showTimeID', $showTime->showTimeID)
                ->where('seatID', $seat->seatID)
                ->exists();

            if (!$exists) {
                Ticket::create([
                    'price'      => 50000,
                    'status'     => 'available',
                    'showTimeID' => $showTime->showTimeID,
                    'seatID'     => $seat->seatID,
                ]);

                $created++;
            }
        }

        if ($created === 0) {
            return redirect()->route('ticket.index')
                ->with('error', 'Tất cả vé cho suất chiếu này đã được tạo trước đó.');
        }

        return redirect()->route('ticket.index')
            ->with('success', "Đã tạo {$created} vé cho suất chiếu {$showTime->showTimeID}.");
    }
}
