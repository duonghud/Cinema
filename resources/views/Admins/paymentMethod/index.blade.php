@extends('layouts.appAdmin')

@section('content')
    <a href="{{ route('paymentMethod.create') }}">
        <button>
            Thêm phương thức
        </button>
    </a>

    <table class="table table-bordered">
        <tr>
            <th>STT</th>
            <th>Tên phương thức</th>
            <th></th>
            <th></th>
        </tr>

        @foreach($paymentMethods as $paymentMethod)
            <tr>
                <td>{{ $paymentMethod->paymentID }}</td>
                <td>{{ $paymentMethod->name }}</td>
                <td>
                    <a href="{{ route('paymentMethod.edit', $paymentMethod->paymentID) }}">
                        <button>
                            Sửa
                        </button>
                    </a>
                </td>
                <td>
                    <form action="{{ route('paymentMethod.destroy', $paymentMethod->paymentID) }}" method="POST">
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
