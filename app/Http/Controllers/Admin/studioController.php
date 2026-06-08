<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\studio;

class studioController extends Controller
{
    /**
     * Hiển thị danh sách nhà sản xuất (Có tìm kiếm, bộ lọc select và phân trang).
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $studioName = trim((string) $request->input('studio_name'));

        $studios = studio::query()
            // Tìm kiếm theo ID hoặc Tên nếu có từ khóa từ ô search
            ->when($search, function ($query) use ($search) {
                $query->where('studioID', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            // Lọc chính xác theo tên chọn từ dropdown quả bộ lọc
            ->when($studioName, function ($query) use ($studioName) {
                $query->where('name', $studioName);
            })
            ->paginate(5)
            ->withQueryString();

        // Lấy danh sách tên không trùng nhau (distinct) để hiển thị lên thẻ select filter
        $studioNames = studio::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name', 'name');

        return view('admins.manageMovies.studio.index', [
            'studios' => $studios,
//            'filters' => [[
//                'name' => 'studio_name',
//                'all_label' => 'Tất cả nhà sản xuất',
//                'options' => $studioNames->toArray(),
//            ]],
        ]);
    }

    /**
     * Hiển thị form tạo mới nhà sản xuất.
     */
    public function create()
    {
        return view('admins.manageMovies.studio.create');
    }

    /**
     * Xác thực dữ liệu và lưu mới nhà sản xuất.
     */
    public function store(Request $request)
    {
        // Kiểm tra bắt buộc nhập và không trùng tên trong bảng studios
        $request->validate([
            'name' => 'required|unique:studios,name'
        ], [
            'name.required' => 'Tên phòng chiếu không được để trống.', // Lưu ý: Thông báo lỗi đang ghi nhầm là "phòng chiếu" thay vì "nhà sản xuất"
            'name.unique' => 'Tên phòng chiếu đã tồn tại.'
        ]);

        $studio = new studio();
        $studio->name = $request->name;
        $studio->save();

        return redirect()->route('studio.index')->with('success', 'Tạo thành công.');
    }

    /**
     * Hiển thị form sửa thông tin nhà sản xuất theo ID.
     */
    public function edit(string $studioID)
    {
        $studio = studio::findOrFail($studioID);
        return view('admins.manageMovies.studio.edit', ['studio' => $studio]);
    }

    /**
     * Cập nhật thông tin nhà sản xuất.
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
     * Xóa nhà sản xuất khỏi hệ thống.
     */
    public function destroy(string $id)
    {
        $studio = studio::findOrFail($id);
        $studio->delete();

        return redirect()->route('studio.index')->with('success', 'Xóa thành công.');
    }
}