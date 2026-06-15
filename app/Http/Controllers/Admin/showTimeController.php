<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Movie;
use App\Models\Admin\ScreeningRoom;
use App\Models\Admin\ShowTime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShowTimeController extends Controller
{
    /**
     * Hiển thị danh sách suất chiếu kèm bộ lọc tìm kiếm nâng cao (Phim, Phòng, Ngày).
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $movieId = trim((string) $request->input('movie_id'));
        $roomId = trim((string) $request->input('room_id'));
        $showDate = trim((string) $request->input('show_date'));

        // Truy vấn dữ liệu suất chiếu kèm thông tin liên kết Movie và Room
        $showTimes = ShowTime::with(['movie', 'room'])
            // Tìm kiếm chung theo từ khóa nhập vào ô Search
            ->when($search, function ($query) use ($search) {
                $query->where('showTimeID', 'like', "%{$search}%")
                    ->orWhere('showDate', 'like', "%{$search}%")
                    ->orWhere('startTime', 'like', "%{$search}%")
                    ->orWhere('endTime', 'like', "%{$search}%")
                    ->orWhereHas('movie', function ($movieQuery) use ($search) {
                        $movieQuery->where('movieTitle', 'like', "%{$search}%");
                    })
                    ->orWhereHas('room', function ($roomQuery) use ($search) {
                        $roomQuery->where('roomName', 'like', "%{$search}%");
                    });
            })
            // Lọc chính xác theo Select Phim
            ->when($movieId, function ($query) use ($movieId) {
                $query->where('movieID', $movieId);
            })
            // Lọc chính xác theo Select Phòng
            ->when($roomId, function ($query) use ($roomId) {
                $query->where('roomID', $roomId);
            })
            // Lọc chính xác theo Select Ngày chiếu
            ->when($showDate, function ($query) use ($showDate) {
                $query->whereDate('showDate', $showDate);
            })
            ->orderByDesc('showTimeID')
            ->paginate(5)
            ->withQueryString();

        // Lấy danh sách phục vụ cho các ô Select Filter ngoài giao diện
        $movies = Movie::query()->orderBy('movieTitle')->pluck('movieTitle', 'movieID');
        $rooms = ScreeningRoom::query()->orderBy('roomName')->pluck('roomName', 'roomID');

        // Lấy danh sách các ngày chiếu duy nhất (distinct) và định dạng lại chuỗi d/m/Y
        $dates = ShowTime::query()
            ->select('showDate')
            ->distinct()
            ->orderBy('showDate')
            ->pluck('showDate', 'showDate')
            ->mapWithKeys(function ($date) {
                return [$date => Carbon::parse($date)->format('d/m/Y')];
            });

        return view('admins.showtime.index', [
            'showTimes' => $showTimes,
            'filters' => [
                [
                    'name' => 'movie_id',
                    'all_label' => 'Tất cả phim',
                    'options' => $movies->toArray(),
                ],
                [
                    'name' => 'room_id',
                    'all_label' => 'Tất cả phòng',
                    'options' => $rooms->toArray(),
                ],
                [
                    'name' => 'show_date',
                    'all_label' => 'Tất cả ngày',
                    'options' => $dates->toArray(),
                ],
            ],
        ]);
    }

    /**
     * Hiển thị form tạo mới suất chiếu.
     */
    public function create()
    {
        $movies = Movie::all();
        $rooms = ScreeningRoom::all();

        return view('admins.showtime.create', compact('movies', 'rooms'));
    }

    /**
     * Xác thực và lưu mới suất chiếu (Kiểm tra chặn ngày cũ và trùng lịch).
     */
    public function store(Request $request)
    {
        $request->validate([
            'showDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required|after:startTime', // Kết thúc phải sau bắt đầu
            'movieID' => 'required|exists:movies,movieID',
            'roomID' => 'required|exists:screening_rooms,roomID',
        ], [
            'showDate.required' => 'Vui lòng chọn ngày chiếu.',
            'startTime.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'endTime.required' => 'Vui lòng chọn thời gian kết thúc.',
            'endTime.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'movieID.required' => 'Vui lòng chọn phim.',
            'roomID.required' => 'Vui lòng chọn phòng.',
        ]);

        $today = Carbon::today();
        $showDate = Carbon::parse($request->showDate);

        // Chặn không cho tạo suất chiếu vào các ngày trong quá khứ
        if ($showDate->lt($today)) {
            return back()->withErrors([
                'showDate' => 'Không được chọn ngày cũ.',
            ])->withInput();
        }

        // Quy định ràng buộc: Suất chiếu phải được lên lịch trước ít nhất 1 ngày
        if ($showDate->lt($today->copy()->addDay())) {
            return back()->withErrors([
                'showDate' => 'Suất chiếu tạo trước ít nhất 1 ngày.',
            ])->withInput();
        }

        if ($showDate->gt($today->copy()->addDays(14))) {
            return back()->withErrors([
                'showDate' => 'Chỉ được tạo suất chiếu trong vòng 2 tuần (14 ngày) từ hôm nay.',
            ])->withInput();
        }

        // Kiểm tra xem khung giờ này trong phòng đã bị ai chiếm chỗ chưa
        $isConflict = $this->hasScheduleConflict(
            roomId: $request->roomID,
            showDate: $request->showDate,
            startTime: $request->startTime,
            endTime: $request->endTime
        );

        if ($isConflict) {
            return back()->withErrors([
                'startTime' => 'Suất chiếu bị trùng giờ trong cùng phòng.',
            ])->withInput();
        }

        ShowTime::create([
            'showDate' => $request->showDate,
            'startTime' => $request->startTime,
            'endTime' => $request->endTime,
            'movieID' => $request->movieID,
            'roomID' => $request->roomID,
        ]);

        return redirect()->route('showTime.index')
            ->with('success', 'Thêm suất chiếu thành công');
    }

    /**
     * Hiển thị chi tiết một suất chiếu.
     */
    public function show(string $id)
    {
        $showTime = ShowTime::with(['movie', 'room'])->findOrFail($id);

        return view('admins.showtime.show', compact('showTime'));
    }

    /**
     * Hiển thị form chỉnh sửa suất chiếu theo ID.
     */
    public function edit(string $id)
    {
        $showTime = ShowTime::findOrFail($id);
        $movies = Movie::all();
        $rooms = ScreeningRoom::all();

        return view('admins.showtime.edit', compact('showTime', 'movies', 'rooms'));
    }

    /**
     * Cập nhật thông tin suất chiếu (Xác thực lại và kiểm tra trùng lịch loại trừ bản ghi hiện tại).
     */
    public function update(Request $request, string $id)
    {
        $showTime = ShowTime::findOrFail($id);

        $request->validate([
            'showDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required|after:startTime',
            'movieID' => 'required|exists:movies,movieID',
            'roomID' => 'required|exists:screening_rooms,roomID',
        ], [
            'showDate.required' => 'Vui lòng chọn ngày chiếu.',
            'startTime.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'endTime.required' => 'Vui lòng chọn thời gian kết thúc.',
            'endTime.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'movieID.required' => 'Vui lòng chọn phim.',
            'roomID.required' => 'Vui lòng chọn phòng.',
        ]);

        $today = Carbon::today();
        $showDate = Carbon::parse($request->showDate);

        if ($showDate->lt($today)) {
            return back()->withErrors([
                'showDate' => 'Không được chọn ngày cũ.',
            ])->withInput();
        }

        if ($showDate->lt($today->copy()->addDay())) {
            return back()->withErrors([
                'showDate' => 'Suất chiếu tạo trước ít nhất 1 ngày.',
            ])->withInput();
        }

        if ($showDate->gt($today->copy()->addDays(14))) {
            return back()->withErrors([
                'showDate' => 'Chỉ được tạo suất chiếu trong vòng 2 tuần (14 ngày) từ hôm nay.',
            ])->withInput();
        }

        // Kiểm tra trùng lịch nhưng loại trừ chính ID suất chiếu đang sửa (ignoreShowTimeId)
        $isConflict = $this->hasScheduleConflict(
            roomId: $request->roomID,
            showDate: $request->showDate,
            startTime: $request->startTime,
            endTime: $request->endTime,
            ignoreShowTimeId: $showTime->showTimeID
        );

        if ($isConflict) {
            return back()->withErrors([
                'startTime' => 'Suất chiếu bị trùng giờ trong cùng phòng.',
            ])->withInput();
        }

        $showTime->update([
            'showDate' => $request->showDate,
            'startTime' => $request->startTime,
            'endTime' => $request->endTime,
            'movieID' => $request->movieID,
            'roomID' => $request->roomID,
        ]);

        return redirect()->route('showTime.index')
            ->with('success', 'Cập nhật thành công');
    }

    /**
     * Xóa suất chiếu khỏi hệ thống.
     */
    public function destroy(string $id)
    {
        $showTime = ShowTime::findOrFail($id);
        $showTime->delete();

        return redirect()->route('showTime.index')
            ->with('success', 'Xóa thành công');
    }

    /**
     * Hàm Helper: Kiểm tra xung đột thời gian của các suất chiếu trong cùng một phòng.
     * Thuật toán: (Bắt đầu A < Kết thúc B) VÀ (Kết thúc A > Bắt đầu B)
     */
    private function hasScheduleConflict(
        int|string $roomId,
        string $showDate,
        string $startTime,
        string $endTime,
        int|string|null $ignoreShowTimeId = null
    ): bool {
        $query = ShowTime::where('roomID', $roomId)
            ->where('showDate', $showDate)
            ->where('startTime', '<', $endTime)
            ->where('endTime', '>', $startTime);

        // Nếu đang trong chế độ Update, bỏ qua không xét trùng với chính nó
        if ($ignoreShowTimeId !== null) {
            $query->where('showTimeID', '!=', $ignoreShowTimeId);
        }

        return $query->exists(); // Trả về true nếu có bất kỳ xung đột nào
    }
}
