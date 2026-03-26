<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
class customerController extends Controller
{
    public function index()
    {
        $customers = customer::all();
        return view('admins.manageUser.customer.index', ['customers' => $customers]);
    }

    public function create()
    {
        return view('admins.manageUser.customer.create');
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6',
            'phoneNumber' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ], [
            'fullName.required' => 'Họ tên không được để trống.',
            'fullName.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'phoneNumber.required' => 'Số điện thoại không được để trống.',
            'phoneNumber.max' => 'Số điện thoại quá dài.',
            'address.required' => 'Địa chỉ không được để trống.',
            'address.max' => 'Địa chỉ quá dài.',
        ]);

        Customer::create([
            'fullName' => $validated['fullName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phoneNumber' => $validated['phoneNumber'],
            'address' => $validated['address'],
        ]);

        return redirect()->route('customer.index')
            ->with('success', 'Khách hàng mới đã được thêm thành công.');
    }

    function edit(string $customerID)
    {
        $customers = customer::findOrFail($customerID);
        return view('admins.manageUser.customer.edit', ['customers' => $customers]);
    }


    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('customers')->ignore($customer->customerID, 'customerID'),
            ],
            'password' => 'nullable|string|min:6',
            'phoneNumber' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ], [
            'fullName.required' => 'Họ tên không được để trống.',
            'fullName.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'phoneNumber.required' => 'Số điện thoại không được để trống.',
            'phoneNumber.max' => 'Số điện thoại quá dài.',
            'address.required' => 'Địa chỉ không được để trống.',
            'address.max' => 'Địa chỉ quá dài.',
        ]);

        $customer->fullName = $validated['fullName'];
        $customer->email = $validated['email'];
        $customer->phoneNumber = $validated['phoneNumber'];
        $customer->address = $validated['address'];

        if (!empty($validated['password'])) {
            $customer->password = Hash::make($validated['password']);
        }

        $customer->save();

        return redirect()->route('customer.index')
            ->with('success', 'Cập nhật khách hàng thành công.');
    }

    public function destroy(string $id)
    {
        $customers = customer::findOrFail($id);
        $customers->delete();

        return redirect()->route('customer.index')->with('success', 'Xóa thành công');
    }
}
