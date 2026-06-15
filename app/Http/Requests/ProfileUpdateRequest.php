<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return session()->has('customer');
    }

    public function rules(): array
    {
        $customerId = session('customer')->customerID;

        return [
            'fullName'    => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', Rule::unique('customers', 'email')->ignore($customerId, 'customerID')],
            'phoneNumber' => ['required', 'string', 'max:20'],
            'address'     => ['required', 'string', 'max:500'],
            'password'    => ['nullable', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'fullName.required'    => 'Họ tên không được để trống',
            'email.required'       => 'Email không được để trống',
            'email.email'          => 'Email không đúng định dạng',
            'email.unique'         => 'Email này đã được sử dụng bởi tài khoản khác',
            'phoneNumber.required' => 'Số điện thoại không được để trống',
            'address.required'     => 'Địa chỉ không được để trống',
            'password.min'         => 'Mật khẩu mới phải có ít nhất 6 ký tự',
            'password.confirmed'   => 'Xác nhận mật khẩu không khớp',
        ];
    }
}