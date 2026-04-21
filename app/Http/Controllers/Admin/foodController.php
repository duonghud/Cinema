<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\food;
use GuzzleHttp\Promise\Create;

class foodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $foods = Food::query()
            ->when($search, function ($query) use ($search) {
                $query->where('foodID', 'like', "%{$search}%")
                    ->orWhere('foodName', 'like', "%{$search}%")
                    ->orWhere('foodType', 'like', "%{$search}%")
                    ->orWhere('size', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%");
            })
            ->paginate(5)
            ->withQueryString();

        return view('admins.manageFoods.food.index', ['foods' => $foods]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.manageFoods.food.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated =$request->validate([
            'foodName' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:100000',
            'foodType' => 'required|string',
            'size' => 'required|in:S,M,L',
        ], [
            'foodName.required' => 'Tên món ăn không được để trống',
            'foodName.string' => 'Tên món ăn phải là chuỗi ký tự',
            'foodName.max' => 'Tên món ăn không được quá 255 ký tự',

            'price.required' => 'Giá món ăn không được để trống',
            'price.numeric' => 'Giá phải là số',
            'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
            'price.max' => 'Giá bán không được vượt quá 100000 nghìn',

            'foodType.required' => 'Loại món ăn không được để trống',
            'foodType.string' => 'Loại món ăn phải là chuỗi ký tự',
        ]);

        food::create($validated);   

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
        return view('admins.manageFoods.food.edit', ['foods' => $foods]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $foodID)
    {
        $foods = food::findOrFail($foodID);

        $request->validate([
            'foodName' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'foodType' => 'required|string',
            'size' => 'required|in:S,M,L',
        ], [
            'foodName.required' => 'Tên món ăn không được để trống',
            'price.required' => 'Giá món ăn không được để trống',
            'foodType.required' => 'Loại món ăn không được để trống',
            // Thêm các message khác nếu muốn
        ]);

        $foods->foodName = $request->input('foodName');
        $foods->price = $request->input('price');
        $foods->foodType = $request->input('foodType');
        $foods->size = $request->input('size');
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
