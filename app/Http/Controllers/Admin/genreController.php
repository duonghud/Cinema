<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\genre;

class genreController extends Controller
{
    /**
     * Hiển thị danh sách thể loại phim (Hỗ trợ tìm kiếm từ khóa, lọc theo tên và phân trang).
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $genreName = trim((string) $request->input('genre_name'));

        $genres = genre::query()
            // Tìm kiếm gần đúng theo ID hoặc Tên thể loại dựa trên từ khóa nhập vào
            ->when($search, function ($query) use ($search) {
                $query->where('genreID', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            // Lọc chính xác theo tên thể loại được chọn từ danh sách dropdown
            ->when($genreName, function ($query) use ($genreName) {
                $query->where('name', $genreName);
            })
            ->paginate(5)
            ->withQueryString(); // Duy trì các tham số tìm kiếm/lọc trên URL khi chuyển trang

        // Lấy danh sách tên thể loại duy nhất (không trùng) để làm dữ liệu cho bộ lọc select
        $genreNames = genre::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name', 'name');

        return view('admins.manageMovies.genre.index', [
            'genres' => $genres,
            /*'filters' => [[
                'name' => 'genre_name',
                'all_label' => 'Tất cả thể loại',
                'options' => $genreNames->toArray(),
            ]],*/
        ]);
    }

    /**
     * Hiển thị form thêm mới thể loại phim.
     */
    public function create()
    {
        return view('admins.manageMovies.genre.create');
    }

    /**
     * Xác thực dữ liệu đầu vào và tiến hành lưu mới thể loại phim.
     */
    public function store(Request $request)
    {
        // Bắt buộc phải nhập tên thể loại
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
     * Hiển thị form chỉnh sửa thể loại phim theo ID (Tự động trả về 404 nếu không tìm thấy).
     */
    public function edit(string $genreID)
    {
        $genres = genre::findOrFail($genreID);
        return view('admins.manageMovies.genre.edit', ['genres' => $genres]);
    }

    /**
     * Cập nhật thông tin sửa đổi của thể loại phim vào cơ sở dữ liệu.
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
     * Xóa thể loại phim ra khỏi hệ thống.
     */
    public function destroy(string $id)
    {
        $genres = genre::findOrFail($id);
        $genres->delete();

        return redirect()->route('genre.index')->with('success', 'Xóa thành công');
    }
}