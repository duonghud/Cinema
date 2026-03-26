@extends('layouts.appAdmin')

@section('content')

<div class="container mt-4">

    <h4 class="fw-semibold mb-3">Tạo hóa đơn đồ ăn</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('foodInvoice.store') }}" method="POST">
                @csrf

                <div class="row">

                    <!-- Khách hàng -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Khách hàng</label>

                        <select name="customerID"
                            class="form-select @error('customerID') is-invalid @enderror">

                            <option value="">-- Chọn khách hàng --</option>

                            @foreach($customers as $c)
                                <option value="{{ $c->customerID }}"
                                    {{ old('customerID') == $c->customerID ? 'selected' : '' }}>
                                    {{ $c->fullName }}
                                </option>
                            @endforeach
                        </select>

                        @error('customerID')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Thanh toán -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phương thức thanh toán</label>

                        <select name="paymentID"
                            class="form-select @error('paymentID') is-invalid @enderror">

                            <option value="">-- Chọn thanh toán --</option>

                            @foreach($payments as $p)
                                <option value="{{ $p->paymentID }}"
                                    {{ old('paymentID') == $p->paymentID ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('paymentID')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Thời gian -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Thời gian đặt</label>

                        <input type="datetime-local"
                               name="orderTime"
                               value="{{ old('orderTime') }}"
                               class="form-control @error('orderTime') is-invalid @enderror">

                        @error('orderTime')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                <!-- Danh sách món -->
                <div class="mb-3">
                    <h5 class="fw-semibold mb-3">Chọn món</h5>

                    @error('foods')
                        <div class="text-danger mb-2">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="card border">
                        <div class="card-body">

                            @foreach($foods as $food)
                                <div class="d-flex justify-content-between align-items-center mb-2">

                                    <div>
                                        <strong>{{ $food->foodName }}</strong><br>
                                        <small class="text-muted">
                                            {{ number_format($food->price, 0, ',', '.') }} ₫
                                        </small>
                                    </div>

                                    <input type="number"
                                           name="foods[{{ $food->foodID }}]"
                                           value="{{ old('foods.' . $food->foodID, 0) }}"
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
                        + Tạo hóa đơn
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection