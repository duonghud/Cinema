<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\customer;

class customerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = customer::all();
        return view('admins.customer.index', ['customers' => $customers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.customer.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fullName' => 'required',
            'email' => 'required|email|unique:customers,email',
            'phoneNumber' => 'required',
            'address' => 'required',
            'password' => 'required|string|min:8'
        ]);

        $customers = new customer();
        $customers->fullname = $request->input('fullName');
        $customers->email = $request->input('email');
        $customers->phoneNumber = $request->input('phoneNumber');
        $customers->address = $request->input('address');
        $customers->password = $request->input('password');
        $customers->save();

        return redirect()->route('customer.index')->with('success', 'Tạo thành công');
    }

    function edit(string $customerID)
    {
        $customers = customer::findOrFail($customerID);
        return view('admins.customer.edit', ['customers' => $customers]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $customerID)
    {
        $customers = customer::findOrFail($customerID);

        $request->validate([
            'fullName' => 'required',
            'email' => 'required|email|unique:customers,email,' . $customerID . ',customerID',
            'phoneNumber' => 'required',
            'address' => 'required',
            'password' => 'nullable|min:8'
        ]);

        $customers->fullname = $request->input('fullName');
        $customers->email = $request->input('email');
        $customers->phoneNumber = $request->input('phoneNumber');
        $customers->address = $request->input('address');
        if ($request->input('password')) {
            $customers->password = $request->input('password');
        }
        $customers->save();

        return redirect()->route('customer.index')->with('success', 'Sửa thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customers = customer::findOrFail($id);
        $customers->delete();

        return redirect()->route('customer.index')->with('success', 'Xóa thành công');
    }
}
