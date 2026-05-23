<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\payment_method;

class paymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $paymentName = trim((string) $request->input('payment_name'));

        $paymentMethods = payment_method::query()
            ->when($search, function ($query) use ($search) {
                $query->where('paymentID', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->when($paymentName, function ($query) use ($paymentName) {
                $query->where('name', $paymentName);
            })
            ->paginate(5)
            ->withQueryString();

        $paymentNames = payment_method::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name', 'name');

        return view('admins.paymentMethod.index', [
            'paymentMethods' => $paymentMethods,
            'filters' => [[
                'name' => 'payment_name',
                'all_label' => 'Tất cả phương thức',
                'options' => $paymentNames->toArray(),
            ]],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.paymentMethod.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $paymentMethods = new payment_method();
        $paymentMethods->name = request('name');
        $paymentMethods->save();

        return redirect()->route('paymentMethod.index')->with('success', 'Tạo thành công');
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
    public function edit(string $paymentID)
    {
        $paymentMethods = payment_method::findOrFail($paymentID);
        return view('admins.paymentMethod.edit', ['paymentMethods' => $paymentMethods]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $paymentID)
    {
        $paymentMethods = payment_method::findOrFail($paymentID);

        $request->validate([
            'name' => 'required'
        ]);

        $paymentMethods->name = $request->input('name');
        $paymentMethods->save();

        return redirect()->route('paymentMethod.index')->with('success', 'Tạo thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentMethods = payment_method::findOrFail($id);
        $paymentMethods->delete();

        return redirect()->route('paymentMethod.index')->with('success', 'Xóa thành công');
    }
}
