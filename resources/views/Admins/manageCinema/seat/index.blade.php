@extends('layouts.appAdmin')

@section('content')

<style>
    body {
        background: #0b1220;
        color: white;
    }

    /* màn hình */
    .screen-bar {
        width: 60%;
        height: 40px;
        margin: auto;
        background: linear-gradient(to bottom, #fbbf24, transparent);
        border-radius: 50%;
    }

    /* GRID */
    .seat-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: center;
    }

    .seat-row {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .row-label {
        width: 30px;
    }

    /* GHẾ */
    .seat {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 12px;
        cursor: pointer;
        transition: 0.2s;
        user-select: none;
    }

    .seat.normal {
        background: #374151;
    }

    .seat.vip {
        background: #f97316;
    }

    .seat.couple {
        background: #ff4d4f;
    }

    .seat.booked {
        background: red;
        pointer-events: none;
    }

    .seat.empty {
        background: #111;
        border: 1px dashed #555;
    }

    .seat.selected {
        outline: 3px solid #3b82f6;
    }

    .seat:hover {
        transform: scale(1.1);
    }

    /* LEGEND */
    .legend {
        display: flex;
        justify-content: center;
        gap: 25px;
        margin-top: 20px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .box {
        width: 22px;
        height: 22px;
        border-radius: 6px;
    }

    .box.booked {
        background: red;
    }

    .box.selected {
        background: #3b82f6;
    }

    .box.normal {
        background: #374151;
    }

    .box.vip {
        background: #f97316;
    }

    .box.couple {
        background: #ff4d4f;
    }

    .seat {
        position: relative;
    }

    .delete-btn {
        position: absolute;
        top: -6px;
        right: -6px;
        background: red;
        color: white;
        font-size: 10px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: none;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .seat:hover .delete-btn {
        display: flex;
    }
</style>

<div class="container">

    <h2 class="mb-4 text-white">Quản lý ghế</h2>

    <!-- chọn phòng -->
    <form method="GET" class="mb-4">
        <select name="roomID" onchange="this.form.submit()" class="form-select w-25">
            @foreach($rooms as $room)
            <option value="{{ $room->roomID }}"
                {{ $roomID == $room->roomID ? 'selected' : '' }}>
                Phòng {{ $room->roomName }}
            </option>
            @endforeach
        </select>
    </form>

    <!-- màn hình -->
    <div class="text-center mb-3">
        <div class="screen-bar"></div>
        <p class="text-warning mt-2">MÀN HÌNH</p>
    </div>

    <!-- GRID -->
    <div id="seatGrid" class="seat-grid"></div>

    <!-- BUTTON -->
    <div class="text-center mt-3">
        <button onclick="changeType(1)" class="btn btn-secondary">Ghế thường</button>
        <button onclick="changeType(2)" class="btn btn-warning">Ghế VIP</button>
        <button onclick="changeType(3)" class="btn btn-danger">Ghế đôi</button>
    </div>

    <!-- LEGEND -->
    <div class="legend">
        <div class="legend-item"><span class="box booked"></span> Đã đặt</div>
        <div class="legend-item"><span class="box selected"></span> Ghế bạn chọn</div>
        <div class="legend-item"><span class="box normal"></span> Ghế thường</div>
        <div class="legend-item"><span class="box vip"></span> Ghế VIP</div>
        <div class="legend-item"><span class="box couple"></span> Ghế đôi</div>
    </div>

</div>

<script>
    const currentRoom = @json($roomID);
    const rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
    const cols = 14;

    let seatsData = [];
    let isMouseDown = false;
    let selectedSeats = [];

    // kéo chuột
    document.addEventListener('mousedown', () => isMouseDown = true);
    document.addEventListener('mouseup', () => isMouseDown = false);

    // load ghế
    function loadSeats() {
        fetch(`/admins/seat/ajax/${currentRoom}`)
            .then(res => res.json())
            .then(data => {
                seatsData = data;
                renderGrid();
            });
    }

    // render
    function renderGrid() {
        let html = '';

        rows.forEach(row => {
            html += `<div class="seat-row">`;
            html += `<span class="row-label">${row}</span>`;

            for (let i = 1; i <= cols; i++) {

                let seat = seatsData.find(s =>
                    s.rowSeat === row && s.colSeat == i
                );

                if (seat) {
                    html += `
                <div class="seat 
                    ${seat.seatTypeID == 1 ? 'normal' : ''}
                    ${seat.seatTypeID == 2 ? 'vip' : ''}
                    ${seat.seatTypeID == 3 ? 'couple' : ''}"
                    data-id="${seat.seatID}"
                    onmouseover="handleHover(this)">

                    ${row}${i}

                    <div class="delete-btn" 
                        onclick="deleteSeat(${seat.seatID}, event)">
                        ×
                    </div>

                </div>
                `;
                } else {
                    html += `
                <div class="seat empty"
                    onclick="createSeat('${row}', ${i})">
                    +
                </div>
                `;
                }
            }

            html += `</div>`;
        });

        document.getElementById('seatGrid').innerHTML = html;
    }

    // kéo chọn
    function handleHover(el) {
        if (!isMouseDown) return;

        let id = el.dataset.id;

        if (!selectedSeats.includes(id)) {
            selectedSeats.push(id);
            el.classList.add('selected');
        }
    }

    // tạo ghế
    function createSeat(row, col) {

        // render ngay lập tức
        seatsData.push({
            seatID: 'temp_' + Date.now(),
            rowSeat: row,
            colSeat: col,
            seatTypeID: 1
        });

        renderGrid();

        // gọi API thật
        fetch(`/admins/seat/ajax-store`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    rowSeat: row,
                    colSeat: col,
                    roomID: currentRoom,
                    seatTypeID: 1
                })
            })
            .then(() => loadSeats()); // sync lại
    }

    // xoá ghế
    function deleteSeat(id, e) {

        if (e) e.stopPropagation(); // Tránh lan ghế

        fetch(`/admins/seat/ajax-delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(() => loadSeats());
    }

    // đổi loại
    function changeType(type) {

        if (selectedSeats.length === 0) {
            alert('Chưa chọn ghế');
            return;
        }

        fetch(`/admins/seat/ajax-update-type`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    seatIDs: selectedSeats,
                    seatTypeID: type
                })
            })
            .then(() => {
                selectedSeats = [];
                loadSeats();
            });
    }

    // load
    loadSeats();
</script>

@endsection