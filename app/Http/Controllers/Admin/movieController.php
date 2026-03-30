<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\movie;
use App\Models\Admin\ageRating;
use App\Models\Admin\studio;
use App\Models\Admin\genre;

class movieController extends Controller
{
    public function index()
    {
        $movies = movie::with(['ageRating', 'studio', 'genres'])->paginate(5);
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
            'trailer' => 'nullable|url',
            'description' => 'nullable',
            'ageRatingID' => 'required|exists:age_ratings,ageRatingID',
            'studioID' => 'required|exists:studios,studioID',
            'genreID' => 'required|array', // kiểm tra là mảng
            'genreID.*' => 'exists:genres,genreID', // từng phần tử phải tồn tại
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ], [
            'movieTitle.required' => 'Tên phim không được để trống',
            'director.required' => 'Đạo diễn không được để trống',
            'releaseDate.required' => 'Ngày phát hành không được để trống',
            'releaseDate.date' => 'Ngày phát hành không hợp lệ',
            'trailer.url' => 'Trailer phải là link hợp lệ',
            'ageRatingID.required' => 'Vui lòng chọn kiểm duyệt',
            'studioID.required' => 'Vui lòng chọn hãng',
            'genreID.required' => 'Vui lòng chọn ít nhất một thể loại',
            'genreID.*.exists' => 'Thể loại không hợp lệ',
            'poster.image' => 'File phải là hình ảnh',
        ]);

        $data = $request->only([
            'movieTitle',
            'director',
            'releaseDate',
            'trailer',
            'description',
            'ageRatingID',
            'studioID'
        ]);

        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('posters'), $filename);
            $data['poster'] = $filename;
        }

        $movie = Movie::create($data);

        // gắn nhiều thể loại
        $movie->genres()->attach($request->genreID);

        return redirect()->route('movies.index')->with('success', 'Thêm phim thành công');
    }

    public function update(Request $request, $id)
    {
        $movie = movie::findOrFail($id);

        $request->validate([
            'movieTitle' => 'required|max:255',
            'director' => 'required|max:255',
            'releaseDate' => 'required|date',
            'trailer' => 'nullable|url',
            'description' => 'nullable',
            'ageRatingID' => 'required|exists:age_ratings,ageRatingID',
            'studioID' => 'required|exists:studios,studioID',
            'genreID' => 'required|array',
            'genreID.*' => 'exists:genres,genreID',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ], [
            'movieTitle.required' => 'Tên phim không được để trống',
            'director.required' => 'Đạo diễn không được để trống',
            'releaseDate.required' => 'Ngày phát hành không được để trống',
            'releaseDate.date' => 'Ngày phát hành không hợp lệ',
        ]);

        $data = $request->only([
            'movieTitle',
            'director',
            'releaseDate',
            'trailer',
            'description',
            'ageRatingID',
            'studioID',
        ]);

        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('posters'), $filename);
            $data['poster'] = $filename;
        }

        $movie->update($data);
        $movie->genres()->sync($request->genreID); // cập nhật thể loại

        return redirect()->route('movies.index')->with('success', 'Cập nhật thành công');
    }
    public function show(Movie $movie)
    {
        return view('show', compact('movie'));
    }



    public function destroy($id)
    {
        movie::destroy($id);

        return redirect()->route('movies.index')->with('success', 'Xóa thành công');
    }
}
