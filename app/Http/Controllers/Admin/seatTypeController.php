<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\seatType;
use App\Models\Admin\seat;
use Illuminate\Http\Request;
use App\Models\Admin\screenType;

class seatTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $seatTypes = seatType::all();
        return view('admins.manageCinema.seatType.index', ['seatTypes' => $seatTypes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.manageCinema.seatType.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'seatTypeName' => 'required|string|max:50|unique:seat_types,seatTypeName',
        ], [
            'seatTypeName.required' => 'Tên kiểu ghế không được để trống.',
            'seatTypeName.max' => 'Tên kiểu ghế không quá 50 ký tự.',
            'seatTypeName.unique' => 'Tên kiểu ghế đã tồn tại.',
        ]);

        SeatType::create($validated);

        return redirect()->route('seatType.index')
            ->with('success', 'Kiểu ghế mới đã được thêm.');
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
    public function edit(string $seatTypeID)
    {
        $seatTypes = seatType::findOrFail($seatTypeID);
        return view('admins.manageCinema.seatType.edit', ['seatTypes' => $seatTypes]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $seatTypes = SeatType::findOrFail($id);

        $validated = $request->validate([
            'seatTypeName' => 'required|string|max:50|unique:seat_types,seatTypeName,' . $seatTypes->seatTypeID . ',seatTypeID',
        ], [
            'seatTypeName.required' => 'Tên kiểu ghế không được để trống.',
            'seatTypeName.max' => 'Tên kiểu ghế không quá 50 ký tự.',
            'seatTypeName.unique' => 'Tên kiểu ghế đã tồn tại.',
        ]);

        $seatTypes->update($validated);

        return redirect()->route('seatType.index')
            ->with('success', 'Cập nhật kiểu ghế thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Kiểm tra có seat đang dùng không
        $isUsed = Seat::where('seatTypeID', $id)->exists();

        if ($isUsed) {
            return redirect()->back()->with('error', 'Không thể xóa! Loại ghế đang được sử dụng.');
        }

        SeatType::destroy($id);

        return redirect()->back()->with('success', 'Xóa thành công!');
    }
}
