@extends('layouts.appAdmin')

@section('content')
<style>
    :root {
        --bg-deep: #080f1e;
        --bg-card: #0f1a2e;
        --bg-panel: #111827;
        --border: #1e2d45;
        --muted: #6b7280;
        --accent: #3b82f6;
    }

    body {
        background: var(--bg-deep);
        color: #e5e7eb;
    }

    /* CONTAINER */
    .room-layout {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 20px;
        align-items: start;
    }

    @media(max-width:1100px) {
        .room-layout {
            grid-template-columns: 1fr;
        }
    }

    /* PANEL */
    .panel-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 18px;
    }

    .panel-title {
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 14px;
        font-weight: 700;
    }

    /* INPUT */
    .ctrl {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        color: #e5e7eb;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        width: 100%;
    }

    .ctrl:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
    }

    .ctrl option {
        background: #1f2937;
    }

    .form-label {
        font-size: 12px;
        color: #9ca3af;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .text-danger {
        font-size: 12px;
    }

    /* BUTTON */
    .btn-save {
        background: var(--accent);
        border: none;
        color: #fff;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 700;
        transition: .2s;
    }

    .btn-save:hover {
        background: #2563eb;
    }

    .btn-back {
        background: #111827;
        border: 1px solid var(--border);
        color: #9ca3af;
        border-radius: 10px;
        padding: 10px 18px;
        text-decoration: none;
        font-weight: 600;
    }

    .btn-back:hover {
        background: #1f2937;
        color: #fff;
    }

    /* TYPE CARD */
    .type-card {
        background: #111827;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 14px;
    }

    .type-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .type-vip {
        color: #fbbf24;
    }

    .type-normal {
        color: #60a5fa;
    }

    .type-double {
        color: #f472b6;
    }

    /* ALERT */
    .alert-custom {
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 18px;
        font-size: 13px;
        font-weight: 600;
    }

    .alert-danger-custom {
        background: rgba(239, 68, 68, .12);
        border: 1px solid rgba(239, 68, 68, .3);
        color: #fca5a5;
    }

    .alert-success-custom {
        background: rgba(34, 197, 94, .12);
        border: 1px solid rgba(34, 197, 94, .3);
        color: #86efac;
    }

    /* STATS */
    .stat-pills {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .stat-pill {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stat-pill .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    /* SCREEN */
    .screen-wrap {
        text-align: center;
        margin-bottom: 16px;
    }

    .screen-bar {
        width: 50%;
        height: 6px;
        margin: 0 auto 6px;
        background: linear-gradient(to right, transparent, #fbbf24, transparent);
        border-radius: 999px;
        box-shadow: 0 0 20px rgba(251, 191, 36, .4);
    }

    .screen-label {
        font-size: 10px;
        letter-spacing: .18em;
        color: #fbbf24;
        text-transform: uppercase;
    }

    /* GRID */
    .preview-container {
        overflow: auto;
        max-height: 650px;
        border-radius: 12px;
        background: #0b1220;
        border: 1px solid var(--border);
        padding: 14px;
    }

    .seat-grid {
        display: flex;
        flex-direction: column;
        gap: 5px;
        align-items: center;
        min-width: max-content;
    }

    .preview-row {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .preview-label {
        width: 20px;
        text-align: center;
        font-size: 11px;
        color: #6b7280;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* SEAT */
    .preview-seat {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        font-weight: 800;
        border: 2px solid transparent;
        transition: .15s;
        flex-shrink: 0;
    }

    .preview-seat:hover {
        transform: translateY(-1px);
    }

    /* NORMAL */
    .preview-seat.normal {
        background: #1e3a5f;
        color: #93c5fd;
        border-color: #1e40af;
    }

    /* VIP */
    .preview-seat.vip {
        background: #92400e;
        color: #fcd34d;
        border-color: #d97706;
    }

    /* DOUBLE */
    .preview-seat.double {
        background: #831843;
        color: #f9a8d4;
        border-color: #db2777;
        width: 60px;
    }

    /* EMPTY */
    .preview-empty {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: #0f172a;
        border: 1px dashed #1e293b;
    }

    /* LEGEND */
    .legend {
        display: flex;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        color: #9ca3af;
    }

    .legend-box {
        width: 18px;
        height: 18px;
        border-radius: 5px;
    }
</style>

<div class="container-fluid mt-4 px-4">

    {{-- ERROR DEBUG --}}
    @if ($errors->any())
    <div class="alert-custom alert-danger-custom">
        <div class="fw-bold mb-2">
            Có lỗi xảy ra:
        </div>

        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert-custom alert-success-custom">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('screeningRoom.store') }}" method="POST" id="roomForm">
        @csrf

        <div class="room-layout">

            {{-- LEFT --}}
            <div>

                {{-- INFO --}}
                <div class="panel-card">

                    <div class="panel-title">
                        Thông tin phòng
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Tên phòng
                        </label>

                        <input type="text"
                            name="roomName"
                            value="{{ old('roomName') }}"
                            class="ctrl @error('roomName') is-invalid @enderror">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">
                                Số hàng
                            </label>

                            <input type="number"
                                name="rows"
                                min="1"
                                max="26"
                                value="{{ old('rows',5) }}"
                                class="ctrl">
                        </div>

                        <div class="col-6 mb-3">
                            <label class="form-label">
                                Số cột
                            </label>

                            <input type="number"
                                name="cols"
                                min="1"
                                max="50"
                                value="{{ old('cols',10) }}"
                                class="ctrl">
                        </div>
                    </div>

                    <div class="mt-2">
                        <div class="small text-secondary">
                            Tổng số ghế tối đa:
                            <b id="maxSeatsText">0</b>
                        </div>

                        <div class="small text-secondary">
                            Ghế đang sử dụng:
                            <b id="usedSeatsText">0</b>
                        </div>

                        <div class="small mt-1 fw-bold" id="seatWarning"></div>
                    </div>
                </div>


                <div class="type-title type-vip">
                    Ghế VIP
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Loại ghế
                    </label>

                    <select name="vipSeatTypeID" class="ctrl">
                        @foreach($seatTypes as $seatType)
                        <option value="{{ $seatType->seatTypeID }}"
                            {{ old('vipSeatTypeID', $seatTypes->firstWhere('seatTypeName', 'like', '%VIP%')?->seatTypeID) == $seatType->seatTypeID ? 'selected' : '' }}>
                            {{ $seatType->seatTypeName }} ({{ number_format($seatType->price) }}đ)
                        </option>
                        @endforeach
                    </select>

                    <label class="form-label">
                        Số lượng ghế
                    </label>

                    <input type="number"
                        name="vipSeats"
                        min="0"
                        value="{{ old('vipSeats',0) }}"
                        class="ctrl">
                </div>

                <div class="type-title type-normal">
                    Ghế thường
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Loại ghế
                    </label>

                    <select name="normalSeatTypeID" class="ctrl">
                        @foreach($seatTypes as $seatType)
                        <option value="{{ $seatType->seatTypeID }}"
                            {{ old('normalSeatTypeID', $seatTypes->firstWhere('seatTypeName', 'like', '%thường%')?->seatTypeID ?? $seatTypes->firstWhere('seatTypeName', 'like', '%normal%')?->seatTypeID) == $seatType->seatTypeID ? 'selected' : '' }}>
                            {{ $seatType->seatTypeName }} ({{ number_format($seatType->price) }}đ)
                        </option>
                        @endforeach
                    </select>
                    <label class="form-label">
                        Số lượng ghế
                    </label>

                    <input type="number"
                        name="normalSeats"
                        min="0"
                        value="{{ old('normalSeats',0) }}"
                        class="ctrl">
                </div>

                <div class="type-title type-double">
                    Ghế đôi
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Loại ghế
                    </label>

                    <select name="doubleSeatTypeID" class="ctrl">
                        @foreach($seatTypes as $seatType)
                        <option value="{{ $seatType->seatTypeID }}"
                            {{ old('doubleSeatTypeID', $seatTypes->firstWhere('seatTypeName', 'like', '%đôi%')?->seatTypeID ?? $seatTypes->firstWhere('seatTypeName', 'like', '%couple%')?->seatTypeID) == $seatType->seatTypeID ? 'selected' : '' }}>
                            {{ $seatType->seatTypeName }} ({{ number_format($seatType->price) }}đ)
                        </option>
                        @endforeach
                    </select>
                    <label class="form-label">
                        Số lượng ghế đôi
                    </label>

                    <input type="number"
                        name="doubleSeats"
                        min="0"
                        value="{{ old('doubleSeats',0) }}"
                        class="ctrl">
                </div>
                {{-- SCREEN TYPE --}}
                <div class="panel-card">

                    <div class="panel-title">
                        Loại phòng chiếu
                    </div>

                    <select name="screenTypeID" class="ctrl">

                        <option value="">
                            -- Chọn loại phòng --
                        </option>

                        @foreach($screenTypes as $type)
                        <option value="{{ $type->screenTypeID }}">
                            {{ $type->name }}
                        </option>
                        @endforeach

                    </select>
                </div>

                {{-- BUTTON --}}
                <div class="d-flex justify-content-between pb-4">

                    <a href="{{ route('screeningRoom.index') }}"
                        class="btn-back">
                        ← Quay lại
                    </a>

                    <button type="submit"
                        class="btn-save"
                        id="submitBtn">
                        Lưu phòng
                    </button>

                </div>

            </div>

            {{-- RIGHT --}}
            <div>

                <div class="panel-card">

                    <div class="panel-title">
                        Xem trước sơ đồ ghế
                    </div>

                    {{-- STATS --}}
                    <div class="stat-pills">

                        <span class="stat-pill">
                            <span class="dot" style="background:#3b82f6"></span>
                            Thường:
                            <b id="previewNormalCount">0</b>
                        </span>

                        <span class="stat-pill">
                            <span class="dot" style="background:#f59e0b"></span>
                            VIP:
                            <b id="previewVipCount">0</b>
                        </span>

                        <span class="stat-pill">
                            <span class="dot" style="background:#ec4899"></span>
                            Đôi:
                            <b id="previewDoubleCount">0</b>
                        </span>

                        <span class="stat-pill">
                            <span class="dot" style="background:#6b7280"></span>
                            Tổng:
                            <b id="previewTotalCount">0</b>
                        </span>

                    </div>

                    {{-- SCREEN --}}
                    <div class="screen-wrap">
                        <div class="screen-bar"></div>

                        <div class="screen-label">
                            Màn hình
                        </div>
                    </div>

                    {{-- GRID --}}
                    <div class="preview-container">
                        <div id="previewGrid" class="seat-grid"></div>
                    </div>

                    {{-- LEGEND --}}
                    <div class="legend">

                        <div class="legend-item">
                            <div class="legend-box"
                                style="background:#1e3a5f;border:2px solid #1e40af"></div>
                            Ghế thường
                        </div>

                        <div class="legend-item">
                            <div class="legend-box"
                                style="background:#92400e;border:2px solid #d97706"></div>
                            Ghế VIP
                        </div>

                        <div class="legend-item">
                            <div class="legend-box"
                                style="background:#831843;border:2px solid #db2777"></div>
                            Ghế đôi
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

<script>
    const rowsInput = document.querySelector('input[name="rows"]');
    const colsInput = document.querySelector('input[name="cols"]');

    const vipSeatsInput = document.querySelector('input[name="vipSeats"]');
    const normalSeatsInput = document.querySelector('input[name="normalSeats"]');
    const doubleSeatsInput = document.querySelector('input[name="doubleSeats"]');

    const previewGrid = document.getElementById('previewGrid');
    const submitBtn = document.getElementById('submitBtn');

    function generatePreview() {
        previewGrid.innerHTML = '';

        const rows = parseInt(rowsInput.value || 0);
        const cols = parseInt(colsInput.value || 0);

        let vipSeats = parseInt(vipSeatsInput.value || 0);
        let normalSeats = parseInt(normalSeatsInput.value || 0);
        let doubleSeats = parseInt(doubleSeatsInput.value || 0);

        // Tổng ô thực tế (ghế đôi chiếm 2 ô)
        const totalUsedSlots = vipSeats + normalSeats + (doubleSeats * 2);
        const maxSeats = rows * cols;

        document.getElementById('previewVipCount').innerText = vipSeats;
        document.getElementById('previewNormalCount').innerText = normalSeats;
        document.getElementById('previewDoubleCount').innerText = doubleSeats;
        document.getElementById('previewTotalCount').innerText = totalUsedSlots;
        document.getElementById('maxSeatsText').innerText = maxSeats;
        document.getElementById('usedSeatsText').innerText = totalUsedSlots;

        const warning = document.getElementById('seatWarning');
        if (totalUsedSlots > maxSeats) {
            warning.innerHTML = `❌ Vượt quá số ghế cho phép (${totalUsedSlots}/${maxSeats})`;
            warning.style.color = '#f87171';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '.5';
            submitBtn.style.cursor = 'not-allowed';
        } else {
            warning.innerHTML = `✔ Hợp lệ`;
            warning.style.color = '#4ade80';
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        }

        // Build queue: VIP → Normal → Double (không shuffle, giống controller)
        const queue = [];
        for (let i = 0; i < vipSeats; i++) queue.push('vip');
        for (let i = 0; i < normalSeats; i++) queue.push('normal');
        for (let i = 0; i < doubleSeats; i++) queue.push('double');

        let qi = 0; // queue index

        for (let r = 0; r < rows; r++) {
            const rowEl = document.createElement('div');
            rowEl.className = 'preview-row';

            const label = document.createElement('div');
            label.className = 'preview-label';
            label.innerText = String.fromCharCode(65 + r);
            rowEl.appendChild(label);

            let c = 1;
            while (c <= cols) {
                if (qi < queue.length) {
                    const type = queue[qi];

                    if (type === 'double') {
                        // Ghế đôi cần 2 cột liên tiếp
                        if (c + 1 <= cols) {
                            const seat = document.createElement('div');
                            seat.className = 'preview-seat double';
                            seat.innerText = `${String.fromCharCode(65+r)}${c}-${c+1}`;
                            seat.title = `${String.fromCharCode(65+r)}${c} - ${String.fromCharCode(65+r)}${c+1}`;
                            rowEl.appendChild(seat);
                            qi++;
                            c += 2; // chiếm 2 cột
                            continue;
                        } else {
                            // Không đủ 2 cột → xuống hàng mới (fill empty cho cột cuối)
                            const empty = document.createElement('div');
                            empty.className = 'preview-empty';
                            rowEl.appendChild(empty);
                            c++;
                            continue;
                        }
                    }

                    // VIP hoặc Normal
                    const seat = document.createElement('div');
                    seat.className = `preview-seat ${type === 'vip' ? 'vip' : 'normal'}`;
                    seat.innerText = `${String.fromCharCode(65+r)}${c}`;
                    rowEl.appendChild(seat);
                    qi++;
                    c++;

                } else {
                    // Hết queue → ô trống
                    const empty = document.createElement('div');
                    empty.className = 'preview-empty';
                    rowEl.appendChild(empty);
                    c++;
                }
            }

            previewGrid.appendChild(rowEl);
        }
    }

    [
        rowsInput,
        colsInput,
        vipSeatsInput,
        normalSeatsInput,
        doubleSeatsInput
    ].forEach(el => {

        el.addEventListener('input', generatePreview);

    });

    generatePreview();

    document.getElementById('roomForm')
        .addEventListener('submit', function(e) {

            const rows = parseInt(rowsInput.value || 0);
            const cols = parseInt(colsInput.value || 0);

            const vipSeats =
                parseInt(vipSeatsInput.value || 0);

            const normalSeats =
                parseInt(normalSeatsInput.value || 0);

            const doubleSeats =
                parseInt(doubleSeatsInput.value || 0);

            const total =
                vipSeats +
                normalSeats +
                (doubleSeats * 2);

            const maxSeats = rows * cols;

            if (total > maxSeats) {

                e.preventDefault();

                alert(
                    `Tổng số ghế (${total}) vượt quá số ghế tối đa (${maxSeats})`
                );

                return false;
            }
        });
</script>
@endsection
