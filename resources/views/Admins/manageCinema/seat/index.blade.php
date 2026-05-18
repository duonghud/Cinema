@extends('layouts.appAdmin')

@section('content')

<style>
    body {
        background: #0b1220;
        color: #ffffff;
    }

    .screen-bar {
        width: 60%;
        height: 40px;
        margin: 0 auto;
        background: linear-gradient(to bottom, #fbbf24, transparent);
        border-radius: 50%;
    }

    .seat-grid {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: center;
        overflow-x: auto;
        padding-bottom: 10px;
    }

    .seat-row {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .row-label {
        width: 30px;
        text-align: center;
        font-size: 13px;
        color: #9ca3af;
        font-weight: 700;
        flex-shrink: 0;
    }

    .seat,
    .empty-seat {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 10px;
        font-weight: 700;
        user-select: none;
        position: relative;
        transition: all .15s ease;
        flex-shrink: 0;
    }

    .seat {
        cursor: pointer;
    }

    .seat:hover,
    .empty-seat:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 10px 24px rgba(0, 0, 0, .35);
        z-index: 10;
    }

    .seat.selected {
        outline: 3px solid #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, .25);
        z-index: 20;
    }

    .seat.highlight {
        outline: 3px solid #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, .25);
    }

    /* Seat colors */
    .seat.normal      { background: #374151; color: #e5e7eb; }
    .seat.vip         { background: #f59e0b; color: #ffffff; }
    .seat.couple      { background: #ec4899; color: #ffffff; }
    .seat.maintenance { background: #7f1d1d; color: #fecaca; }

    .empty-seat {
        background: #1f2937;
        color: #4b5563;
        border: 2px solid #334155;
        box-shadow: inset 0 3px 8px rgba(0, 0, 0, .35);
        cursor: crosshair;
    }

    .empty-seat:hover {
        transform: translateY(-2px) scale(1.05);
        border-color: #22c55e;
        color: #22c55e;
        background: #273449;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, .18);
    }

    .delete-btn {
        position: absolute;
        top: -6px;
        right: -6px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #dc2626;
        color: #ffffff;
        font-size: 11px;
        display: none;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        z-index: 50;
    }

    .seat:hover .delete-btn {
        display: flex;
    }

    .toolbox-btn {
        min-width: 150px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 12px;
        font-weight: 700;
        padding: 10px 14px;
        transition: all .2s ease;
    }

    .toolbox-btn.active {
        transform: translateY(-2px);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .35);
    }

    .toolbox-icon {
        font-size: 18px;
    }

    .legend {
        display: flex;
        justify-content: center;
        gap: 24px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #d1d5db;
    }

    .box {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        display: inline-block;
        flex-shrink: 0;
    }

    .box.normal      { background: #374151; }
    .box.vip         { background: #f59e0b; }
    .box.couple      { background: #ec4899; }
    .box.maintenance { background: #7f1d1d; }

    #selectedCount {
        display: inline-block;
        background: #3b82f6;
        color: #ffffff;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        margin-left: 6px;
    }

    /* ── Room info badges ── */
    .room-info-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .room-info-label {
        font-size: 13px;
        font-weight: 600;
        color: #9ca3af;
        margin-right: 2px;
    }

    .room-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #1f2937;
        border: 1px solid #374151;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 13px;
        font-weight: 700;
        color: #e5e7eb;
        white-space: nowrap;
    }

    .room-badge .badge-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #6b7280;
        flex-shrink: 0;
    }

    .room-badge.b-rows   .badge-dot { background: #60a5fa; }
    .room-badge.b-cols   .badge-dot { background: #a78bfa; }
    .room-badge.b-total  .badge-dot { background: #34d399; }
</style>

{{-- PANEL ĐIỀU KHIỂN --}}
<div class="mb-4">
    <div class="bg-dark border border-secondary rounded-4 shadow-lg p-4">
        <div class="row g-3 align-items-end">

            {{-- Chọn phòng --}}
            <div class="col-md-3">
                <form method="GET" id="roomForm">
                    <label class="form-label text-white fw-semibold">Chọn phòng</label>
                    <select name="roomID"
                            class="form-select bg-secondary text-white border-0 rounded-3"
                            onchange="this.form.submit()">
                        @foreach($rooms as $roomItem)
                            <option value="{{ $roomItem->roomID }}"
                                {{ ($roomID ?? 0) == $roomItem->roomID ? 'selected' : '' }}>
                                Phòng {{ $roomItem->roomName }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Chọn loại để đổi --}}
            <div class="col-md-3">
                <label class="form-label text-white fw-semibold">Đổi sang loại</label>
                <select id="seatTypeSelect"
                        class="form-select bg-secondary text-white border-0 rounded-3">
                    @foreach($seatTypes as $type)
                        <option value="{{ $type->seatTypeID }}">
                            {{ $type->seatTypeName }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Ghế đã chọn --}}
            <div class="col-md-4">
                <label class="form-label text-white fw-semibold">
                    Ghế đã chọn
                    <span id="selectedCount">0</span>
                </label>
                <input type="text"
                       id="selectedSeatNames"
                       class="form-control bg-secondary text-white border-0 rounded-3"
                       readonly
                       value="Chưa chọn ghế">
            </div>

            {{-- Cập nhật --}}
            <div class="col-md-2">
                <button type="button"
                        class="btn btn-primary w-100 rounded-3 fw-semibold"
                        onclick="submitSeatUpdate()">
                    Cập nhật
                </button>
            </div>

            {{-- TOOLBOX --}}
            <div class="col-12 mt-3">
                <label class="form-label text-white fw-semibold mb-2">
                    🧰 Hộp công cụ (Toolbox)
                </label>

                <div class="d-flex flex-wrap gap-2">
                    <button type="button"
                            class="btn btn-outline-light toolbox-btn active"
                            onclick="setCreateSeatType(2, this)">
                        <span class="toolbox-icon">💺</span>
                        Ghế thường
                    </button>

                    <button type="button"
                            class="btn btn-outline-warning toolbox-btn"
                            onclick="setCreateSeatType(1, this)">
                        <span class="toolbox-icon">👑</span>
                        Ghế VIP
                    </button>

                    <button type="button"
                            class="btn btn-outline-danger toolbox-btn"
                            onclick="setCreateSeatType(3, this)">
                        <span class="toolbox-icon">💕</span>
                        Ghế đôi
                    </button>

                    <button type="button"
                            class="btn btn-outline-secondary toolbox-btn"
                            onclick="setCreateSeatType(4, this)">
                        <span class="toolbox-icon">🛠️</span>
                        Bảo trì
                    </button>
                </div>

                <small class="text-info d-block mt-2">
                    Chọn loại ghế ở Toolbox, sau đó click hoặc kéo chuột trên sơ đồ để gán loại ghế.
                    Các ô chưa tạo sẽ hiển thị như ghế thường ở trạng thái chìm.
                </small>

                {{-- THÔNG TIN PHÒNG --}}
                <div class="room-info-row">
                    <span class="room-info-label">Thông tin phòng</span>

                    <span class="room-badge b-rows">
                        <span class="badge-dot"></span>
                        <span id="statRows">—</span> hàng
                    </span>

                    <span class="room-badge b-cols">
                        <span class="badge-dot"></span>
                        <span id="statCols">—</span> ghế/hàng
                    </span>

                    <span class="room-badge b-total">
                        <span class="badge-dot"></span>
                        <span id="statTotal">—</span> ghế
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- FORM ẨN CẬP NHẬT HÀNG LOẠT --}}
<form id="updateSeatForm"
      method="POST"
      action="{{ route('seat.updateMultiple') }}">
    @csrf
    <div id="seatIDsContainer"></div>
    <input type="hidden" name="seatTypeID" id="seatTypeInput">
</form>

{{-- MÀN HÌNH --}}
<div class="text-center mb-3">
    <div class="screen-bar"></div>
    <p class="text-warning mt-2 mb-0" style="letter-spacing: 4px; font-size: 12px;">
        MÀN HÌNH
    </p>
</div>

{{-- SƠ ĐỒ GHẾ --}}
<div id="seatGrid" class="seat-grid mt-4"></div>

{{-- CHÚ THÍCH --}}
<div class="legend">
    <div class="legend-item"><span class="box normal"></span> Ghế thường</div>
    <div class="legend-item"><span class="box vip"></span> Ghế VIP</div>
    <div class="legend-item"><span class="box couple"></span> Ghế đôi</div>
    <div class="legend-item"><span class="box maintenance"></span> Ghế bảo trì</div>
</div>

<script>
const currentRoom = "{{ $roomID ?? 0 }}";

let seatsData        = [];
let selectedSeats    = [];
let isMouseDown      = false;
let createSeatTypeID = 2; // mặc định ghế thường
let createdInDrag    = new Set();
let toggledInDrag    = new Set();

const DEFAULT_ROWS = parseInt('{{ $rowsCount ?? 10 }}');
const DEFAULT_COLS = parseInt('{{ $colsCount ?? 12 }}');

const TYPE_CLASS = {
    1: 'vip',
    2: 'normal',
    3: 'couple',
    4: 'maintenance'
};

// ===== Mouse state =====
document.addEventListener('mousedown', function (e) {
    if (e.button === 0) {
        isMouseDown = true;
        createdInDrag.clear();
        toggledInDrag.clear();
    }
});

document.addEventListener('mouseup', function () {
    isMouseDown = false;
    createdInDrag.clear();
    toggledInDrag.clear();
});

// ===== Toolbox =====
function setCreateSeatType(typeID, button) {
    createSeatTypeID = typeID;
    document.querySelectorAll('.toolbox-btn').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
}

// ===== Load seats =====
function loadSeats() {
    if (!currentRoom || currentRoom == 0) {
        document.getElementById('seatGrid').innerHTML =
            '<p class="text-warning">Vui lòng chọn phòng.</p>';
        return;
    }

    fetch(`/admins/seat/ajax/${currentRoom}`)
        .then(res => res.json())
        .then(data => {
            seatsData = data || [];
            renderGrid();
            updateRoomInfo();
        })
        .catch(() => {
            document.getElementById('seatGrid').innerHTML =
                '<p class="text-danger">Không tải được dữ liệu ghế.</p>';
        });
}

// ===== Update room info badges =====
function updateRoomInfo() {
    document.getElementById('statRows').textContent  = DEFAULT_ROWS;
    document.getElementById('statCols').textContent  = DEFAULT_COLS;
    document.getElementById('statTotal').textContent = seatsData.length;
}

// ===== Render grid =====
function renderGrid() {
    const seatGrid = document.getElementById('seatGrid');
    let html = '';

    for (let i = 0; i < DEFAULT_ROWS; i++) {
        const row = String.fromCharCode(65 + i);
        html += `<div class="seat-row"><span class="row-label">${row}</span>`;

        for (let col = 1; col <= DEFAULT_COLS; col++) {
            const seat = seatsData.find(s =>
                String(s.rowSeat) === String(row) &&
                parseInt(s.colSeat) === col
            );

            if (seat) {
                // Ghế đã tồn tại — hiển thị đúng màu theo loại
                const typeClass = TYPE_CLASS[seat.seatTypeID] || 'normal';
                html += `
                    <div class="seat ${typeClass} paint-cell"
                         data-row="${row}"
                         data-col="${col}"
                         data-id="${seat.seatID}"
                         data-type="${seat.seatTypeID}"
                         data-name="${row}${col}">
                        ${row}${col}
                        <span class="delete-btn"
                              onmousedown="event.stopPropagation()"
                              onclick="deleteSeat(${seat.seatID}, event)">×</span>
                    </div>
                `;
            } else {
                // Ô trống — hiển thị chìm
                html += `
                    <div class="empty-seat paint-cell"
                         data-row="${row}"
                         data-col="${col}"
                         data-id=""
                         data-type=""
                         data-name="">
                        +
                    </div>
                `;
            }
        }

        html += `</div>`;
    }

    seatGrid.innerHTML = html;
    bindPaintEvents();
    updatePanel();
}

// ===== Bind paint events (click + drag) =====
function bindPaintEvents() {
    document.querySelectorAll('.paint-cell').forEach(cell => {
        cell.addEventListener('mousedown', function (e) {
            if (e.target.classList.contains('delete-btn')) return;
            applyToolToCell(this);
        });

        cell.addEventListener('mouseenter', function () {
            if (isMouseDown) applyToolToCell(this);
        });
    });
}

// ===== Apply toolbox action to a cell =====
function applyToolToCell(cell) {
    const key = `${cell.dataset.row}-${cell.dataset.col}`;
    if (createdInDrag.has(key)) return;
    createdInDrag.add(key);

    const seatID      = cell.dataset.id;
    const currentType = parseInt(cell.dataset.type || 0);
    const rowSeat     = cell.dataset.row;
    const colSeat     = cell.dataset.col;

    if (seatID) {
        if (currentType === createSeatTypeID) return;

        fetch('/admins/seat/ajax-update-type', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                seatID: parseInt(seatID),
                seatTypeID: createSeatTypeID
            })
        })
        .then(async response => {
            let data = {};
            try { data = await response.json(); } catch (e) {}
            if (!response.ok) throw new Error(data.message || 'Không thể cập nhật loại ghế.');
            return data;
        })
        .then(() => loadSeats())
        .catch(error => console.error(error));

        return;
    }

    createSeatAt(rowSeat, colSeat);
}

// ===== Select seats =====
function toggleSeat(el) {
    const seatID   = el.dataset.id;
    const seatName = el.dataset.name;
    const seatType = el.dataset.type;
    const index    = selectedSeats.findIndex(s => s.id == seatID);

    if (index >= 0) {
        selectedSeats.splice(index, 1);
        el.classList.remove('selected');
    } else {
        selectedSeats.push({ id: seatID, name: seatName, type: seatType });
        el.classList.add('selected');
    }

    updatePanel();
    applySeatFilter();
}

// ===== Highlight theo loại mục tiêu =====
function applySeatFilter() {
    document.querySelectorAll('.seat').forEach(seat => seat.classList.remove('highlight'));
    if (selectedSeats.length === 0) return;

    const targetType = parseInt(document.getElementById('seatTypeSelect').value);

    document.querySelectorAll('.seat').forEach(seat => {
        if (parseInt(seat.dataset.type) === targetType) seat.classList.add('highlight');
    });

    selectedSeats.forEach(selected => {
        const el = document.querySelector(`.seat[data-id="${selected.id}"]`);
        if (el) { el.classList.remove('highlight'); el.classList.add('selected'); }
    });
}

// ===== Update panel =====
function updatePanel() {
    document.getElementById('selectedCount').textContent = selectedSeats.length;

    document.getElementById('selectedSeatNames').value =
        selectedSeats.length > 0
            ? selectedSeats.map(s => s.name).join(', ')
            : 'Chưa chọn ghế';

    const container = document.getElementById('seatIDsContainer');
    container.innerHTML = '';
    selectedSeats.forEach(seat => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'seatIDs[]';
        input.value = seat.id;
        container.appendChild(input);
    });

    if (selectedSeats.length > 0) {
        document.getElementById('seatTypeSelect').value = selectedSeats[0].type;
    }
}

// ===== Create seat =====
function createSeatAt(rowSeat, colSeat) {
    if (!currentRoom || currentRoom == 0) return;

    fetch('/admins/seat/ajax-add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            roomID: parseInt(currentRoom),
            rowSeat: rowSeat,
            colSeat: parseInt(colSeat),
            seatTypeID: createSeatTypeID
        })
    })
    .then(async response => {
        let data = {};
        try { data = await response.json(); } catch (e) {}
        if (!response.ok) throw new Error(data.message || 'Không thể tạo ghế.');
        return data;
    })
    .then(() => loadSeats())
    .catch(error => console.error(error));
}

// ===== Submit bulk update =====
function submitSeatUpdate() {
    if (selectedSeats.length === 0) {
        alert('Vui lòng chọn ít nhất 1 ghế.');
        return;
    }
    document.getElementById('seatTypeInput').value =
        document.getElementById('seatTypeSelect').value;
    document.getElementById('updateSeatForm').submit();
}

// ===== Delete seat =====
function deleteSeat(id, event) {
    event.stopPropagation();
    if (!confirm('Bạn có chắc muốn xoá ghế này?')) return;

    fetch(`/admins/seat/ajax-delete/${id}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(async response => {
        let data = {};
        try { data = await response.json(); } catch (e) {}
        if (!response.ok) throw new Error(data.message || 'Không thể xoá ghế.');
        return data;
    })
    .then(() => {
        selectedSeats = selectedSeats.filter(seat => seat.id != id);
        loadSeats();
    })
    .catch(error => {
        console.error(error);
        alert(error.message || 'Xoá ghế thất bại.');
    });
}

// ===== Init =====
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('seatTypeSelect')
        .addEventListener('change', applySeatFilter);
    loadSeats();
});
</script>

@endsection