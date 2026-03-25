@extends('layouts.appAdmin')

@section('content')
    <a href="{{ route('customer.create') }}">
        <button>
            Thêm khách hàng
        </button>
    </a>

    <table class="table table-bordered">
        <tr>
            <th>STT</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Địa chỉ</th>
            <th></th>
            <th></th>
        </tr>

        @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->customerID }}</td>
                <td>{{ $customer->fullName }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phoneNumber }}</td>
                <td>{{ $customer->address }}</td>
                <td>
                    <a href="{{ route('customer.edit', $customer->customerID) }}">
                        <button>
                            Sửa
                        </button>
                    </a>
                </td>
                <td>
                    <form action="{{ route('customer.destroy', $customer->customerID) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')">
                            Xóa
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
