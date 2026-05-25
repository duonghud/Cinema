@extends('layouts.appAdmin')

@section('content')
<style>
:root{
    --bg:#080f1e;
    --card:#0f1a2e;
    --panel:#111827;
    --border:#1e2d45;
    --muted:#6b7280;
    --text:#e5e7eb;
    --accent:#3b82f6;
}

body{
    background:var(--bg);
    color:var(--text);
}

.room-wrap{
    max-width:1100px;
    margin:auto;
}

.room-card{
    background:var(--card);
    border:1px solid var(--border);
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 40px rgba(0,0,0,.35);
}

.room-header{
    padding:24px 28px;
    border-bottom:1px solid var(--border);
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.room-title{
    font-size:24px;
    font-weight:800;
    margin:0;
}

.room-sub{
    color:var(--muted);
    font-size:13px;
    margin-top:4px;
}

.room-body{
    padding:28px;
}

.block{
    background:var(--panel);
    border:1px solid var(--border);
    border-radius:18px;
    padding:20px;
    margin-bottom:20px;
}

.block-title{
    font-size:12px;
    letter-spacing:.14em;
    text-transform:uppercase;
    color:var(--muted);
    font-weight:700;
    margin-bottom:18px;
}

.form-label{
    color:#cbd5e1;
    font-weight:600;
    font-size:14px;
    margin-bottom:8px;
}

.form-control,
.form-select{
    background:#0b1220 !important;
    border:1px solid var(--border) !important;
    color:#f1f5f9 !important;
    border-radius:12px !important;
    min-height:48px;
    box-shadow:none !important;
}

.form-control:focus,
.form-select:focus{
    border-color:var(--accent)!important;
    box-shadow:0 0 0 4px rgba(59,130,246,.15)!important;
}

.form-select option{
    background:#111827;
    color:#fff;
}

.preview-wrap{
    background:#0b1220;
    border:1px solid var(--border);
    border-radius:18px;
    padding:24px;
    overflow:auto;
}

.screen{
    width:60%;
    height:8px;
    margin:auto;
    border-radius:999px;
    background:linear-gradient(to right,transparent,#fbbf24,transparent);
    box-shadow:0 0 25px rgba(251,191,36,.5);
}

.screen-text{
    text-align:center;
    color:#fbbf24;
    font-size:11px;
    letter-spacing:.2em;
    text-transform:uppercase;
    margin-top:8px;
    margin-bottom:28px;
}

.seat-grid{
    display:flex;
    flex-direction:column;
    gap:10px;
    align-items:center;
}

.seat-row{
    display:flex;
    gap:8px;
    align-items:center;
}

.row-label{
    width:24px;
    text-align:center;
    font-size:12px;
    color:#64748b;
    font-weight:700;
}

.seat{
    width:42px;
    height:42px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:10px;
    font-weight:800;
    border:2px solid transparent;
}

.seat.normal{
    background:#1e3a5f;
    border-color:#2563eb;
    color:#93c5fd;
}

.seat.vip{
    background:#92400e;
    border-color:#f59e0b;
    color:#fde68a;
}

.seat.double{
    background:#831843;
    border-color:#ec4899;
    color:#f9a8d4;
}

.legend{
    margin-top:24px;
    display:flex;
    justify-content:center;
    gap:20px;
    flex-wrap:wrap;
}

.legend-item{
    display:flex;
    align-items:center;
    gap:8px;
    color:#94a3b8;
    font-size:13px;
}

.legend-box{
    width:20px;
    height:20px;
    border-radius:6px;
}

.btn-back{
    background:#111827;
    border:1px solid var(--border);
    color:#94a3b8;
    padding:12px 18px;
    border-radius:12px;
    font-weight:700;
    text-decoration:none;
}

.btn-back:hover{
    color:#fff;
    background:#1f2937;
}

.btn-save{
    background:var(--accent);
    border:none;
    color:#fff;
    padding:12px 24px;
    border-radius:12px;
    font-weight:700;
}

.btn-save:hover{
    background:#2563eb;
}

.invalid-feedback{
    display:block;
}

.alert-debug{
    background:#7f1d1d;
    border:1px solid #ef4444;
    color:#fecaca;
    padding:16px 18px;
    border-radius:14px;
    margin-bottom:20px;
}
</style>

<div class="container py-5 room-wrap">

    {{-- DEBUG --}}
    @if ($errors->any())
        <div class="alert-debug">
            <b>❌ Form đang có lỗi:</b>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="room-card">

        {{-- HEADER --}}
        <div class="room-header">
            <div>
                <h2 class="room-title">
                    Chỉnh sửa phòng chiếu
                </h2>

                <div class="room-sub">
                    Cập nhật thông tin và xem trước sơ đồ ghế
                </div>
            </div>
        </div>

        {{-- BODY --}}
        <div class="room-body">

            <form action="{{ route('screeningRoom.update', $room->roomID) }}"
                  method="POST">

                @csrf
                @method('PUT')

                {{-- THÔNG TIN --}}
                <div class="block">

                    <div class="block-title">
                        Thông tin phòng
                    </div>

                    <div class="row g-4">

                        <div class="col-md-6">
                            <label class="form-label">
                                Tên phòng
                            </label>

                            <input type="text"
                                   name="roomName"
                                   id="roomName"
                                   class="form-control @error('roomName') is-invalid @enderror"
                                   value="{{ old('roomName', $room->roomName) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Số cột ghế
                            </label>

                            <input type="number"
                                   name="cols"
                                   id="cols"
                                   min="1"
                                   max="20"
                                   class="form-control @error('cols') is-invalid @enderror"
                                   value="{{ old('cols', $room->cols) }}">
                        </div>

                    </div>

                </div>

                {{-- GHẾ --}}
                <div class="block">

                    <div class="block-title">
                        Cấu hình ghế
                    </div>

                    <div class="row g-4">

                        {{-- VIP --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Ghế VIP
                            </label>

                            <input type="number"
                                   name="vipSeats"
                                   id="vipSeats"
                                   min="0"
                                   class="form-control"
                                   value="{{ old('vipSeats', $seatCounts['vipSeats'] ?? 0) }}">
                        </div>

                        {{-- NORMAL --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Ghế thường
                            </label>

                            <input type="number"
                                   name="normalSeats"
                                   id="normalSeats"
                                   min="0"
                                   class="form-control"
                                   value="{{ old('normalSeats', $seatCounts['normalSeats'] ?? 0) }}">
                        </div>

                        {{-- DOUBLE --}}
                        <div class="col-md-4">
                            <label class="form-label">
                                Ghế đôi
                            </label>

                            <input type="number"
                                   name="doubleSeats"
                                   id="doubleSeats"
                                   min="0"
                                   class="form-control"
                                   value="{{ old('doubleSeats', $seatCounts['doubleSeats'] ?? 0) }}">
                        </div>

                    </div>

                </div>

                {{-- LOẠI PHÒNG --}}
                <div class="block">

                    <div class="block-title">
                        Loại phòng chiếu
                    </div>

                    <select name="screenTypeID"
                            class="form-select @error('screenTypeID') is-invalid @enderror">

                        <option value="">
                            -- Chọn loại phòng --
                        </option>

                        @foreach($screenTypes as $type)
                            <option value="{{ $type->screenTypeID }}"
                                {{ old('screenTypeID', $room->screenTypeID) == $type->screenTypeID ? 'selected' : '' }}>

                                {{ $type->name }}

                            </option>
                        @endforeach

                    </select>

                </div>

                {{-- PREVIEW --}}
                <div class="block">

                    <div class="block-title">
                        Xem trước sơ đồ ghế
                    </div>

                    <div class="preview-wrap">

                        <div class="screen"></div>
                        <div class="screen-text">
                            Màn hình
                        </div>

                        <div id="seatPreview" class="seat-grid"></div>

                        <div class="legend">
                            <div class="legend-item">
                                <div class="legend-box"
                                     style="background:#1e3a5f;border:2px solid #2563eb"></div>
                                Ghế thường
                            </div>

                            <div class="legend-item">
                                <div class="legend-box"
                                     style="background:#92400e;border:2px solid #f59e0b"></div>
                                VIP
                            </div>

                            <div class="legend-item">
                                <div class="legend-box"
                                     style="background:#831843;border:2px solid #ec4899"></div>
                                Ghế đôi
                            </div>
                        </div>

                    </div>

                </div>

                {{-- BUTTON --}}
                <div class="d-flex justify-content-between align-items-center">

                    <a href="{{ route('screeningRoom.index') }}"
                       class="btn-back">
                        ← Quay lại
                    </a>

                    <button type="submit"
                            class="btn-save">
                        Cập nhật phòng chiếu
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script>
function renderPreview() {

    const cols    = parseInt(document.getElementById('cols').value) || 0;
    let vip       = parseInt(document.getElementById('vipSeats').value) || 0;
    let normal    = parseInt(document.getElementById('normalSeats').value) || 0;
    let dbl       = parseInt(document.getElementById('doubleSeats').value) || 0;

    const total   = vip + normal + dbl;
    const preview = document.getElementById('seatPreview');

    preview.innerHTML = '';

    if (!cols || !total) {
        preview.innerHTML = `
            <div style="color:#64748b;text-align:center;padding:30px;">
                Nhập số ghế để xem trước sơ đồ
            </div>
        `;
        return;
    }

    const rows = Math.ceil(total / cols);

    let seatNo = 1;

    for (let r = 0; r < rows; r++) {

        const rowLetter = String.fromCharCode(65 + r);

        const row = document.createElement('div');
        row.className = 'seat-row';

        row.innerHTML += `
            <div class="row-label">
                ${rowLetter}
            </div>
        `;

        for (let c = 1; c <= cols; c++) {

            if (seatNo > total) break;

            let type = 'normal';

            if (vip > 0) {
                type = 'vip';
                vip--;
            }
            else if (dbl > 0) {
                type = 'double';
                dbl--;
            }
            else if (normal > 0) {
                type = 'normal';
                normal--;
            }

            row.innerHTML += `
                <div class="seat ${type}">
                    ${rowLetter}${c}
                </div>
            `;

            seatNo++;
        }

        preview.appendChild(row);
    }
}

['cols','vipSeats','normalSeats','doubleSeats']
.forEach(id => {
    document.getElementById(id)
        .addEventListener('input', renderPreview);
});

document.addEventListener('DOMContentLoaded', renderPreview);
</script>
@endsection