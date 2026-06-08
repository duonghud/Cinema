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
     * Hiển thị danh sách loại ghế.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $seatTypes = seatType::query()
            // Tìm kiếm theo ID hoặc Tên loại ghế nếu có từ khóa
            ->when($search, function ($query) use ($search) {
                $query->where('seatTypeID', 'like', "%{$search}%")
                    ->orWhere('seatTypeName', 'like', "%{$search}%");
            })
            ->paginate(5)
            ->withQueryString(); // Giữ lại tham số tìm kiếm trên URL khi chuyển trang

        return view('admins.manageCinema.seatType.index', ['seatTypes' => $seatTypes]);
    }

    /**
     * Hiển thị form tạo mới loại ghế.
     */
    public function create()
    {
        return view('admins.manageCinema.seatType.create');
    }

    /**
     * Xác thực dữ liệu và lưu mới loại ghế vào database.
     */
    public function store(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào và cấu hình thông báo lỗi tiếng Việt
        $validated = $request->validate([
            'seatTypeName' => 'required|string|max:50|unique:seat_types,seatTypeName',
            'price' => 'required|numeric|min:0|max:500000'
        ], [
            'seatTypeName.required' => 'Tên kiểu ghế không được để trống.',
            'seatTypeName.max' => 'Tên kiểu ghế không quá 50 ký tự.',
            'seatTypeName.unique' => 'Tên kiểu ghế đã tồn tại.',
            'price.required' => 'Giá không được để trống.',
            'price.numeric' => 'Giá phải là một số.',
            'price.min' => 'Giá không được nhỏ hơn 0.',
            'price.max' => 'Giá không được lớn hơn 500.000đ'
        ]);

        // Tạo nhanh bản ghi bằng Mass Assignment
        SeatType::create($validated);

        return redirect()->route('seatType.index')
            ->with('success', 'Kiểu ghế mới đã được thêm.');
    }

    /**
     * Hiển thị chi tiết.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Hiển thị form sửa loại ghế dựa trên ID.
     */
    public function edit(string $seatTypeID)
    {
        $seatTypes = seatType::findOrFail($seatTypeID); // Trả lỗi 404 nếu không tìm thấy
        return view('admins.manageCinema.seatType.edit', ['seatTypes' => $seatTypes]);
    }

    /**
     * Xác thực và cập nhật thông tin loại ghế.
     */
    public function update(Request $request, $id)
    {
        $seatTypes = SeatType::findOrFail($id);

        // Loại trừ ID hiện tại khi kiểm tra trùng tên (unique)
        $validated = $request->validate([
            'seatTypeName' => 'required|string|max:50|unique:seat_types,seatTypeName,' . $seatTypes->seatTypeID . ',seatTypeID',
            'price' => 'required|numeric|min:0|max:500000',
        ], [
            'seatTypeName.required' => 'Tên kiểu ghế không được để trống.',
            'seatTypeName.max' => 'Tên kiểu ghế không quá 50 ký tự.',
            'seatTypeName.unique' => 'Tên kiểu ghế đã tồn tại.',
            'price.required' => 'Giá không được để trống.',
            'price.numeric' => 'Giá phải là một số.',
            'price.min' => 'Giá không được nhỏ hơn 0.',
            'price.max' => 'Giá không được lớn hơn 500.000đ'
        ]);

        $seatTypes->update($validated);

        return redirect()->route('seatType.index')
            ->with('success', 'Cập nhật kiểu ghế thành công.');
    }

    /**
     * Xóa loại ghế.
     */
    public function destroy($id)
    {
        // Chặn xóa nếu có bất kỳ chiếc ghế nào trong hệ thống đang thuộc loại ghế này
        $isUsed = Seat::where('seatTypeID', $id)->exists();

        if ($isUsed) {
            return redirect()->back()->with('error', 'Không thể xóa! Loại ghế đang được sử dụng.');
        }

        // Tiến hành xóa khi điều kiện an toàn hợp lệ
        SeatType::destroy($id);

        return redirect()->back()->with('success', 'Xóa thành công!');
    }
}