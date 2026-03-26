<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\studio;

class studioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studios = studio::all();
        return view('admins.manageMovies.studio.index', ['studios' => $studios]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.manageMovies.studio.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:studios,name'
        ],[
            'name.required' => 'Tên phòng chiếu không được để trống.',
            'name.unique' => 'Tên phòng chiếu đã tồn tại.'

        ]);

        $studio = new studio();
        $studio->name = $request->name;
        $studio->save();

        return redirect()->route('studio.index')->with('success', 'Tạo thành công.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $studioID)
    {
        $studio = studio::findOrFail($studioID);
        return view('admins.manageMovies.studio.edit', ['studio' => $studio]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $studioID)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $studio = studio::findOrFail($studioID);
        $studio->name = $request->name;
        $studio->save();

        return redirect()->route('studio.index')->with('success', 'Cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $studio = studio::findOrFail($id);
        $studio->delete();

        return redirect()->route('studio.index')->with('success', 'Xóa thành công.');
    }
}
