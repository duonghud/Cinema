@extends('layouts.appAdmin')

@section('content')
<h3>Sửa kiểm duyệt</h3>

<form action="{{ route('ageRating.update', $ageRating) }}" method="POST">

    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Tên kiểm duyệt</label>
        <input type="text" name="code" 
               value="{{ $ageRating->code }}" 
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Mô tả</label>
        <input type="text" name="description" 
               value="{{ $ageRating->description }}" 
               class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">
        Cập nhật
    </button>

</form>

@endsection
