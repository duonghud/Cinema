@extends('layouts.appAdmin')

@section('content')

<style>
    body { background: #0b1220; color: white; }

    .screen-bar {
        width: 60%;
        height: 40px;
        margin: auto;
        background: linear-gradient(to bottom, #fbbf24, transparent);
        border-radius: 50%;
    }

    .seat-grid { display: flex; flex-direction: column; gap: 10px; align-items: center; }
    .seat-row { display: flex; gap: 8px; align-items: center; }
    .row-label { width: 30px; }

    .seat {
        width: 40px; height: 40px;
        border-radius: 8px;
        display: flex; justify-content: center; align-items: center;
        font-size: 12px; cursor: pointer;
        transition: 0.2s; user-select: none;
        position: relative;
    }

    .seat.normal { background: #374151; }
    .seat.vip { background: #f97316; }
    .seat.couple { background: #ff4d4f; }
    .seat.maintenance { background: #6F0E10; }
    .seat.booked { background: red; pointer-events: none; }
    .seat.empty { background: #111; border: 1px dashed #555; }
    .seat.selected { outline: 3px solid #3b82f6; }
    .seat:hover { transform: scale(1.1); }

    .delete-btn {
        position: absolute;
        top: -6px; right: -6px;
        background: red; color: white;
        font-size: 10px;
        width: 16px; height: 16px;
        border-radius: 50%;
        display: none;
        justify-content: center; align-items: center;
        cursor: pointer;
    }
    .seat:hover .delete-btn { display: flex; }

    .legend { display: flex; justify-content: center; gap: 25px; margin-top: 20px; }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .box { width: 22px; height: 22px; border-radius: 6px; }
    .box.normal { background: #374151; }
    .box.vip { background: #f97316; }
    .box.couple { background: #ff4d4f; }
    .box.maintenance { background: #6f0e10; }
</style>

<div class="container">

    <h2 class="mb-4 text-white">Quản lý ghế</h2>

    <!-- chọn phòng -->
    <form method="GET" class="mb-4">
        <select name="roomID" onchange="this.form.submit()" class="form-select w-25">
            @foreach($rooms as $roomItem)
                <option value="{{ $roomItem->roomID }}"
                    {{ $roomID == $roomItem->roomID ? 'selected' : '' }}>
                    Phòng {{ $roomItem->roomName }}
                </option>
            @endforeach
        </select>
        <button type="button" onclick="goEditPage()" class="btn btn-primary">Cập nhật</button>
    </form>

    <div class="text-center mb-3">
        <div class="screen-bar"></div>
        <p class="text-warning mt-2">MÀN HÌNH</p>
    </div>

    <div id="seatGrid" class="seat-grid"></div>

    <form id="editForm" method="GET" action="{{ route('seat.editMultiple') }}">
        <input type="hidden" name="seatIDs" id="seatIDsInput">
    </form>

    <div class="legend">
        <div class="legend-item"><span class="box normal"></span> Ghế thường</div>
        <div class="legend-item"><span class="box vip"></span> Ghế VIP</div>
        <div class="legend-item"><span class="box couple"></span> Ghế đôi</div>
        <div class="legend-item"><span class="box maintenance"></span> Ghế bảo trì</div>
    </div>
</div>

<!-- toast wrapper -->
<div class="modern-toast-wrapper position-fixed top-0 end-0 p-3"></div>

<script>

/* ================== DEBUG HELPER ================== */
function logError(msg, data = null) {
    console.error(" ERROR:", msg, data || "");
}

function logWarn(msg, data = null) {
    console.warn(" WARN:", msg, data || "");
}

function logInfo(msg, data = null) {
    console.log(" INFO:", msg, data || "");
}
/* ================================================= */


/* ================== FIX QUAN TRỌNG ================== */
const currentRoom = "{{ $roomID ?? 0 }}";
const rowsCount = "{{ $room ? $room->rows : 0 }}";
const cols = "{{ $room ? $room->cols : 0 }}";

logInfo("ROOM ID", currentRoom);
logInfo("ROWS", rowsCount);
logInfo("COLS", cols);

/* CHECK DATA NGAY */
if (!currentRoom || currentRoom == "0") {
    logError("currentRoom bị null hoặc = 0");
}

if (rowsCount == "0" || cols == "0") {
    logError("rows hoặc cols = 0 → KHÔNG render được grid");
}
/* =================================================== */


/* ================= INIT ROW ================= */
let rows = [];

try {
    rows = Array.from({ length: Number(rowsCount) }, (_, i) =>
        String.fromCharCode(65 + i)
    );
    logInfo("ROWS ARRAY", rows);
} catch (e) {
    logError("Lỗi tạo rows", e);
}
/* ============================================ */


let seatsData = [];
let isMouseDown = false;
let selectedSeats = [];

document.addEventListener('mousedown', () => isMouseDown = true);
document.addEventListener('mouseup', () => isMouseDown = false);


/* ================= LOAD ================= */
function loadSeats() {

    logInfo("CALL API", `/admins/seat/ajax/${currentRoom}`);

    if (!currentRoom) {
        logError("Không có roomID → không gọi API");
        return;
    }

    fetch(`/admins/seat/ajax/${currentRoom}`)
        .then(res => {

            logInfo("API STATUS", res.status);

            if (!res.ok) {
                logError("API lỗi", res.status);
                throw new Error("API lỗi");
            }

            return res.json();
        })
        .then(data => {

            if (!Array.isArray(data)) {
                logError("API không trả array", data);
                return;
            }

            if (data.length === 0) {
                logWarn("Không có ghế trong DB");
            }

            logInfo("SEATS DATA", data);

            seatsData = data;
            renderGrid();
        })
        .catch(err => {
            logError("Fetch lỗi", err);
            renderGrid(); // vẫn render ghế trống
        });
}


/* ================= RENDER ================= */
function renderGrid() {

    logInfo("RENDER GRID FROM SEATS");

    if (!seatsData || seatsData.length === 0) {
        document.getElementById('seatGrid').innerHTML =
            "<p style='color:orange'>Chưa có ghế → hãy bấm + để tạo</p>";
        return;
    }
    let rows = [...new Set(seatsData.map(s => s.rowSeat))].sort();

    let maxCol = Math.max(...seatsData.map(s => Number(s.colSeat)));

    logInfo("ROWS DETECTED", rows);
    logInfo("MAX COL", maxCol);

    let html = '';

    rows.forEach(row => {

        html += `<div class="seat-row">`;
        html += `<span class="row-label">${row}</span>`;

        for (let i = 1; i <= maxCol; i++) {

            let seat = seatsData.find(s =>
                s.rowSeat === row && Number(s.colSeat) === i
            );

            if (seat) {

                let seatClass = 'normal';
                if (seat.seatTypeID == 1) seatClass = 'vip';
                if (seat.seatTypeID == 3) seatClass = 'couple';
                if (seat.seatTypeID == 4) seatClass = 'maintenance';

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

                </div>`;
            } else {
                // 👉 ghế chưa tồn tại
                html += `
                <div class="seat empty"
                    onclick="createSeat('${row}', ${i})">
                    +
                </div>`;
            }
        }

        html += `</div>`;
    });

    document.getElementById('seatGrid').innerHTML = html;

    logInfo("RENDER DONE");
}


/* ================= EVENTS ================= */
function handleHover(el) {
    if (!isMouseDown) return;

    let id = el.dataset.id;

    if (!selectedSeats.includes(id)) {
        selectedSeats.push(id);
        el.classList.add('selected');
    }
}


/* ================= CREATE ================= */
function createSeat(row, col) {

    logInfo("CREATE SEAT", { row, col });

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
        logInfo("CREATE RESPONSE", res);
        loadSeats();
    })
    .catch(err => logError("Create lỗi", err));
}


/* ================= DELETE ================= */
function deleteSeat(id, e) {
    if (e) e.stopPropagation();

    logWarn("DELETE SEAT", id);

    fetch(`/admins/seat/ajax-delete/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res => res.json())
    .then(res => {
        logInfo("DELETE RESPONSE", res);
        loadSeats();
    })
    .catch(err => logError("Delete lỗi", err));
}


/* ================= INIT ================= */
logInfo("INIT PAGE");
loadSeats();

</script>

@endsection