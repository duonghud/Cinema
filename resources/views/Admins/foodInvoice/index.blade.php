@extends('layouts.appAdmin')

@section('content')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Danh sách hóa đơn</h2>
        <a href="{{ route('foodInvoice.create') }}" class="btn btn-primary">Tạo mới hóa đơn</a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Người mua</th>
                <th>Thời gian đặt</th>
                <th>Phương thức thanh toán</th>
                <th>Chi tiết món</th>
                <th>Tổng tiền</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $foodInvoice)
            <tr>
                <td>{{ $foodInvoice->foodInvoiceID }}</td>
                <td>{{ $foodInvoice->customer->fullName ?? 'Khách vãng lai' }}</td>
                <td>{{ $foodInvoice->created_at ? $foodInvoice->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</td>
                <td>{{ $foodInvoice->payment ? $foodInvoice->payment->name : '' }}</td>              
                <td>
                    <a href="{{ route('foodInvoice.show', $foodInvoice->foodInvoiceID) }}" class="btn btn-sm btn-info">Xem chi tiết</a>
                </td>
                <td>{{ number_format($foodInvoice->total, 0, ',', '.') }} ₫</td>
                <td>
                    <a href="{{ route('foodInvoice.edit', $foodInvoice->foodInvoiceID) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('foodInvoice.destroy', $foodInvoice->foodInvoiceID) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa hóa đơn này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection