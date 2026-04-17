<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\ShowTime;
use App\Models\Admin\Movie;
use App\Models\Admin\ScreeningRoom;
use App\Http\Controllers\Admin\ticketController;
use Carbon\Carbon;

class ShowTimeController extends Controller
{
    // LIST
    public function index()
    {
        $showTimes = ShowTime::with(['movie', 'room'])->paginate(10);

        return view('admins.showtime.index', compact('showTimes'));
    }

    // FORM CREATE
    public function create()
    {
        $movies = Movie::all();
        $rooms = ScreeningRoom::all();

        return view('admins.showtime.create', compact('movies', 'rooms'));
    }

    // STORE + AUTO CREATE TICKET
    

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

        //Không cho chọn ngày cũ
        $today = Carbon::today();
        $showDate = Carbon::parse($request->showDate);

        if ($showDate->lt($today)) {
            return back()->withErrors([
                'showDate' => 'Không được chọn ngày cũ.'
            ])->withInput();
        }

        //Tạo suất chiếu trước ít nhất 1 ngày
        if ($showDate->lt($today->addDay())) {
            return back()->withErrors([
                'showDate' => 'Suất chiếu tạo trước ít nhất 1 ngày.'
            ])->withInput();
        }

        //Kiểm tra trùng giờ trong cùng phòng
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
                'startTime' => 'Suất chiếu bị trùng giờ trong cùng phòng.'
            ])->withInput();
        }

        // Tạo suất chiếu
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

    // SHOW
    public function show(string $id)
    {
        $showTime = ShowTime::with(['movie', 'room'])->findOrFail($id);

        return view('admins.showtime.show', compact('showTime'));
    }

    // FORM EDIT
    public function edit(string $id)
    {
        $showTime = ShowTime::findOrFail($id);
        $movies = Movie::all();
        $rooms = ScreeningRoom::all();

        return view('admins.showtime.edit', compact('showTime', 'movies', 'rooms'));
    }

    // UPDATE
    public function update(Request $request, string $id)
    {
        $showTime = ShowTime::findOrFail($id);

        $request->validate([
            'showDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required|after:startTime',
            'movieID' => 'required|exists:movies,movieID',
            'roomID' => 'required|exists:screening_rooms,roomID',
        ]);

        //Không cho chọn ngày cũ
        $today = Carbon::today();
        $showDate = Carbon::parse($request->showDate);

        if ($showDate->lt($today)) {
            return back()->withErrors([
                'showDate' => 'Không được chọn ngày cũ.'
            ])->withInput();
        }

        //Tạo suất chiếu trước ít nhất 1 ngày
        if ($showDate->lt($today->addDay())) {
            return back()->withErrors([
                'showDate' => 'Suất chiếu tạo trước ít nhất 1 ngày.'
            ])->withInput();
        }

        //Kiểm tra trùng giờ trong cùng phòng
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
                'startTime' => 'Suất chiếu bị trùng giờ trong cùng phòng.'
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

    // DELETE
    public function destroy(string $id)
    {
        $showTime = ShowTime::findOrFail($id); // 🔥 fix chữ hoa
        $showTime->delete();

        return redirect()->route('showTime.index')
            ->with('success', 'Xóa thành công');
    }
}
