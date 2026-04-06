@extends('layouts.appAdmin')

@section('content')

<div class="container mt-4">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Tạo hóa đơn</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('invoices.store') }}" method="POST">
                @csrf

                <div class="row">

                    <!-- Customer -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Khách hàng</label>
                        <select name="customerID" class="form-select">
                            @if($customers->isEmpty())
                            <option value="">-- Chưa có khách hàng --</option>
                            @else
                            @foreach($customers as $c)
                            <option value="{{ $c->customerID }}">{{ $c->fullName }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Admin -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Admin</label>
                        <select name="adminID" class="form-select">
                            @if($admins->isEmpty())
                            <option value="">-- Chưa có admin --</option>
                            @else
                            @foreach($admins as $a)
                            <option value="{{ $a->adminID }}">
                                {{ $a->fullName }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Payment -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Thanh toán</label>
                        <select name="paymentID" class="form-select">
                            @if($payments->isEmpty())
                            <option>-- Chưa có phương thức thanh toán --</option>
                            @else
                            @foreach($payments as $p)
                            <option value="{{ $p->paymentID }}">{{ $p->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Total -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tổng tiền</label>
                        <input type="number" name="totalAmount" class="form-control" placeholder="Nhập tổng tiền">
                    </div>

                </div>

                <div class="text-end">
                    <button class="btn btn-dark">Lưu hóa đơn</button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary ms-2">Hủy</a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection