<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\ShowTime;
use App\Models\Admin\Movie;
use App\Models\Admin\ScreeningRoom;

class ShowTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $showTimes = ShowTime::with(['movie', 'room'])->paginate(10);

        return view('admins.showtime.index', compact('showTimes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $movies = Movie::all();
        $rooms = ScreeningRoom::all();

        return view('admins.showtime.create', compact('movies', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'showDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required|after:startTime',
            'movieID' => 'required',
            'roomID' => 'required',
        ], [
            'showDate.required' => 'Vui lòng chọn ngày chiếu.',
            'startTime.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'endTime.required' => 'Vui lòng chọn thời gian kết thúc.',
            'endTime.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'movieID.required' => 'Vui lòng chọn phim.',
            'roomID.required' => 'Vui lòng chọn phòng.',
        ]);

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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $showTime = ShowTime::with(['movie', 'room'])->findOrFail($id);

        return view('admins.showtime.show', compact('showTime'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $showTime = ShowTime::findOrFail($id);
        $movies = Movie::all();
        $rooms = ScreeningRoom::all();

        return view('admins.showTime.edit', compact('showTime', 'movies', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $showTime = showTime::findOrFail($id);

        $request->validate([
            'showDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required',
            'movieID' => 'required|exists:movies,movieID',
            'roomID' => 'required|exists:screening_rooms,roomID',
        ]);

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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $showTime = showTime::findOrFail($id);
        $showTime->delete();

        return redirect()->route('showTime.index')
            ->with('success', 'Xóa thành công');
    }
}
