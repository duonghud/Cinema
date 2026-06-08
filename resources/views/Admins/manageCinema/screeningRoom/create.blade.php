@extends('layouts.appAdmin')

@section('content')
<style>
    :root {
        --bg-deep:   #f0f4f8;
        --bg-card:   #ffffff;
        --bg-panel:  #f8fafc;
        --border:    #e2e8f0;
        --muted:     #94a3b8;
        --accent:    #3b82f6;
        --text-main: #1e293b;
        --text-sub:  #64748b;
    }

    body { background: var(--bg-deep); color: var(--text-main); }

    .room-layout {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 20px;
        align-items: start;
    }

    .preview-sticky {
        position: sticky;
        top: 16px;
        max-height: calc(100vh - 32px);
        display: flex;
        flex-direction: column;
    }

    .preview-sticky .panel-card {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        margin-bottom: 0;
        overflow: hidden;
    }

    @media(max-width:1100px) {
        .room-layout    { grid-template-columns: 1fr; }
        .preview-sticky { position: static; max-height: none; }
    }

    .panel-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 18px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }

    .panel-title {
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 14px;
        font-weight: 700;
    }

    .ctrl {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        color: var(--text-main);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        width: 100%;
    }

    .ctrl:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(59,130,246,.12);
        background: #fff;
    }

    .ctrl option { background: #fff; }

    .form-label {
        font-size: 12px;
        color: var(--text-sub);
        margin-bottom: 6px;
        font-weight: 600;
    }

    .text-danger { font-size: 12px; }

    .btn-save {
        background: var(--accent);
        border: none;
        color: #fff;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 700;
        transition: .2s;
        cursor: pointer;
    }

    .btn-save:hover { background: #2563eb; }

    .btn-back {
        background: #fff;
        border: 1px solid var(--border);
        color: var(--text-sub);
        border-radius: 10px;
        padding: 10px 18px;
        text-decoration: none;
        font-weight: 600;
    }

    .btn-back:hover { background: var(--bg-deep); color: var(--text-main); }

    .type-card {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .type-title { font-size: 13px; font-weight: 700; margin-bottom: 12px; }
    .type-vip    { color: #d97706; }
    .type-normal { color: #2563eb; }
    .type-double { color: #db2777; }

    .alert-custom {
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 18px;
        font-size: 13px;
        font-weight: 600;
    }

    .alert-danger-custom {
        background: rgba(239,68,68,.08);
        border: 1px solid rgba(239,68,68,.25);
        color: #b91c1c;
    }

    .alert-success-custom {
        background: rgba(34,197,94,.08);
        border: 1px solid rgba(34,197,94,.25);
        color: #15803d;
    }

    .stat-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }

    .stat-pill {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stat-pill .dot { width: 8px; height: 8px; border-radius: 50%; }

    .screen-wrap { text-align: center; margin-bottom: 16px; }

    .screen-bar {
        width: 50%;
        height: 5px;
        margin: 0 auto 6px;
        background: linear-gradient(to right, transparent, #f59e0b, transparent);
        border-radius: 999px;
        box-shadow: 0 0 14px rgba(245,158,11,.35);
    }

    .screen-label {
        font-size: 10px;
        letter-spacing: .18em;
        color: #b45309;
        text-transform: uppercase;
        font-weight: 700;
    }

    .preview-container {
        overflow: auto;
        flex: 1;
        min-height: 120px;
        border-radius: 12px;
        background: #f1f5f9;
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

    .preview-row { display: flex; align-items: center; gap: 5px; }

    .preview-label {
        width: 20px;
        text-align: center;
        font-size: 11px;
        color: var(--muted);
        font-weight: 700;
        flex-shrink: 0;
    }

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

    .preview-seat:hover { transform: translateY(-1px); }

    .preview-seat.normal { background: #dbeafe; color: #1d4ed8; border-color: #93c5fd; }
    .preview-seat.vip    { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
    .preview-seat.double { background: #fce7f3; color: #9d174d; border-color: #f9a8d4; }

    .preview-empty {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: #e2e8f0;
        border: 1px dashed #cbd5e1;
    }

    .legend { display: flex; justify-content: center; gap: 16px; flex-wrap: wrap; margin-top: 16px; }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        color: var(--text-sub);
        font-weight: 600;
    }

    .legend-box { width: 18px; height: 18px; border-radius: 5px; }

    .action-bar {
        position: sticky;
        top: 0;
        z-index: 100;
        background: rgba(240,244,248,.92);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid var(--border);
        padding: 10px 0;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .action-bar .room-title-preview { font-size: 14px; font-weight: 700; color: var(--text-sub); }
    .action-bar .room-title-preview span { color: var(--text-main); }

    .form-col { min-height: 0; }
</style>

<div class="container-fluid mt-4 px-4">

    @if ($errors->any())
    <div class="alert-custom alert-danger-custom">
        <div class="fw-bold mb-2">Có lỗi xảy ra:</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert-custom alert-success-custom">{{ session('success') }}</div>
    @endif

    <form action="{{ route('screeningRoom.store') }}" method="POST" id="roomForm">
        @csrf

        <div class="action-bar">
            <div class="room-title-preview">Tạo phòng mới: <span id="barRoomName">—</span></div>
            <div class="d-flex align-items-center gap-2">
                <div class="small" id="barSeatSummary" style="color:var(--text-sub)"></div>
                <a href="{{ route('screeningRoom.index') }}" class="btn-back" style="padding:7px 14px;font-size:13px">← Quay lại</a>
                <button type="submit" class="btn-save" id="submitBtn" style="padding:7px 16px;font-size:13px">Lưu phòng</button>
            </div>
        </div>

        <div class="room-layout">

            {{-- LEFT --}}
            <div class="form-col">

                <div class="panel-card">
                    <div class="panel-title">Thông tin phòng</div>

                    <div class="mb-3">
                        <label class="form-label">Tên phòng</label>
                        <input type="text" name="roomName" id="roomNameInput"
                               value="{{ old('roomName') }}"
                               class="ctrl @error('roomName') is-invalid @enderror">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Số hàng</label>
                            <input type="number" name="rows" id="rowsInput" min="1" max="26"
                                   value="{{ old('rows', 5) }}" class="ctrl">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Số cột</label>
                            <input type="number" name="cols" id="colsInput" min="1" max="50"
                                   value="{{ old('cols', 10) }}" class="ctrl">
                        </div>
                    </div>

                    <div class="mt-2">
                        <div class="small text-secondary">Tổng số ô tối đa: <b id="maxSeatsText">0</b></div>
                        <div class="small text-secondary">Ghế đang sử dụng: <b id="usedSeatsText">0</b></div>
                        <div class="small mt-1 fw-bold" id="seatWarning"></div>
                    </div>
                </div>

                {{-- GHẾ VIP --}}
                <div class="type-card">
                    <div class="type-title type-vip">Ghế VIP</div>
                    <div class="mb-3">
                        <label class="form-label">Loại ghế</label>
                        <select name="vipSeatTypeID" class="ctrl">
                            @foreach($seatTypes as $seatType)
                            {{--
                                Controller truyền $defaultVipTypeID đã tìm đúng theo tên.
                                Mỗi dropdown dùng biến riêng → không bị chọn cùng 1 loại.
                            --}}
                            <option value="{{ $seatType->seatTypeID }}"
                                {{ old('vipSeatTypeID', $defaultVipTypeID) == $seatType->seatTypeID ? 'selected' : '' }}>
                                {{ $seatType->seatTypeName }} ({{ number_format($seatType->price) }}đ)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Số lượng ghế</label>
                        <input type="number" name="vipSeats" id="vipSeatsInput" min="0"
                               value="{{ old('vipSeats', 0) }}" class="ctrl">
                    </div>
                </div>

                {{-- GHẾ THƯỜNG --}}
                <div class="type-card">
                    <div class="type-title type-normal">Ghế thường</div>
                    <div class="mb-3">
                        <label class="form-label">Loại ghế</label>
                        <select name="normalSeatTypeID" class="ctrl">
                            @foreach($seatTypes as $seatType)
                            <option value="{{ $seatType->seatTypeID }}"
                                {{ old('normalSeatTypeID', $defaultNormalTypeID) == $seatType->seatTypeID ? 'selected' : '' }}>
                                {{ $seatType->seatTypeName }} ({{ number_format($seatType->price) }}đ)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Số lượng ghế</label>
                        <input type="number" name="normalSeats" id="normalSeatsInput" min="0"
                               value="{{ old('normalSeats', 0) }}" class="ctrl">
                    </div>
                </div>

                {{-- GHẾ ĐÔI --}}
                <div class="type-card">
                    <div class="type-title type-double">Ghế đôi</div>
                    <div class="mb-3">
                        <label class="form-label">Loại ghế</label>
                        <select name="doubleSeatTypeID" class="ctrl">
                            @foreach($seatTypes as $seatType)
                            <option value="{{ $seatType->seatTypeID }}"
                                {{ old('doubleSeatTypeID', $defaultDoubleTypeID) == $seatType->seatTypeID ? 'selected' : '' }}>
                                {{ $seatType->seatTypeName }} ({{ number_format($seatType->price) }}đ)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Số lượng ghế đôi <span style="color:#db2777;font-size:11px">(bắt buộc số chẵn)</span></label>
                        <input type="number" name="doubleSeats" min="0" step="2"
                               value="{{ old('doubleSeats', 0) }}" class="ctrl" id="doubleSeatsInput">
                        <div id="doubleSeatsError" style="color:#dc2626;font-size:12px;margin-top:4px;display:none">
                            Số ghế đôi phải là số chẵn
                        </div>
                    </div>
                </div>

                {{-- LOẠI PHÒNG --}}
                <div class="panel-card">
                    <div class="panel-title">Loại phòng chiếu</div>
                    <select name="screenTypeID" class="ctrl">
                        <option value="">-- Chọn loại phòng --</option>
                        @foreach($screenTypes as $type)
                        <option value="{{ $type->screenTypeID }}"
                            {{ old('screenTypeID') == $type->screenTypeID ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>{{-- /form-col --}}

            {{-- RIGHT --}}
            <div class="preview-sticky">
                <div class="panel-card">
                    <div class="panel-title">Xem trước sơ đồ ghế</div>

                    <div class="stat-pills">
                        <span class="stat-pill">
                            <span class="dot" style="background:#3b82f6"></span>
                            Thường: <b id="previewNormalCount">0</b>
                        </span>
                        <span class="stat-pill">
                            <span class="dot" style="background:#f59e0b"></span>
                            VIP: <b id="previewVipCount">0</b>
                        </span>
                        <span class="stat-pill">
                            <span class="dot" style="background:#ec4899"></span>
                            Đôi: <b id="previewDoubleCount">0</b>
                        </span>
                        <span class="stat-pill">
                            <span class="dot" style="background:#94a3b8"></span>
                            Tổng: <b id="previewTotalCount">0</b>
                        </span>
                    </div>

                    <div class="screen-wrap">
                        <div class="screen-bar"></div>
                        <div class="screen-label">Màn hình</div>
                    </div>

                    <div class="preview-container">
                        <div id="previewGrid" class="seat-grid"></div>
                    </div>

                    <div class="legend">
                        <div class="legend-item">
                            <div class="legend-box" style="background:#dbeafe;border:2px solid #93c5fd"></div>
                            Ghế thường
                        </div>
                        <div class="legend-item">
                            <div class="legend-box" style="background:#fef3c7;border:2px solid #fcd34d"></div>
                            Ghế VIP
                        </div>
                        <div class="legend-item">
                            <div class="legend-box" style="background:#fce7f3;border:2px solid #f9a8d4"></div>
                            Ghế đôi
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>

<script>
    const rowsInput        = document.getElementById('rowsInput');
    const colsInput        = document.getElementById('colsInput');
    const vipSeatsInput    = document.getElementById('vipSeatsInput');
    const normalSeatsInput = document.getElementById('normalSeatsInput');
    const doubleSeatsInput = document.getElementById('doubleSeatsInput');
    const doubleSeatsError = document.getElementById('doubleSeatsError');
    const previewGrid      = document.getElementById('previewGrid');
    const submitBtn        = document.getElementById('submitBtn');

    function validateDoubleSeats() {
        const val = parseInt(doubleSeatsInput.value || 0);
        if (val > 0 && val % 2 !== 0) {
            doubleSeatsError.style.display    = 'block';
            doubleSeatsInput.style.borderColor = '#dc2626';
            doubleSeatsInput.style.boxShadow   = '0 0 0 3px rgba(220,38,38,.12)';
            return false;
        }
        doubleSeatsError.style.display    = 'none';
        doubleSeatsInput.style.borderColor = '';
        doubleSeatsInput.style.boxShadow   = '';
        return true;
    }

    function generatePreview() {
        previewGrid.innerHTML = '';

        const rows        = parseInt(rowsInput.value        || 0);
        const cols        = parseInt(colsInput.value        || 0);
        const vipSeats    = parseInt(vipSeatsInput.value    || 0);
        const normalSeats = parseInt(normalSeatsInput.value || 0);
        const doubleSeats = parseInt(doubleSeatsInput.value || 0);

        const totalUsed = vipSeats + normalSeats + doubleSeats;
        const maxSeats  = rows * cols;

        document.getElementById('previewVipCount').innerText    = vipSeats;
        document.getElementById('previewNormalCount').innerText = normalSeats;
        document.getElementById('previewDoubleCount').innerText = doubleSeats;
        document.getElementById('previewTotalCount').innerText  = totalUsed;
        document.getElementById('maxSeatsText').innerText       = maxSeats;
        document.getElementById('usedSeatsText').innerText      = totalUsed;

        const warning       = document.getElementById('seatWarning');
        const isDoubleValid = validateDoubleSeats();

        if (totalUsed > maxSeats || !isDoubleValid) {
            if (totalUsed > maxSeats) {
                warning.innerHTML   = `❌ Vượt quá số ghế cho phép (${totalUsed}/${maxSeats})`;
                warning.style.color = '#dc2626';
            } else {
                warning.innerHTML   = '';
            }
            submitBtn.disabled      = true;
            submitBtn.style.opacity = '.5';
            submitBtn.style.cursor  = 'not-allowed';
        } else {
            warning.innerHTML       = totalUsed > 0 ? `✔ Hợp lệ` : '';
            warning.style.color     = '#16a34a';
            submitBtn.disabled      = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor  = 'pointer';
        }

        const barSummary = document.getElementById('barSeatSummary');
        if (barSummary) {
            barSummary.textContent = totalUsed > 0
                ? `${normalSeats} thường · ${vipSeats} VIP · ${doubleSeats} đôi`
                : '';
        }

        const queue = [];
        for (let i = 0; i < vipSeats;    i++) queue.push('vip');
        for (let i = 0; i < normalSeats; i++) queue.push('normal');
        for (let i = 0; i < doubleSeats; i++) queue.push('double');

        let qi = 0;

        for (let r = 0; r < rows; r++) {
            const rowEl = document.createElement('div');
            rowEl.className = 'preview-row';

            const label = document.createElement('div');
            label.className = 'preview-label';
            label.innerText = String.fromCharCode(65 + r);
            rowEl.appendChild(label);

            for (let c = 1; c <= cols; c++) {
                if (qi < queue.length) {
                    const type = queue[qi++];
                    const seat = document.createElement('div');
                    seat.className = `preview-seat ${type}`;
                    seat.innerText = `${String.fromCharCode(65 + r)}${c}`;
                    rowEl.appendChild(seat);
                } else {
                    const empty = document.createElement('div');
                    empty.className = 'preview-empty';
                    rowEl.appendChild(empty);
                }
            }

            previewGrid.appendChild(rowEl);
        }
    }

    [rowsInput, colsInput, vipSeatsInput, normalSeatsInput, doubleSeatsInput]
        .forEach(el => el.addEventListener('input', generatePreview));

    document.getElementById('roomNameInput').addEventListener('input', function () {
        const el = document.getElementById('barRoomName');
        if (el) el.textContent = this.value.trim() || '—';
    });

    generatePreview();

    document.getElementById('roomForm').addEventListener('submit', function (e) {
        const rows        = parseInt(rowsInput.value        || 0);
        const cols        = parseInt(colsInput.value        || 0);
        const vipSeats    = parseInt(vipSeatsInput.value    || 0);
        const normalSeats = parseInt(normalSeatsInput.value || 0);
        const doubleSeats = parseInt(doubleSeatsInput.value || 0);
        const total       = vipSeats + normalSeats + doubleSeats;
        const maxSeats    = rows * cols;

        if (doubleSeats > 0 && doubleSeats % 2 !== 0) {
            e.preventDefault();
            doubleSeatsError.style.display = 'block';
            doubleSeatsInput.focus();
            doubleSeatsInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        if (total > maxSeats) {
            e.preventDefault();
            alert(`Tổng số ghế (${total}) vượt quá số ghế tối đa (${maxSeats})`);
            return false;
        }
    });
</script>
@endsection