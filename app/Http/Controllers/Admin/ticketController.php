<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Ticket;
use App\Models\Admin\ShowTime;
use App\Models\Admin\Seat;

class TicketController extends Controller
{
    /**
     * Hiển thị danh sách vé kèm bộ lọc nâng cao và sắp xếp ưu tiên vé đã đặt lên đầu.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $status = trim((string) $request->input('status'));
        $showTimeId = trim((string) $request->input('show_time_id'));
        $movieId = trim((string) $request->input('movie_id'));

        // Eager loading các quan hệ liên quan để tránh lỗi N+1 Query
        $tickets = Ticket::with([
            'showTime.movie',
            'showTime.room',
            'seat'
        ])
            // Tìm kiếm đa điều kiện (ID vé, trạng thái, giá, thông tin ghế, thông tin suất chiếu)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ticketID', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%")
                        // Tìm kiếm theo hàng ghế, cột ghế hoặc chuỗi kết hợp (ví dụ: A5)
                        ->orWhereHas('seat', function ($seatQuery) use ($search) {
                            $seatQuery->where('seatID', 'like', "%{$search}%")
                                ->orWhere('rowSeat', 'like', "%{$search}%")
                                ->orWhere('colSeat', 'like', "%{$search}%")
                                ->orWhereRaw(
                                    "CONCAT(rowSeat, colSeat) LIKE ?",
                                    ["%{$search}%"]
                                );
                        })
                        // Tìm kiếm theo thông tin phim hoặc phòng của suất chiếu đó
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
            // Bộ lọc: Trạng thái vé
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            // Bộ lọc: Suất chiếu cụ thể
            ->when($showTimeId, function ($query) use ($showTimeId) {
                $query->where('showTimeID', $showTimeId);
            })
            // Bộ lọc: Tất cả suất chiếu thuộc một bộ phim
            ->when($movieId, function ($query) use ($movieId) {
                $query->whereHas('showTime', function ($showTimeQuery) use ($movieId) {
                    $showTimeQuery->where('movieID', $movieId);
                });
            })
            // Ưu tiên đẩy trạng thái 'booked' lên đầu danh sách, sau đó mới đến 'available'
            ->orderByRaw("
            CASE
                WHEN status = 'booked' THEN 0
                ELSE 1
            END
        ")
            ->orderByDesc('ticketID')
            ->paginate(5)
            ->withQueryString();

        // Lấy danh sách suất chiếu làm tùy chọn cho thẻ select filter ngoài giao diện
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

        // Lấy danh sách phim đang có suất chiếu để làm bộ lọc dropdown
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

    /**
     * Hiển thị form tạo vé thủ công.
     */
    public function create()
    {
        $showTimes = ShowTime::all();
        $seats = Seat::all();

        return view('admins.ticket.create', compact('showTimes', 'seats'));
    }

    /**
     * Lưu vé mới được tạo thủ công (Có chặn trùng vị trí ghế trong cùng suất chiếu).
     */
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

        // Đảm bảo mỗi ghế chỉ có duy nhất 1 vé cho mỗi suất chiếu
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

    /**
     * Hiển thị form chỉnh sửa thông tin vé.
     */
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        $showTimes = ShowTime::all();
        $seats = Seat::all();

        return view('admins.ticket.edit', compact('ticket', 'showTimes', 'seats'));
    }

    /**
     * Cập nhật thông tin vé (Kiểm tra trùng ghế ngoại trừ chính chiếc vé đang sửa).
     */
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

        // Kiểm tra trùng vị trí nhưng bỏ qua ID của bản ghi hiện tại
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

    /**
     * Xóa vé.
     */
    public function destroy($id)
    {
        Ticket::destroy($id);

        return redirect()->route('ticket.index')
            ->with('success', 'Xóa thành công');
    }

    /**
     * Tự động tạo hàng loạt vé trống (Giá mặc định 50k) cho tất cả các ghế thuộc phòng chiếu của suất chiếu được chọn.
     */
    public function generateTicketsByShowTime($showTimeId)
    {
        $showTime = ShowTime::with('room')->findOrFail($showTimeId);

        if (!$showTime->roomID) {
            return redirect()->route('ticket.index')
                ->with('error', 'Suất chiếu chưa được gán phòng chiếu.');
        }

        // Lấy toàn bộ danh sách ghế thuộc cấu hình của phòng chiếu đó
        $seats = Seat::where('screeningRoomID', $showTime->roomID)->get();

        if ($seats->isEmpty()) {
            return redirect()->route('ticket.index')
                ->with('error', 'Phòng chiếu này chưa có ghế.');
        }

        $created = 0;

        // Vòng lặp kiểm tra và chèn vé trống vào database nếu chưa tồn tại
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