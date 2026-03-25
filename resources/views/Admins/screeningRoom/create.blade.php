@extends('layouts.appAdmin')

@section('content')
<h2>Add Screening Room</h2>

<form action="{{ route('screeningRoom.store') }}" method="POST">

    @csrf

    <div>
        <label>Room Name</label>
        <input type="text" name="roomName">
    </div>

    <div>
        <label>Capacity</label>
        <input type="number" name="capacity">
    </div>

    <div>
        <label>Screen Type</label>

        <select name="screenTypeID">
            @foreach($screenTypes as $type)
            <option value="{{ $type->screenTypeID }}">
                {{ $type->name }}
            </option>
            @endforeach
        </select>

    </div>

    <button type="submit">
        Save
    </button>

</form>

@endsection
