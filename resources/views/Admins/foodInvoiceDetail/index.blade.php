@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <h2 class="mb-4">Chi tiết hóa đơn #{{ $invoice->id }}</h2>

    <div class="mb-4">
        <p><strong>Khách hàng:</strong> {{ $invoice->customer->fullName ?? 'N/A' }}</p>
        <p><strong>Phương thức thanh toán:</strong> {{ $invoice->payment->name ?? 'N/A' }}</p>
        <p><strong>Ngày tạo:</strong> {{ $invoice->created_at?->format('d-m-Y') ?? 'N/A' }}</p>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Food</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>

        <tbody>
            @php $total = 0; @endphp
            @foreach($invoice->details as $detail)
            @php
            $subtotal = $detail->quantity * $detail->price;
            $total += $subtotal;
            @endphp
            <tr>
                <td>{{ $detail->food->foodName ?? '' }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ number_format($detail->price) }}</td>
                <td>{{ number_format($subtotal) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                <td colspan="2"><strong>{{ number_format($total) }}</strong></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection