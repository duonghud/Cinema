<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\genre;

class genreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = genre::all();
        return view('admins.genre.index', ['genres' => $genres]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.genre.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $genres = new genre();
        $genres->name = $request->input('name');
        $genres->save();

        return redirect()->route('genre.index')->with('success', 'Tạo thành công');
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
    public function edit(string $genreID)
    {
        $genres = genre::findOrFail($genreID);
        return view('admins.genre.edit', ['genres' => $genres]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $genreID)
    {
        $genres = genre::findOrFail($genreID);

        $request->validate([
            'name' => 'required'
        ]);

        $genres->name = $request->input('name');
        $genres->save();

        return redirect()->route('genre.index')->with('success', 'Sửa thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $genres = genre::findOrFail($id);
        $genres->delete();

        return redirect()->route('genre.index')->with('success', 'Xóa thành công');
    }
}
