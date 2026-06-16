<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\ageRating;

class ageRatingController extends Controller
{
    /**
     * Hiển thị danh sách nhãn độ tuổi kèm bộ lọc mã phân loại và tìm kiếm từ khóa.
     */
    public function index(Request $request)
    {
        // Thu thập và làm sạch dữ liệu đầu vào từ bộ lọc request
        $search = trim((string) $request->input('search'));
        $code = trim((string) $request->input('code'));

        $ageRatings = ageRating::query()
            // Tìm kiếm chuỗi gần đúng trên mã nhãn hoặc đoạn mô tả chi tiết
            ->when($search, function ($query) use ($search) {
                $query->where('ageRatingID', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            // Lọc chính xác theo mã phân loại độ tuổi được chọn từ dropdown
            ->when($code, function ($query) use ($code) {
                $query->where('code', $code);
            })
            ->paginate(5)
            ->withQueryString(); // Duy trì trạng thái từ khóa và bộ lọc khi thực hiện chuyển trang phân trang

        // Trích xuất danh sách các mã code độc nhất (distinct) hiện có trong hệ thống để làm menu lọc
        $codes = ageRating::query()
            ->select('code')
            ->distinct()
            ->orderBy('code')
            ->pluck('code', 'code');

        return view('admins.manageMovies.ageRating.index', [
            'ageRatings' => $ageRatings,
            /*'filters' => [[
                'name' => 'code',
                'all_label' => 'Tất cả độ tuổi',
                'options' => $codes->toArray(),
            ]],*/
        ]);
    }

    /**
     * Hiển thị giao diện form thêm mới nhãn phân loại độ tuổi.
     */
    public function create()
    {
        return view('admins.manageMovies.ageRating.create');
    }

    /**
     * Kiểm tra ràng buộc dữ liệu đầu vào và lưu bản ghi giới hạn độ tuổi mới.
     */
    public function store(Request $request)
    {
        // Xác thực bắt buộc phải điền đầy đủ mã code và mô tả
        $request->validate([
            'code' => 'required',
            'description' => 'required'
        ]);

        // Tạo instance mới và gán thủ công từng thuộc tính để lưu vào DB
        $ageRating = new ageRating();
        $ageRating->code = $request->code;
        $ageRating->description = $request->description;
        $ageRating->save();

        return redirect()->route('ageRating.index')
            ->with('success', 'Tạo thành công');
    }

    /**
     * Hiển thị giao diện form chỉnh sửa thông tin nhãn độ tuổi theo ID.
     */
    public function edit(string $id)
    {
        // Tự động ném ra lỗi 404 nếu không tìm thấy ID nhãn độ tuổi tương ứng
        $ageRating = ageRating::findOrFail($id);

        return view('admins.manageMovies.ageRating.edit', compact('ageRating'));
    }

    /**
     * Xác thực thông tin thay đổi và cập nhật lại bản ghi nhãn độ tuổi.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required',
            'description' => 'required'
        ]);

        $ageRating = ageRating::findOrFail($id);

        // Tiến hành cập nhật đè dữ liệu mới từ request gửi lên
        $ageRating->code = $request->code;
        $ageRating->description = $request->description;
        $ageRating->save();

        return redirect()->route('ageRating.index')
            ->with('success', 'Cập nhật thành công');
    }

    /**
     * Xóa nhãn phân loại độ tuổi khỏi danh mục hệ thống.
     */
    public function destroy(string $id)
    {
        $ageRating = ageRating::findOrFail($id);
        $ageRating->delete();

        return redirect()->route('ageRating.index')
            ->with('success', 'Xóa thành công');
    }
}