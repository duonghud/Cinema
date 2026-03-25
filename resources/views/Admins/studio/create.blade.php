@extends('layouts.appAdmin')

@section('content')
<form action="{{ route('studio.store') }}" method="post">
    @csrf
    <div>
        <label for="name">Tên nhà sản xuất: </label>
        <input type="text" name="name" id="name" placeholder="Nhập tên nhà sản xuất">
    </div>

    <div>
        <button type="submit" class="btn btn-primary">
            Thêm nhà sản xuất
        </button>
    </div>
</form>
@endsection