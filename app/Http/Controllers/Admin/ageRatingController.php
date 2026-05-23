<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\ageRating;

class ageRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $code = trim((string) $request->input('code'));

        $ageRatings = ageRating::query()
            ->when($search, function ($query) use ($search) {
                $query->where('ageRatingID', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($code, function ($query) use ($code) {
                $query->where('code', $code);
            })
            ->paginate(5)
            ->withQueryString();

        $codes = ageRating::query()
            ->select('code')
            ->distinct()
            ->orderBy('code')
            ->pluck('code', 'code');

        return view('admins.manageMovies.ageRating.index', [
            'ageRatings' => $ageRatings,
            'filters' => [[
                'name' => 'code',
                'all_label' => 'Tất cả độ tuổi',
                'options' => $codes->toArray(),
            ]],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.manageMovies.ageRating.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'description' => 'required'
        ]);

        $ageRating = new ageRating();
        $ageRating->code = $request->code;
        $ageRating->description = $request->description;
        $ageRating->save();

        return redirect()->route('ageRating.index')
            ->with('success','Tạo thành công');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ageRating = ageRating::findOrFail($id);

        return view('admins.manageMovies.ageRating.edit', compact('ageRating'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'code' => 'required',
            'description' => 'required'
        ]);

        $ageRating = ageRating::findOrFail($id);

        $ageRating->code = $request->code;
        $ageRating->description = $request->description;
        $ageRating->save();

        return redirect()->route('ageRating.index')
            ->with('success','Cập nhật thành công');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ageRating = ageRating::findOrFail($id);
        $ageRating->delete();

        return redirect()->route('ageRating.index')

            ->with('success','Xóa thành công');

    }
}
