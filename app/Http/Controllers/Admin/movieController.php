<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\movie;
use App\Models\Admin\ageRating;
use App\Models\Admin\studio;

class movieController extends Controller
{
    public function index()
    {
        $movies = movie::with(['ageRating','studio'])->get();
        $ageRatings = ageRating::all();
        $studios = studio::all();

        return view('admins.movies.index', compact('movies','ageRatings','studios'));
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'movieTitle',
            'director',
            'releaseDate',
            'trailer',
            'description',
            'ageRatingID',
            'studioID'
        ]);

        // upload poster
        if ($request->hasFile('poster')) {

            $file = $request->file('poster');
            $filename = time().'.'.$file->getClientOriginalExtension();

            $file->move(public_path('posters'), $filename);

            $data['poster'] = $filename;
        }

        movie::create($data);

        return redirect()->route('movies.index')->with('success','Thêm phim thành công');
    }

    public function update(Request $request, $id)
    {
        $movie = movie::findOrFail($id);

        $data = $request->only([
            'movieTitle',
            'director',
            'releaseDate',
            'trailer',
            'description',
            'ageRatingID',
            'studioID'
        ]);

        // upload poster mới nếu có
        if ($request->hasFile('poster')) {

            $file = $request->file('poster');
            $filename = time().'.'.$file->getClientOriginalExtension();

            $file->move(public_path('posters'), $filename);

            $data['poster'] = $filename;
        }

        $movie->update($data);

        return redirect()->route('movies.index')->with('success','Cập nhật thành công');
    }

    public function destroy($id)
    {
        movie::destroy($id);

        return redirect()->route('movies.index')->with('success','Xóa thành công');
    }

}
