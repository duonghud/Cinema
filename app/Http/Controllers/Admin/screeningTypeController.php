<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\screeningRoom;
use App\Models\Admin\screenType;

class screeningTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $screenTypes = screenType::query()
            ->when($search, function ($query) use ($search) {
                $query->where('screenTypeID', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->paginate(5)
            ->withQueryString();

        return view('admins.manageCinema.screenType.index', ['screenTypes' => $screenTypes]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.manageCinema.screenType.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:screen_types,name',
        ], [
            'name.required' => 'Tên định dạng không được để trống.',
            'name.max' => 'Tên định dạng không quá 50 ký tự.',
            'name.unique' => 'Tên định dạng đã tồn tại.',
        ]);

        ScreenType::create($validated);

        return redirect()->route('screenType.index')
            ->with('success', 'Định dạng màn hình mới đã được thêm.');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $screenTypeID)
    {
        $screenTypes = screenType::findOrFail($screenTypeID);
        return view('admins.manageCinema.screenType.edit', ['screenTypes' => $screenTypes]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $screenTypes = ScreenType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:screen_types,name,' . $screenTypes->screenTypeID . ',screenTypeID',
        ], [
            'name.required' => 'Tên định dạng không được để trống.',
            'name.max' => 'Tên định dạng không quá 50 ký tự.',
            'name.unique' => 'Tên định dạng đã tồn tại.',
        ]);

        $screenTypes->update($validated);

        return redirect()->route('screenType.index')
            ->with('success', 'Cập nhật định dạng màn hình thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $screenTypes = screenType::findOrFail($id);
        $screenTypes->delete();

        return redirect()->route('screenType.index')->with('success', 'Xóa thành công');
    }
}
