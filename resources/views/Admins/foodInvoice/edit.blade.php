@extends('layouts.appAdmin')
@section('content')
<form action="{{ route('foodInvoice.update', $invoice->foodInvoiceID) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Customer -->
     <label for="">Tên khách hàng</label>
    <select name="customerID" class="form-control">
        @foreach($customers as $c)
            <option value="{{ $c->customerID }}"
                {{ $invoice->customerID == $c->customerID ? 'selected' : '' }}>
                {{ $c->fullName }}
            </option>
        @endforeach
    </select>

    <!-- Payment -->
     <label for="">Phương thức thanh toán</label>
    <select name="paymentID" class="form-control">
        @foreach($payments as $p)
            <option value="{{ $p->paymentID }}"
                {{ $invoice->paymentID == $p->paymentID ? 'selected' : '' }}>
                {{ $p->name }}
            </option>
        @endforeach
    </select>

    <h4>Món ăn</h4>

    @foreach($foods as $food)
        @php
            $qty = $invoice->details->firstWhere('foodID', $food->foodID)->quantity ?? 0;
        @endphp

        <div class="d-flex mb-2">
            <span style="width:200px">
                {{ $food->foodName }}
            </span>

            <input type="number"
                   name="foods[{{ $food->foodID }}]"
                   value="{{ $qty }}"
                   min="0">
        </div>
    @endforeach

    <button class="btn btn-primary mt-3">Cập nhật</button>
</form>
@endsection