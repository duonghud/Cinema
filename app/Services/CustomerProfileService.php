<?php

namespace App\Services;

use App\Models\Admin\Customer;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Hash;

class CustomerProfileService
{
    public function update(Customer $customer, ProfileUpdateRequest $request): Customer
    {
        $data = [
            'fullName'    => $request->fullName,
            'email'       => $request->email,
            'phoneNumber' => $request->phoneNumber,
            'address'     => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $customer->update($data);

        return $customer->fresh();
    }
}