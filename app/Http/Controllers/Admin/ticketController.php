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

                        // Tìm theo ghế (A1, B5...)
                        ->orWhereHas('seat', function ($seatQuery) use ($search) {
                            $seatQuery->where('seatID', 'like', "%{$search}%")
                                ->orWhere('rowSeat', 'like', "%{$search}%")
                                ->orWhere('colSeat', 'like', "%{$search}%")
                                ->orWhereRaw(
                                    "CONCAT(rowSeat, colSeat) LIKE ?",
                                    ["%{$search}%"]
                                );
                        })

                        // Tìm theo suất chiếu
                        ->orWhereHas('showTime', function ($showTimeQuery) use ($search) {
                            $showTimeQuery->where('showTimeID', 'like', "%{$search}%")
                                ->orWhere('showDate', 'like', "%{$search}%")

                                // Tìm theo phim
                                ->orWhereHas('movie', function ($movieQuery) use ($search) {
                                    $movieQuery->where(
                                        'movieTitle',
                                        'like',
                                        "%{$search}%"
                                    );
                                })

                                // Tìm theo phòng
                                ->orWhereHas('room', function ($roomQuery) use ($search) {
                                    $roomQuery->where(
                                        'roomName',
                                        'like',
                                        "%{$search}%"
                                    );
                                });
                        });
                });
            })

            // Vé đã đặt hiển thị lên đầu
            ->orderByRaw("
            CASE
                WHEN status = 'booked' THEN 0
                ELSE 1
            END
        ")

            // Vé mới nhất hiển thị trước
            ->orderByDesc('ticketID')

            ->paginate(5)
            ->withQueryString();

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

    // Thay thế toàn bộ method generateTicketsByShowTime() bằng đoạn dưới đây.
    // Lỗi hiện tại: bạn đang dùng $seats = Seat::all();
    // nhưng hệ thống ghế thuộc từng phòng chiếu, nên cần lọc theo roomID của suất chiếu.

    public function generateTicketsByShowTime($showTimeId)
    {
        // Lấy suất chiếu kèm phòng chiếu
        $showTime = ShowTime::with('room')->findOrFail($showTimeId);

        // Kiểm tra suất chiếu có phòng hay không
        if (!$showTime->roomID) {
            return redirect()->route('ticket.index')
                ->with('error', 'Suất chiếu chưa được gán phòng chiếu.');
        }

        // Chỉ lấy ghế thuộc phòng của suất chiếu
        // Nếu cột trong bảng seats là screeningRoomID thì dùng dòng dưới:
        $seats = Seat::where('screeningRoomID', $showTime->roomID)->get();

        // Nếu model của bạn dùng cột khác (ví dụ roomID) thì đổi thành:
        // $seats = Seat::where('roomID', $showTime->roomID)->get();

        // Không có ghế nào trong phòng
        if ($seats->isEmpty()) {
            return redirect()->route('ticket.index')
                ->with('error', 'Phòng chiếu này chưa có ghế.');
        }

        $created = 0;

        foreach ($seats as $seat) {

            // Kiểm tra vé đã tồn tại chưa
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
