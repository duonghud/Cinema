@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <h4>Chi tiết hóa đơn #{{ $invoice->invoiceID }}</h4>

    <div class="card p-3 mb-3">
        <p><b>Khách hàng:</b> {{ $invoice->customer->fullName ?? '' }}</p>
        <p><b>Nhân viên:</b> {{ $invoice->admin->fullName ?? '' }}</p>
        <p><b>Ngày tạo:</b> {{ $invoice->createDate }}</p>
        <p><b>Tổng tiền:</b> {{ number_format($invoice->totalAmount) }} đ</p>
        <p><b>Thanh toán:</b> {{ $invoice->paymentMethod->name ?? '' }}</p>
    </div>

    <h5>Danh sách vé / ghế</h5>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã vé</th>
                <th>Phim</th>
                <th>Suất chiếu</th>
                <th>Ghế</th>
                <th>Giá</th>
                <th>Trạng thái</th>
            </tr>
        </thead>

        <tbody>
            @forelse($invoice->tickets as $ticket)
                <tr>
                    <td>{{ $ticket->ticketID }}</td>

                    <td>
                        {{ $ticket->showTime->movie->movieTitle ?? '' }}
                    </td>

                    <td>
                        {{ $ticket->showTime->showDate ?? '' }}
                        {{ $ticket->showTime->startTime ?? '' }}
                    </td>

                    <td>
                        {{ $ticket->seat->rowSeat ?? '' }}{{ $ticket->seat->colSeat ?? '' }}
                    </td>

                    <td>
                        {{ number_format($ticket->price ?? 0) }} đ
                    </td>

                    <td>
                        @if($ticket->status == 'booked')
                            <span class="badge bg-success">Đã đặt</span>
                        @else
                            <span class="badge bg-secondary">Trống</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Không có vé nào trong hóa đơn
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection