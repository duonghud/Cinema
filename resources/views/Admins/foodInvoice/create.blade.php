@extends('layouts.appAdmin')

@section('content')

<div class="container mt-4">
    <h2>Tạo mới hóa đơn</h2>

    <form action="{{ route('foodInvoice.store') }}" method="POST">
        @csrf

        <!-- Khách hàng -->
        <div class="mb-3">
            <label for="customerID" class="form-label">Khách hàng</label>
            <select name="customerID" id="customerID" class="form-control">
                @foreach($customers as $c)
                <option value="{{ $c->customerID }}">
                    {{ $c->fullName }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Phương thức thanh toán -->
        <div class="mb-3">
            <label for="paymentID" class="form-label">Phương thức thanh toán</label>
            <select name="paymentID" id="paymentID" class="form-control">
                @foreach($payments as $p)
                <option value="{{ $p->paymentID }}">
                    {{ $p->name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Thời gian đặt -->
        <div class="mb-3">
            <label for="orderTime" class="form-label">Thời gian đặt</label>
            <input type="datetime-local" name="orderTime" id="orderTime" class="form-control"
                value="{{ old('orderTime', now()->format('Y-m-d\TH:i')) }}">
        </div>

        <!-- Danh sách món -->
        <div class="mb-3">
            <h4>Món ăn & Đồ uống</h4>
            @foreach($foods as $food)
            <div class="d-flex align-items-center mb-2">
                <span style="width:250px">
                    {{ $food->foodName }} ({{ number_format($food->price, 0, ',', '.') }} ₫)
                </span>
                <input type="number" name="foods[{{ $food->foodID }}]" value="0" min="0" class="form-control" style="width:80px">
            </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-success mt-3">Tạo hóa đơn</button>
        <a href="{{ route('foodInvoice.index') }}" class="btn btn-secondary mt-3">Quay lại danh sách</a>
    </form>
</div>

@endsection