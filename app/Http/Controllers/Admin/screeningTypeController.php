<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\screeningRoom;
use App\Models\Admin\screenType;

class screeningTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $screenTypes = screenType::all();
        return view('admins.screenType.index', ['screenTypes' => $screenTypes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.screenType.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $screenTypes = new screenType();
        $screenTypes->name = $request->input('name');
        $screenTypes->save();

        return redirect()->route('screenType.index')->with('success', 'Tạo thành công');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $screenTypeID)
    {
        $screenTypes = screenType::findOrFail($screenTypeID);
        return view('admins.screenType.edit', ['screenTypes' => $screenTypes]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $screenTypeID)
    {
        $screenTypes = screenType::findOrFail($screenTypeID);

        $request->validate([
            'name' => 'required'
        ]);

        $screenTypes->name = $request->input('name');
        $screenTypes->save();

        return redirect()->route('screenType.index')->with('success', 'Sửa thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $screenTypes = screenType::findOrFail($id);
        $screenTypes->delete();

        return redirect()->route('screenType.index')->with('success', 'Xóa thành công');
    }
}
