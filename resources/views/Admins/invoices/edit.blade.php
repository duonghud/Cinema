@extends('layouts.appAdmin')

@section('content')

<div class="container mt-4">

<div class="card shadow-sm border-0">
    <div class="card-header bg-warning">
        <h5 class="mb-0"> Cập nhật hóa đơn</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('invoices.update',$invoice->invoiceID) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label">Tổng tiền</label>
                <input type="number" 
                       name="totalAmount" 
                       value="{{ $invoice->totalAmount }}"
                       class="form-control">
            </div>

            <div class="text-end">
                <button class="btn btn-warning">Cập nhật</button>
            </div>

        </form>
    </div>
</div>

</div>
@endsection
