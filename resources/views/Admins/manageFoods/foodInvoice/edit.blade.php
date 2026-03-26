@extends('layouts.appAdmin')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="mb-3">
        <h4 class="fw-semibold">Cập nhật hóa đơn</h4>
    </div>

    <!-- Card -->
    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('foodInvoice.update', $invoice->foodInvoiceID) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <!-- Customer -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Khách hàng</label>
                        <select name="customerID" class="form-select">
                            @foreach($customers as $c)
                                <option value="{{ $c->customerID }}"
                                    {{ $invoice->customerID == $c->customerID ? 'selected' : '' }}>
                                    {{ $c->fullName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phương thức thanh toán</label>
                        <select name="paymentID" class="form-select">
                            @foreach($payments as $p)
                                <option value="{{ $p->paymentID }}"
                                    {{ $invoice->paymentID == $p->paymentID ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <!-- Food list -->
                <div class="mb-3">
                    <h5 class="fw-semibold mb-3">Món ăn</h5>

                    <div class="card border">
                        <div class="card-body">

                            @foreach($foods as $food)
                                @php
                                    $detail = $invoice->details->firstWhere('foodID', $food->foodID);
                                    $qty = $detail ? $detail->quantity : 0;
                                @endphp

                                <div class="d-flex justify-content-between align-items-center mb-2">

                                    <!-- Name -->
                                    <div>
                                        <strong>{{ $food->foodName }}</strong>
                                    </div>

                                    <!-- Quantity -->
                                    <input type="number"
                                           name="foods[{{ $food->foodID }}]"
                                           value="{{ $qty }}"
                                           min="0"
                                           class="form-control"
                                           style="width:90px">
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <!-- Button -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('foodInvoice.index') }}" 
                       class="btn btn-secondary me-2">
                        Quay lại
                    </a>

                    <button type="submit" class="btn btn-dark">
                        Cập nhật
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection