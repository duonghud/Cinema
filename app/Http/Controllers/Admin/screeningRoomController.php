<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\screeningRoom;
use App\Models\Admin\screenType;
use App\Models\Admin\Seat;

class screeningRoomController extends Controller
{

    // READ - danh sách
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $room = screeningRoom::with('screenType')
            ->when($search, function ($query) use ($search) {
                $query->where('roomID', 'like', "%{$search}%")
                    ->orWhere('roomName', 'like', "%{$search}%")
                    ->orWhere('capacity', 'like', "%{$search}%")
                    ->orWhereHas('screenType', function ($screenTypeQuery) use ($search) {
                        $screenTypeQuery->where('name', 'like', "%{$search}%");
                    });
            })
            ->paginate(5)
            ->withQueryString();

        $screenTypes = screenType::all();

        return view('admins.manageCinema.screeningRoom.index', compact('room', 'screenTypes'));
    }


    // FORM CREATE
    public function create()
    {
        $screenTypes = screenType::all();

        return view('admins.manageCinema.screeningRoom.create', compact('screenTypes'));
    }


    // CREATE
    public function store(Request $request)
{
    $validated = $request->validate([
        'roomName' => 'required|string|max:100',
        'rows' => 'required|integer|min:1|max:20',
        'cols' => 'required|integer|min:1|max:20',
        'screenTypeID' => 'required|exists:screen_types,screenTypeID',
    ]);

    $capacity = $request->rows * $request->cols;

    $room = ScreeningRoom::create([
        'roomName' => $request->roomName,
        'capacity' => $capacity,
        'screenTypeID' => $request->screenTypeID,
    ]);

    $rows = range('A', chr(64 + $request->rows));

    foreach ($rows as $row) {
        for ($col = 1; $col <= $request->cols; $col++) {

            Seat::create([
                'roomID' => $room->roomID,
                'rowSeat' => $row,
                'colSeat' => $col,
                'seatTypeID' => 2
            ]);
        }
    }

    return redirect()->route('seat.index', ['roomID' => $room->roomID])
        ->with('success', 'Tạo phòng + ghế thành công');
}

    // FORM EDIT
    public function edit(string $id)
    {
        $room = screeningRoom::findOrFail($id);
        $screenTypes = screenType::all();

        return view('admins.manageCinema.screeningRoom.edit', compact('room', 'screenTypes'));
    }


    // UPDATE
    public function update(Request $request, $id)
    {
        $room = ScreeningRoom::findOrFail($id);

        $validated = $request->validate([
            'roomName' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
            'screenTypeID' => 'required|exists:screen_types,screenTypeID',
        ], [
            'roomName.required' => 'Tên phòng không được để trống.',
            'roomName.max' => 'Tên phòng không được quá 100 ký tự.',
            'capacity.required' => 'Sức chứa không được để trống.',
            'capacity.integer' => 'Sức chứa phải là số nguyên.',
            'capacity.min' => 'Sức chứa phải lớn hơn 0.',
            'screenTypeID.required' => 'Vui lòng chọn loại phòng.',
            'screenTypeID.exists' => 'Loại phòng không hợp lệ.',
        ]);

        $room->update($validated);

        return redirect()->route('screeningRoom.index')
            ->with('success', 'Cập nhật phòng chiếu thành công.');
    }


    // DELETE
    public function destroy(string $id)
    {
        screeningRoom::destroy($id);

        return redirect()->route('screeningRoom.index')
            ->with('success', 'Room deleted successfully');
    }
}
