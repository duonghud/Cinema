@extends('layouts.appAdmin')

@section('content')
<form action="{{ route('studio.update', $studio->studioID) }}" method="post">
    @csrf
    @method('PUT')
    <div>
        <label for="name">Tên nhà sản xuất: </label>

        <input type="text" name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name') }}">

        @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
    <div>
        <button type="submit" class="btn btn-primary">
            Cập nhật
        </button>
    </div>
</form>
@endsection
