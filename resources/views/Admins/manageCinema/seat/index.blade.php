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
    .seat.maintenance {
        background: #6F0E10;    
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

    .box.maintenance {
        background: #6f0e10;
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
        <button type="button" onclick="goEditPage()" class="btn btn-primary">Cập nhật</button>
    </form>

    <!-- màn hình -->
    <div class="text-center mb-3">
        <div class="screen-bar"></div>
        <p class="text-warning mt-2">MÀN HÌNH</p>
    </div>

    <!-- GRID -->
    <div id="seatGrid" class="seat-grid"></div>
    <form id="editForm" method="GET" action="{{ route('seat.editMultiple') }}">
        <input type="hidden" name="seatIDs" id="seatIDsInput">
    </form>


    <!-- LEGEND -->
    <div class="legend">
        <div class="legend-item"><span class="box normal"></span> Ghế thường</div>
        <div class="legend-item"><span class="box vip"></span> Ghế VIP</div>
        <div class="legend-item"><span class="box couple"></span> Ghế đôi</div>
        <div class="legend-item"><span class="box maintenance"></span> Ghế bảo trì</div>
    </div>
</div>
<div id="errorBox" class="alert alert-danger d-none"></div>

<script>
    const currentRoom = "{{ $roomID }}";
    const rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
    const cols = 14;

    let seatsData = [];
    let isMouseDown = false;
    let selectedSeats = [];

    document.addEventListener('mousedown', () => isMouseDown = true);
    document.addEventListener('mouseup', () => isMouseDown = false);

    function loadSeats() {
        fetch(`/admins/seat/ajax/${currentRoom}`)
            .then(res => res.json())
            .then(data => {
                seatsData = data; // 
                renderGrid();
            });
    }

    function renderGrid() {
        let html = '';

        rows.forEach(row => {
            html += `<div class="seat-row">`;
            html += `<span class="row-label">${row}</span>`;

            for (let i = 1; i <= cols; i++) {

                let seat = seatsData.find(s =>
                    s.rowSeat === row && Number(s.colSeat) === i
                );

                if (seat) {
                    let seatClass = '';

                    if (seat.seatTypeID == 2) seatClass = 'normal';
                    else if (seat.seatTypeID == 1) seatClass = 'vip';
                    else if (seat.seatTypeID == 3) seatClass = 'couple';
                    else if (seat.seatTypeID == 4) seatClass = 'maintenance';

                    html += `
                    <div class="seat ${seatClass}"
                        data-id="${seat.seatID}"
                        data-type="${seat.seatTypeID}"
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

    function handleHover(el) {
        if (!isMouseDown) return;

        let id = el.dataset.id;

        if (!selectedSeats.includes(id)) {
            selectedSeats.push(id);
            el.classList.add('selected');
        }
    }

    function createSeat(row, col) {

        seatsData.push({
            seatID: 'temp_' + Date.now(),
            rowSeat: row,
            colSeat: col,
            seatTypeID: 1
        });

        renderGrid();

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
            .then(res => res.json())
            .then(res => {
                if (res.error) {
                    showError(res.error);
                    loadSeats();
                } else {
                    loadSeats();
                }
            });
    }

    function deleteSeat(id, e) {
        if (e) e.stopPropagation();

        fetch(`/admins/seat/ajax-delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.error) {
                    showError(res.error);
                }
                loadSeats();
            });
    }

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

    function cycleSeatType(seatID, currentType) {
        let nextType = 1;

        if (currentType == 1) nextType = 2;
        else if (currentType == 2) nextType = 3;
        else if (currentType == 3) nextType = 4;

        fetch(`/admins/seat/ajax-update-type`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                seatIDs: [seatID],
                seatTypeID: nextType
            })
        }).then(() => loadSeats());
    }   

    function showError(message) {
        const wrapper = document.querySelector('.modern-toast-wrapper');

        const toast = document.createElement('div');
        toast.className = 'modern-toast toast-error';

        toast.innerHTML = `
        <div class="toast-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">Lỗi</div>
            <div class="toast-text">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    `;

        wrapper.appendChild(toast);

        // auto ẩn giống layout
        setTimeout(() => {
            toast.style.transition = '.4s';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(40px)';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }

    function goEditPage() {
        if (selectedSeats.length === 0) {
            showError('Chưa chọn ghế');
            return;
        }

        document.getElementById('seatIDsInput').value = selectedSeats.join(',');
        document.getElementById('editForm').submit();
    }

    loadSeats();
</script>

@endsection