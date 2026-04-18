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
    private function validateShowTimeLeadTime(Request $request)
    {
        $showStartAt = Carbon::parse($request->showDate . ' ' . $request->startTime);
        $minimumStartAt = Carbon::now()->addDay();

        if ($showStartAt->lt($minimumStartAt)) {
            return back()->withErrors([
                'showDate' => 'Xuất chiếu phải được tạo cách thời điểm hiện tại ít nhất 1 ngày.',
            ])->withInput();
        }

        return null;
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

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
            ->paginate(5)
            ->withQueryString();

        return view('admins.showtime.index', compact('showTimes'));
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

        if ($leadTimeError = $this->validateShowTimeLeadTime($request)) {
            return $leadTimeError;
        }

        $isConflict = ShowTime::where('roomID', $request->roomID)
            ->where('showDate', $request->showDate)
            ->where(function ($query) use ($request) {
                $query->whereBetween('startTime', [$request->startTime, $request->endTime])
                    ->orWhereBetween('endTime', [$request->startTime, $request->endTime])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('startTime', '<=', $request->startTime)
                            ->where('endTime', '>=', $request->endTime);
                    });
            })
            ->exists();

        if ($isConflict) {
            return back()->withErrors([
                'startTime' => 'Xuất chiếu bị trùng giờ trong cùng phòng.',
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
            ->with('success', 'Thêm xuất chiếu thành công');
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

        if ($leadTimeError = $this->validateShowTimeLeadTime($request)) {
            return $leadTimeError;
        }

        $isConflict = ShowTime::where('roomID', $request->roomID)
            ->where('showDate', $request->showDate)
            ->where('showTimeID', '!=', $showTime->showTimeID)
            ->where(function ($query) use ($request) {
                $query->whereBetween('startTime', [$request->startTime, $request->endTime])
                    ->orWhereBetween('endTime', [$request->startTime, $request->endTime])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('startTime', '<=', $request->startTime)
                            ->where('endTime', '>=', $request->endTime);
                    });
            })
            ->exists();

        if ($isConflict) {
            return back()->withErrors([
                'startTime' => 'Xuất chiếu bị trùng giờ trong cùng phòng.',
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
}
