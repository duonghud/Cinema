<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\screeningRoom;
use App\Models\Admin\screenType;

class screeningTypeController extends Controller
{
    /**
     * Hiển thị danh sách các loại định dạng màn hình (có tìm kiếm và phân trang).
     */
    public function index(Request $request)
    {
        // Lấy từ khóa tìm kiếm từ ô nhập liệu và cắt bỏ khoảng trắng thừa
        $search = trim((string) $request->input('search'));

        // Khởi tạo truy vấn Eloquent trên Model screenType
        $screenTypes = screenType::query()
            // Bộ lọc tìm kiếm: Chỉ kích hoạt khi biến $search có giá trị
            ->when($search, function ($query) use ($search) {
                // Tìm kiếm gần đúng (LIKE) theo ID hoặc theo tên loại màn hình
                $query->where('screenTypeID', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->paginate(5) // Phân trang: Mỗi trang hiển thị tối đa 5 bản ghi
            ->withQueryString(); // Giữ lại từ khóa tìm kiếm trên URL khi bấm chuyển trang

        // Trả về giao diện danh sách kèm theo dữ liệu loại màn hình đã lọc
        return view('admins.manageCinema.screenType.index', ['screenTypes' => $screenTypes]);
    }

    /**
     * Hiển thị form giao diện để thêm mới một định dạng màn hình.
     */
    public function create()
    {
        return view('admins.manageCinema.screenType.create');
    }

    /**
     * Tiếp nhận dữ liệu từ form và lưu mới định dạng màn hình vào Database.
     */
    public function store(Request $request)
    {
        // 1. Xác thực dữ liệu đầu vào (Validation) kèm thông báo lỗi tiếng Việt
        $validated = $request->validate([
            // Bắt buộc nhập, là chuỗi, tối đa 50 ký tự, không được trùng (unique) trong bảng screen_types
            'name' => 'required|string|max:50|unique:screen_types,name',
        ], [
            'name.required' => 'Tên định dạng không được để trống.',
            'name.max'      => 'Tên định dạng không quá 50 ký tự.',
            'name.unique'   => 'Tên định dạng đã tồn tại.',
        ]);

        // 2. Tạo mới bản ghi vào Database bằng Mass Assignment (dựa trên mảng $validated đã lọc sạch)
        ScreenType::create($validated);

        // 3. Chuyển hướng về trang danh sách kèm thông báo flash session thành công
        return redirect()->route('screenType.index')
            ->with('success', 'Định dạng màn hình mới đã được thêm.');
    }

    /**
     * Hiển thị chi tiết một định dạng màn hình cụ thể.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Hiển thị form giao diện chỉnh sửa định dạng màn hình.
     */
    public function edit(string $screenTypeID)
    {
        // Tìm loại màn hình theo ID, nếu không tồn tại hệ thống tự ném lỗi 404
        $screenTypes = screenType::findOrFail($screenTypeID);
        
        // Trả về view sửa và truyền dữ liệu của bản ghi vừa tìm được sang form
        return view('admins.manageCinema.screenType.edit', ['screenTypes' => $screenTypes]);
    }

    /**
     * Tiếp nhận dữ liệu thay đổi từ form và cập nhật vào Database.
     */
    public function update(Request $request, $id)
    {
        // Tìm bản ghi cần chỉnh sửa trong cơ sở dữ liệu, lỗi 404 nếu không thấy
        $screenTypes = ScreenType::findOrFail($id);

        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            // Quy tắc unique đặc biệt: Kiểm tra không trùng tên, ngoại trừ chính ID của bản ghi đang sửa này
            'name' => 'required|string|max:50|unique:screen_types,name,' . $screenTypes->screenTypeID . ',screenTypeID',
        ], [
            'name.required' => 'Tên định dạng không được để trống.',
            'name.max'      => 'Tên định dạng không quá 50 ký tự.',
            'name.unique'   => 'Tên định dạng đã tồn tại.',
        ]);

        // Tiến hành cập nhật dữ liệu mới vào bản ghi
        $screenTypes->update($validated);

        // Chuyển hướng về trang danh sách với thông báo cập nhật thành công
        return redirect()->route('screenType.index')
            ->with('success', 'Cập nhật định dạng màn hình thành công.');
    }

    /**
     * Xóa định dạng màn hình khỏi cơ sở dữ liệu.
     */
    public function destroy(string $id)
    {
        // Tìm bản ghi cần xóa theo ID, ném lỗi 404 nếu không tìm thấy
        $screenTypes = screenType::findOrFail($id);
        
        // Thực hiện xóa bản ghi
        $screenTypes->delete();

        // Quay lại trang danh sách kèm thông báo thành công
        return redirect()->route('screenType.index')->with('success', 'Xóa thành công');
    }
}