@extends('layouts.appAdmin')

@section('content')
    <h2>Edit Screening Room</h2>

    <a href="{{ route('screeningRoom.index') }}">
        Back
    </a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('screeningRoom.update', $room->roomID) }}" method="POST">

        @csrf
        @method('PUT')

        <div>
            <label>Room Name</label>
            <input type="text" name="roomName" value="{{ $room->roomName }}">
        </div>

        <br>

        <div>
            <label>Capacity</label>
            <input type="number" name="capacity" value="{{ $room->capacity }}">
        </div>

        <br>

        <div>
            <label>Screen Type</label>

            <select name="screenTypeID">

                @foreach($screenTypes as $type)

                    <option value="{{ $type->screenTypeID }}"
                        {{ $type->screenTypeID == $room->screenTypeID ? 'selected' : '' }}>

                        {{ $type->name }}

                    </option>

                @endforeach

            </select>

        </div>

        <br>

        <button type="submit">
            Update Room
        </button>

    </form>

@endsection

