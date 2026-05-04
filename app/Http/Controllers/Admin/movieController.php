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
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $movies = movie::with(['ageRating', 'studio', 'genres'])
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
            ->paginate(5)
            ->withQueryString();

        $ageRatings = ageRating::all();
        $studios = studio::all();
        $genres = genre::all();

        return view('admins.manageMovies.movies.index', compact('movies', 'ageRatings', 'studios', 'genres'));
    }

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

        if ($releaseDate->lt($today)) {
            return back()->withErrors([
                'releaseDate' => 'Không được chọn ngày cũ.',
            ])->withInput();
        }

        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = time() . '_poster.' . $file->getClientOriginalExtension();
            $file->move(public_path('posters'), $filename);
            $data['poster'] = $filename;
        }

        if ($request->hasFile('trailer')) {
            $trailerName = time() . '_trailer.' . $request->trailer->extension();
            $request->trailer->move(public_path('uploads/trailers'), $trailerName);
            $data['trailer'] = 'uploads/trailers/' . $trailerName;
        }

        $movie = movie::create($data);
        $movie->genres()->attach($request->genreID);

        return redirect()->route('admin.movies.index')
            ->with('success', 'Thêm phim thành công');
    }

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

        if ($movie->showTimes()->exists() && $releaseDate->ne(Carbon::parse($movie->releaseDate))) {
            return back()->withErrors([
                'releaseDate' => 'Phim đã có suất chiếu, không thể thay đổi ngày phát hành.',
            ])->withInput()->with('edit_id', $movie->movieID);
        }

        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('posters'), $filename);
            $data['poster'] = $filename;
        }

        if ($request->hasFile('trailer')) {
            if ($movie->trailer && file_exists(public_path($movie->trailer))) {
                unlink(public_path($movie->trailer));
            }

            $trailerName = time() . '_trailer.' . $request->trailer->extension();
            $request->trailer->move(public_path('uploads/trailers'), $trailerName);
            $data['trailer'] = 'uploads/trailers/' . $trailerName;
        }

        $movie->update($data);
        $movie->genres()->sync($request->genreID);

        return redirect()->route('admin.movies.index')->with('success', 'Cập nhật thành công');
    }

    public function show(movie $movie)
    {
        return view('system.show', compact('movie'));
    }

    public function destroy($id)
    {
        try {
            $movie = movie::findOrFail($id);
            $movie->delete();

            return back()->with('success', 'Xóa thành công');
        } catch (QueryException $e) {
            return back()->with('error', 'Phim đang chiếu không thể xóa.');
        }
    }
}
