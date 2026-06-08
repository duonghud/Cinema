<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ageRating;
use App\Models\Admin\genre;
use App\Models\Admin\movie;
use App\Models\Admin\studio;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class movieController extends Controller
{
    /**
     * Hiển thị danh sách phim kèm thanh tìm kiếm tổng hợp và bộ lọc phân loại theo Thể loại.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $genreId = trim((string) $request->input('genre_id'));

        // Eager loading các thực thể liên quan (Giới hạn độ tuổi, Nhà sản xuất, Thể loại)
        $movies = movie::with(['ageRating', 'studio', 'genres'])
            // Tìm kiếm đa trường: thông tin phim hoặc thông tin từ các bảng liên kết
            ->when($search, function ($query) use ($search) {
                $query->where('movieID', 'like', "%{$search}%")
                    ->orWhere('movieTitle', 'like', "%{$search}%")
                    ->orWhere('duration', 'like', "%{$search}%")
                    ->orWhere('director', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('releaseDate', 'like', "%{$search}%")
                    ->orWhereHas('ageRating', function ($ageQuery) use ($search) {
                        $ageQuery->where('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('studio', function ($studioQuery) use ($search) {
                        $studioQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('genres', function ($genreQuery) use ($search) {
                        $genreQuery->where('name', 'like', "%{$search}%");
                    });
            })
            // Lọc chính xác danh sách phim theo Thể loại được chọn
            ->when($genreId, function ($query) use ($genreId) {
                $query->whereHas('genres', function ($genreQuery) use ($genreId) {
                    $genreQuery->where('genres.genreID', $genreId);
                });
            })
            ->paginate(5)
            ->withQueryString();

        $ageRatings = ageRating::all();
        $studios = studio::all();
        $genres = genre::all();

        return view('admins.manageMovies.movies.index', [
            'movies' => $movies,
            'ageRatings' => $ageRatings,
            'studios' => $studios,
            'genres' => $genres,
            'filters' => [[
                'name' => 'genre_id',
                'all_label' => 'Tất cả thể loại',
                'options' => $genres->pluck('name', 'genreID')->toArray(),
            ]],
        ]);
    }

    /**
     * Xác thực dữ liệu, xử lý upload file ảnh/video và lưu thông tin phim mới.
     */
    public function store(Request $request)
    {
        $request->validate([
            'movieTitle' => 'required|max:255',
            'director' => 'required|max:255',
            'releaseDate' => 'required|date',
            'duration' => 'required|integer|min:1',
            'trailer' => 'nullable|mimes:mp4,mov,avi,webm|max:100000',
            'description' => 'nullable',
            'ageRatingID' => 'required|exists:age_ratings,ageRatingID',
            'studioID' => 'required|exists:studios,studioID',
            'genreID' => 'required|array',
            'genreID.*' => 'exists:genres,genreID',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'duration.required' => 'Thời lượng không được để trống',
            'duration.integer' => 'Thời lượng phải là số nguyên',
            'duration.min' => 'Thời lượng phải lớn hơn 0 phút',
        ]);

        $data = $request->only([
            'movieTitle',
            'director',
            'releaseDate',
            'duration',
            'description',
            'ageRatingID',
            'studioID',
        ]);

        $today = Carbon::today();
        $releaseDate = Carbon::parse($request->releaseDate);

        // Chặn không cho phép chọn ngày phát hành trong quá khứ
        if ($releaseDate->lt($today)) {
            return back()->withErrors([
                'releaseDate' => 'Không được chọn ngày cũ.',
            ])->withInput();
        }

        // Xử lý lưu file Poster vào thư mục public/posters
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = time() . '_poster.' . $file->getClientOriginalExtension();
            $file->move(public_path('posters'), $filename);
            $data['poster'] = $filename;
        }

        // Xử lý lưu file Trailer vào thư mục public/uploads/trailers
        if ($request->hasFile('trailer')) {
            $trailerName = time() . '_trailer.' . $request->trailer->extension();
            $request->trailer->move(public_path('uploads/trailers'), $trailerName);
            $data['trailer'] = 'uploads/trailers/' . $trailerName;
        }

        $movie = movie::create($data);
        
        // Gắn mối quan hệ nhiều-nhiều giữa Phim và các Thể loại được chọn vào bảng trung gian
        $movie->genres()->attach($request->genreID);

        return redirect()->route('admin.movies.index')
            ->with('success', 'Thêm phim thành công');
    }

    /**
     * Cập nhật thông tin phim (Có ràng buộc ngày phát hành và làm sạch file cũ).
     */
    public function update(Request $request, $id)
    {
        $movie = movie::findOrFail($id);

        $request->validate([
            'movieTitle' => 'required|max:255',
            'director' => 'required|max:255',
            'releaseDate' => 'required|date',
            'duration' => 'required|integer|min:1',
            'trailer' => 'nullable|mimes:mp4,mov,avi,webm|max:100000',
            'description' => 'nullable',
            'ageRatingID' => 'required|exists:age_ratings,ageRatingID',
            'studioID' => 'required|exists:studios,studioID',
            'genreID' => 'required|array',
            'genreID.*' => 'exists:genres,genreID',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'movieTitle.required' => 'Tên phim không được để trống',
            'director.required' => 'Đạo diễn không được để trống',
            'releaseDate.required' => 'Ngày phát hành không được để trống',
            'releaseDate.date' => 'Ngày phát hành không hợp lệ',
            'duration.required' => 'Thời lượng không được để trống',
            'duration.integer' => 'Thời lượng phải là số nguyên',
            'duration.min' => 'Thời lượng phải lớn hơn 0 phút',
            'poster.mimes' => 'File phải đúng định dạng: jpg, jpeg, png, webp',
        ]);

        $data = $request->only([
            'movieTitle',
            'director',
            'releaseDate',
            'duration',
            'description',
            'ageRatingID',
            'studioID',
        ]);

        $today = Carbon::today();
        $releaseDate = Carbon::parse($request->releaseDate);

        if ($releaseDate->lt($today)) {
            return back()->withErrors([
                'releaseDate' => 'Không được chọn ngày trong quá khứ.',
            ])->withInput();
        }

        // RÀNG BUỘC: Nếu phim đã được xếp lịch chiếu, cấm tuyệt đối việc thay đổi ngày phát hành
        if ($movie->showTimes()->exists() && $releaseDate->ne(Carbon::parse($movie->releaseDate))) {
            return back()->withErrors([
                'releaseDate' => 'Phim đã có suất chiếu, không thể thay đổi ngày phát hành.',
            ])->withInput()->with('edit_id', $movie->movieID);
        }

        // Cập nhật Poster mới (Ghi đè tên file trong DB, chưa xóa file cũ vật lý)
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('posters'), $filename);
            $data['poster'] = $filename;
        }

        // Cập nhật Trailer mới và thực hiện xóa file Trailer cũ khỏi ổ đĩa để tránh rác hệ thống
        if ($request->hasFile('trailer')) {
            if ($movie->trailer && file_exists(public_path($movie->trailer))) {
                unlink(public_path($movie->trailer));
            }

            $trailerName = time() . '_trailer.' . $request->trailer->extension();
            $request->trailer->move(public_path('uploads/trailers'), $trailerName);
            $data['trailer'] = 'uploads/trailers/' . $trailerName;
        }

        $movie->update($data);
        
        // Làm mới và đồng bộ lại danh sách thể loại trong bảng trung gian (Xóa cũ, nạp mới)
        $movie->genres()->sync($request->genreID);

        return redirect()->route('admin.movies.index')->with('success', 'Cập nhật thành công');
    }

    /**
     * Hiển thị chi tiết phim và thông tin phòng chiếu dựa theo suất chiếu được chỉ định.
     */
    public function show(Request $request, movie $movie)
    {
        $movie->load(['showTimes.room']);

        $selectedShowTime = null;
        $selectedShowTimeId = $request->integer('showtime');

        // Tìm suất chiếu cụ thể được chọn từ request trong danh sách suất chiếu của phim
        if ($selectedShowTimeId) {
            $selectedShowTime = $movie->showTimes->firstWhere('showTimeID', $selectedShowTimeId);
        }

        return view('system.show', compact('movie', 'selectedShowTime'));
    }

    /**
     * Xóa phim khỏi hệ thống (Bắt lỗi khóa ngoại nếu phim đang ràng buộc với dữ liệu lịch chiếu).
     */
    public function destroy($id)
    {
        try {
            $movie = movie::findOrFail($id);
            $movie->delete();

            return back()->with('success', 'Xóa thành công');
        } catch (QueryException $e) {
            // Chặn xóa nếu dính lỗi Integrity constraint violation (Khóa ngoại ràng buộc với bảng show_times)
            return back()->with('error', 'Phim đang chiếu không thể xóa.');
        }
    }
}