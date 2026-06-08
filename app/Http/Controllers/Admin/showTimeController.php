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


    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $movieId = trim((string) $request->input('movie_id'));
        $roomId = trim((string) $request->input('room_id'));
        $showDate = trim((string) $request->input('show_date'));

        $showTimes = ShowTime::with(['movie', 'room'])
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
            ->when($movieId, function ($query) use ($movieId) {
                $query->where('movieID', $movieId);
            })
            ->when($roomId, function ($query) use ($roomId) {
                $query->where('roomID', $roomId);
            })
            ->when($showDate, function ($query) use ($showDate) {
                $query->whereDate('showDate', $showDate);
            })
            ->orderByDesc('showTimeID')
            ->paginate(5)
            ->withQueryString();

        $movies = Movie::query()->orderBy('movieTitle')->pluck('movieTitle', 'movieID');
        $rooms = ScreeningRoom::query()->orderBy('roomName')->pluck('roomName', 'roomID');
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

    public function create()
    {
        $movies = Movie::all();
        $rooms = ScreeningRoom::all();

        return view('admins.showtime.create', compact('movies', 'rooms'));
    }

    public function store(Request $request)
    {
        
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

    public function show(string $id)
    {
        $showTime = ShowTime::with(['movie', 'room'])->findOrFail($id);

        return view('admins.showtime.show', compact('showTime'));
    }

    public function edit(string $id)
    {
        $showTime = ShowTime::findOrFail($id);
        $movies = Movie::all();
        $rooms = ScreeningRoom::all();

        return view('admins.showtime.edit', compact('showTime', 'movies', 'rooms'));
    }

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

    public function destroy(string $id)
    {
        $showTime = ShowTime::findOrFail($id);
        $showTime->delete();

        return redirect()->route('showTime.index')
            ->with('success', 'Xóa thành công');
    }

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

        if ($ignoreShowTimeId !== null) {
            $query->where('showTimeID', '!=', $ignoreShowTimeId);
        }

        return $query->exists();
    }
}
