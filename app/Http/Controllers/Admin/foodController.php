<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\food;

class foodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $foods = Food::paginate(5);
        return view('admins.food.index', ['foods' => $foods]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.food.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'foodName' => 'required',
            'price' => 'required',
            'foodType' => 'required',
        ]);

        $foods = new food();
        $foods->foodName = request('foodName');
        $foods->price = request('price');
        $foods->foodType = request('foodType');
        $foods->save();

        return redirect()->route('food.index')->with('success', 'Tạo thành công');
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
    public function edit(string $foodID)
    {
        $foods = food::findOrFail($foodID);
        return view('admins.food.edit', ['foods' => $foods]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $foodID)
    {
        $foods = food::findOrFail($foodID);

        $request->validate([
            'foodName' => 'required',
            'price' => 'required',
            'foodType' => 'required',
        ]);

        $foods->foodName = $request->input('foodName');
        $foods->price = $request->input('price');
        $foods->foodType = $request->input('foodType');
        $foods->save();

        return redirect()->route('food.index')->with('success', 'Sửa thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $foods = food::findOrFail($id);
        $foods->delete();

        return redirect()->route('food.index')->with('success', 'Xóa thành công');
    }
}
