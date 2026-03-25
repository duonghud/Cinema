<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\screeningRoom;
use App\Models\Admin\screenType;

class screeningRoomController extends Controller
{

    // READ - danh sách
    public function index()
    {
        $room = screeningRoom::all();
        $screenTypes = screenType::all();

        return view('admins.screeningRoom.index', compact('room', 'screenTypes'));
    }


    // FORM CREATE
    public function create()
    {
        $screenTypes = screenType::all();

        return view('admins.screeningRoom.create', compact('screenTypes'));
    }


    // CREATE
    public function store(Request $request)
    {
        $request->validate([
            'roomName' => 'required',
            'capacity' => 'required|integer',
            'screenTypeID' => 'required'
        ]);

        screeningRoom::create([
            'roomName' => $request->roomName,
            'capacity' => $request->capacity,
            'screenTypeID' => $request->screenTypeID
        ]);

        return redirect()->route('screeningRoom.index')
            ->with('success', 'Room added successfully');
    }



    // FORM EDIT
    public function edit(string $id)
    {
        $room = screeningRoom::findOrFail($id);
        $screenTypes = screenType::all();

        return view('admins.screeningRoom.edit', compact('room', 'screenTypes'));
    }


    // UPDATE
    public function update(Request $request, string $id)
    {
        $room = screeningRoom::findOrFail($id);

        $room->update([
            'roomName' => $request->roomName,
            'capacity' => $request->capacity,
            'screenTypeID' => $request->screenTypeID
        ]);

        return redirect()->route('screeningRoom.index')
            ->with('success', 'Room updated successfully');
    }


    // DELETE
    public function destroy(string $id)
    {
        screeningRoom::destroy($id);

        return redirect()->route('screeningRoom.index')
            ->with('success', 'Room deleted successfully');
    }
}
