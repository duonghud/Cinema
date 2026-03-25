@extends('layouts.appAdmin')

@section('content')

<a href="{{ route('ticket.create') }}" class="btn btn-success mb-3">
    Thêm vé
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Giá</th>
            <th>Trạng thái</th>
            <th>Suất chiếu</th>
            <th>Ghế</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tickets as $ticket)
        <tr>
            <td>{{ $ticket->ticketID }}</td>
            <td>{{ $ticket->price }}</td>
            <td>{{ $ticket->status ?? '' }}</td>
            <td>{{ $ticket->showTime->showTimeID ?? '' }}</td>
            <td>{{ $ticket->seat->seatID ?? '' }}</td>
            <td>
                <a href="{{ route('ticket.edit', $ticket->ticketID) }}" class="btn btn-warning btn-sm">Sửa</a>

                <form action="{{ route('ticket.destroy', $ticket->ticketID) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection