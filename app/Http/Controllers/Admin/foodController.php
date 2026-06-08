<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\food;
use GuzzleHttp\Promise\Create;

class foodController extends Controller
{
    /**
     * Hiển thị danh sách món ăn kèm bộ tìm kiếm từ khóa và hai bộ lọc độc lập (Loại đồ ăn & Kích cỡ).
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $foodType = trim((string) $request->input('food_type'));
        $size = trim((string) $request->input('size'));

        $foods = Food::query()
            // Tìm kiếm chuỗi gần đúng trên tất cả các trường thông tin của món ăn
            ->when($search, function ($query) use ($search) {
                $query->where('foodID', 'like', "%{$search}%")
                    ->orWhere('foodName', 'like', "%{$search}%")
                    ->orWhere('foodType', 'like', "%{$search}%")
                    ->orWhere('size', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%");
            })
            // Lọc chính xác theo loại món ăn (Ví dụ: Bắp rang, Nước ngọt)
            ->when($foodType, function ($query) use ($foodType) {
                $query->where('foodType', $foodType);
            })
            // Lọc chính xác theo kích cỡ (S, M, L)
            ->when($size, function ($query) use ($size) {
                $query->where('size', $size);
            })
            ->paginate(5)
            ->withQueryString(); // Giữ lại trạng thái từ khóa và bộ lọc khi thực hiện chuyển trang phân trang

        // Gộp nhóm và trích xuất danh sách các loại món ăn hiện có trong DB (loại bỏ giá trị rỗng/null) để làm menu lọc
        $foodTypes = Food::query()
            ->select('foodType')
            ->whereNotNull('foodType')
            ->where('foodType', '!=', '')
            ->distinct()
            ->orderBy('foodType')
            ->pluck('foodType', 'foodType');

        // Gộp nhóm và trích xuất danh sách các kích cỡ hiện có trong DB để làm menu lọc
        $sizes = Food::query()
            ->select('size')
            ->whereNotNull('size')
            ->where('size', '!=', '')
            ->distinct()
            ->orderBy('size')
            ->pluck('size', 'size');

        return view('admins.manageFoods.food.index', [
            'foods' => $foods,
            'filters' => [
                [
                    'name' => 'food_type',
                    'all_label' => 'Tất cả loại',
                    'options' => $foodTypes->toArray(),
                ],
                [
                    'name' => 'size',
                    'all_label' => 'Tất cả size',
                    'options' => $sizes->toArray(),
                ],
            ],
        ]);
    }

    /**
     * Hiển thị giao diện form thêm mới đồ ăn/thức uống.
     */
    public function create()
    {
        return view('admins.manageFoods.food.create');
    }

    /**
     * Kiểm tra ràng buộc dữ liệu đầu vào và tiến hành lưu bản ghi món ăn mới.
     */
    public function store(Request $request)
    {
        // Thực hiện xác thực (Giới hạn giá bán tối đa, ép kiểu size thuộc tập hợp cố định S, M, L)
        $validated = $request->validate([
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

        // Sử dụng mảng dữ liệu sạch đã qua kiểm tra để nạp an toàn vào DB (Mass Assignment)
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
     * Hiển thị giao diện cập nhật thông tin món ăn theo mã ID truyền vào.
     */
    public function edit(string $foodID)
    {
        $foods = food::findOrFail($foodID);
        return view('admins.manageFoods.food.edit', ['foods' => $foods]);
    }

    /**
     * Xác thực thông tin thay đổi và cập nhật trực tiếp vào đối tượng Model.
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
        ]);

        // Gán thủ công từng thuộc tính từ request đầu vào và tiến hành lưu đè
        $foods->foodName = $request->input('foodName');
        $foods->price = $request->input('price');
        $foods->foodType = $request->input('foodType');
        $foods->size = $request->input('size');
        $foods->save();

        return redirect()->route('food.index')->with('success', 'Sửa thành công');
    }

    /**
     * Xóa món ăn khỏi danh mục hệ thống.
     */
    public function destroy(string $id)
    {
        $foods = food::findOrFail($id);
        $foods->delete();

        return redirect()->route('food.index')->with('success', 'Xóa thành công');
    }
}