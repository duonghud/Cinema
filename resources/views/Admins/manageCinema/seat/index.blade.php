@extends('layouts.appAdmin')

@section('content')
<style>
:root {
    --bg-deep:   #080f1e;
    --bg-card:   #0f1a2e;
    --bg-panel:  #111827;
    --border:    #1e2d45;
    --muted:     #6b7280;
    --accent:    #3b82f6;
}
body { background: var(--bg-deep); color: #e5e7eb; }

/* ── Cards ── */
.panel-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 16px;
}
.panel-title {
    font-size: 11px; letter-spacing: .12em;
    text-transform: uppercase; color: var(--muted);
    margin-bottom: 10px; font-weight: 600;
}

/* ── Controls ── */
.ctrl { background: var(--bg-panel); border: 1px solid var(--border);
        color: #e5e7eb; border-radius: 10px; padding: 9px 14px;
        font-size: 14px; width: 100%; transition: border-color .2s; }
.ctrl:focus { outline: none; border-color: var(--accent);
              box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
.ctrl option { background: #1f2937; }

/* ── Buttons ── */
.btn-c {
    border-radius: 10px; font-weight: 700; font-size: 13px;
    padding: 9px 18px; cursor: pointer; border: none;
    transition: all .15s ease; white-space: nowrap;
}
.btn-c:hover { transform: translateY(-1px); }
.btn-primary-c { background: var(--accent); color: #fff; }
.btn-primary-c:hover { background: #2563eb; }
.btn-ghost    { background: var(--bg-panel); color: var(--muted);
                border: 1px solid var(--border); }
.btn-ghost:hover { background: #1f2937; color: #e5e7eb; }
.btn-danger-c { background: #7f1d1d; color: #fca5a5; border: 1px solid #dc2626; }
.btn-danger-c:hover { background: #991b1b; }
.btn-success-c { background: #14532d; color: #86efac; border: 1px solid #22c55e; }
.btn-success-c:hover { background: #166534; }
.btn-warning-c { background: #78350f; color: #fcd34d; border: 1px solid #f59e0b; }
.btn-warning-c:hover { background: #92400e; }

/* ── Swap info box ── */
.swap-info {
    background: #1e3a5f22;
    border: 1px solid #1e40af;
    border-radius: 12px;
    padding: 14px 18px;
    display: none;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
}
.swap-info.show { display: flex; }
.swap-info .seat-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--bg-panel); border: 1px solid var(--border);
    border-radius: 8px; padding: 6px 12px; font-weight: 700; font-size: 14px;
}
.swap-info .arrow { color: var(--muted); font-size: 18px; }
.swap-mode-hint {
    background: #0f172a; border: 1px dashed #22c55e;
    border-radius: 10px; padding: 10px 16px;
    color: #86efac; font-size: 13px; font-weight: 600;
    display: none;
}
.swap-mode-hint.show { display: block; }

/* ── Stats ── */
.stat-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
.stat-pill {
    background: var(--bg-panel); border: 1px solid var(--border);
    border-radius: 999px; padding: 5px 14px;
    font-size: 12px; font-weight: 700;
    display: flex; align-items: center; gap: 6px;
}
.stat-pill .dot { width: 8px; height: 8px; border-radius: 50%; }
.stat-pill.over-limit { border-color: #ef4444; color: #fca5a5; }

/* ── Add seat form ── */
.add-seat-grid { display: grid; grid-template-columns: 80px 100px 1fr auto; gap: 10px; align-items: end; }
@media(max-width:640px){ .add-seat-grid { grid-template-columns: 1fr 1fr; } }

/* ── Screen ── */
.screen-wrap { text-align: center; margin-bottom: 16px; }
.screen-bar {
    width: 55%; height: 8px; margin: 0 auto 6px;
    background: linear-gradient(to right, transparent, #fbbf24, transparent);
    border-radius: 999px; box-shadow: 0 0 24px rgba(251,191,36,.4);
}
.screen-label { font-size: 11px; letter-spacing: .2em; color: #fbbf24; text-transform: uppercase; }

/* ── Grid ── */
.seat-grid { display: flex; flex-direction: column; gap: 8px;
             align-items: center; overflow-x: auto; padding: 16px 8px 24px; }
.seat-row { display: flex; gap: 6px; align-items: center; }
.row-label { width: 26px; text-align: center; font-size: 12px;
             color: var(--muted); font-weight: 700; flex-shrink: 0; }

/* ── Seat ── */
.seat, .empty-slot {
    width: 40px; height: 40px; border-radius: 8px;
    display: flex; justify-content: center; align-items: center;
    font-size: 9px; font-weight: 800;
    user-select: none; flex-shrink: 0; position: relative;
    transition: transform .1s, box-shadow .1s;
}
.seat { cursor: pointer; border: 2px solid transparent; }
.seat:hover { transform: translateY(-2px) scale(1.07); z-index: 10; }

.seat.t-normal  { background: #1e3a5f; color: #93c5fd; border-color: #1e40af; }
.seat.t-vip     { background: #92400e; color: #fcd34d; border-color: #d97706; }
.seat.t-couple  { background: #831843; color: #f9a8d4; border-color: #db2777; }
.seat.t-maint   { background: #374151; color: #9ca3af; border-color: #4b5563; }

/* States */
.seat.is-source {
    outline: 3px solid #60a5fa;
    box-shadow: 0 0 0 5px rgba(96,165,250,.3);
    z-index: 30; transform: scale(1.1);
}
.seat.is-target {
    outline: 3px solid #22c55e;
    box-shadow: 0 0 0 5px rgba(34,197,94,.3);
    animation: pulse-green .8s infinite alternate;
    z-index: 25;
}
@keyframes pulse-green {
    from { box-shadow: 0 0 0 3px rgba(34,197,94,.2); }
    to   { box-shadow: 0 0 0 7px rgba(34,197,94,.45); }
}
.seat.is-dimmed { opacity: .25; pointer-events: none; }

/* Empty slot */
.empty-slot {
    background: #0f172a; border: 1px dashed #1e293b;
    color: #1e3a5f; cursor: crosshair; font-size: 16px; font-weight: 300;
}
.empty-slot:hover { border-color: #22c55e; color: #22c55e; background: #071a0e; transform: scale(1.05); }

/* Delete btn */
.del-btn {
    position: absolute; top: -5px; right: -5px;
    width: 16px; height: 16px; border-radius: 50%;
    background: #dc2626; color: #fff; font-size: 10px;
    display: none; justify-content: center; align-items: center;
    cursor: pointer; z-index: 50; border: 1px solid #fca5a5;
}
.seat:hover .del-btn { display: flex; }

/* ── Legend ── */
.legend { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; margin-top: 20px; padding-bottom: 24px; }
.legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #9ca3af; }
.legend-box  { width: 20px; height: 20px; border-radius: 5px; }

/* ── Spinner ── */
.grid-spinner { display: none; text-align: center; padding: 40px 0; color: var(--muted); }
.grid-spinner.show { display: block; }

/* ── Toast ── */
#toast {
    position: fixed; bottom: 24px; right: 24px;
    min-width: 260px; padding: 14px 20px; border-radius: 12px;
    font-size: 14px; font-weight: 600; z-index: 9999;
    opacity: 0; pointer-events: none; transform: translateY(10px);
    transition: all .3s ease; display: flex; align-items: center; gap: 10px;
}
#toast.show { opacity: 1; transform: translateY(0); }
#toast.success { background: #14532d; border: 1px solid #22c55e; color: #bbf7d0; }
#toast.error   { background: #7f1d1d; border: 1px solid #ef4444; color: #fecaca; }
#toast.info    { background: #1e3a5f; border: 1px solid #3b82f6; color: #bfdbfe; }
#toast.warning { background: #78350f; border: 1px solid #f59e0b; color: #fcd34d; }
</style>

{{-- ══════════════════════ PANEL 1: CHỌN PHÒNG + THỐNG KÊ ══════════════════════ --}}
<div class="panel-card">
    <div class="row g-3 align-items-end">

        <div class="col-md-4">
            <div class="panel-title">Phòng chiếu</div>
            <form method="GET" id="roomForm">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="roomID" class="ctrl" onchange="this.form.submit()">
                    @forelse($rooms as $r)
                        <option value="{{ $r->roomID }}"
                            {{ (int)($roomID ?? 0) === (int)$r->roomID ? 'selected' : '' }}>
                            {{ $r->roomName }} — {{ $r->capacity }} ghế
                        </option>
                    @empty
                        <option value="">Chưa có phòng</option>
                    @endforelse
                </select>
            </form>
        </div>

        <div class="col-md-8">
            <div class="panel-title">Thống kê ghế</div>
            <div class="stat-pills">
                <span class="stat-pill"><span class="dot" style="background:#6b7280"></span>Tổng: <b id="statTotal">0</b> / <b id="statCap">{{ $room?->capacity ?? 0 }}</b></span>
                <span class="stat-pill" id="pillNormal"><span class="dot" style="background:#3b82f6"></span>Thường: <b id="statNormal">0</b></span>
                <span class="stat-pill" id="pillVip"><span class="dot" style="background:#f59e0b"></span>VIP: <b id="statVip">0</b></span>
                <span class="stat-pill" id="pillCouple"><span class="dot" style="background:#ec4899"></span>Đôi: <b id="statCouple">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#6b7280"></span>Bảo trì: <b id="statMaint">0</b></span>
                <span class="stat-pill"><span class="dot" style="background:#374151"></span>Hàng: <b id="statRows">0</b> / Cột: <b id="statCols">0</b></span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════ PANEL 2: HOÁN ĐỔI GHẾ ══════════════════════ --}}
<div class="panel-card">
    <div class="panel-title">Hoán đổi loại ghế</div>

    {{-- Trạng thái mặc định --}}
    <p id="swapIdleHint" class="mb-0" style="color:var(--muted);font-size:13px;">
        ← Bấm vào một ghế trên sơ đồ để bắt đầu hoán đổi loại.
    </p>

    {{-- Khi đã chọn ghế nguồn --}}
    <div class="swap-info" id="swapInfo">
        <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Ghế đang chọn</div>
            <div class="seat-badge" id="swapSourceBadge">—</div>
        </div>

        <span class="arrow">⇄</span>

        <div style="flex:1;min-width:160px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Đổi sang loại</div>
            <select id="swapTargetType" class="ctrl">
                @foreach($seatTypes as $type)
                    @if($type->seatTypeName !== 'Bảo trì')
                        <option value="{{ $type->seatTypeID }}">{{ $type->seatTypeName }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-c btn-success-c" onclick="startSwapPicking()">
                Tìm ghế để hoán đổi
            </button>
            <button class="btn-c btn-warning-c" onclick="toggleMaintenance()">
                ⚙ Bảo trì
            </button>
            <button class="btn-c btn-ghost" onclick="resetSwapMode()">
                Huỷ
            </button>
        </div>
    </div>

    {{-- Khi đang picking target --}}
    <div class="swap-mode-hint" id="swapPickHint"></div>
</div>

{{-- ══════════════════════ PANEL 3: THÊM GHẾ MỚI ══════════════════════ --}}
<div class="panel-card">
    <div class="panel-title">
        Thêm ghế mới
        <span id="addLimitWarn" style="color:#f59e0b;font-size:11px;margin-left:8px;display:none;">
            ⚠ Phòng đã đạt sức chứa tối đa
        </span>
    </div>
    <div class="add-seat-grid">
        <div>
            <label style="font-size:11px;color:var(--muted);display:block;margin-bottom:4px;">Hàng</label>
            <input id="addRow" type="text" maxlength="1" placeholder="A"
                   class="ctrl" style="text-transform:uppercase">
        </div>
        <div>
            <label style="font-size:11px;color:var(--muted);display:block;margin-bottom:4px;">Cột</label>
            <input id="addCol" type="number" min="1" max="50" placeholder="1" class="ctrl">
        </div>
        <div>
            <label style="font-size:11px;color:var(--muted);display:block;margin-bottom:4px;">Loại ghế</label>
            <select id="addType" class="ctrl">
                @foreach($seatTypes as $type)
                    <option value="{{ $type->seatTypeID }}">{{ $type->seatTypeName }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-c btn-primary-c" onclick="addSeat()" id="btnAddSeat">
            + Thêm ghế
        </button>
    </div>
</div>

{{-- Form batch update (hidden) --}}
<form id="updateSeatForm" method="POST" action="{{ route('seat.updateMultiple') }}">
    @csrf
    <div id="seatIDsContainer"></div>
    <input type="hidden" name="seatTypeID" id="seatTypeInput">
</form>

{{-- ══════════════════════ GRID ══════════════════════ --}}
<div class="screen-wrap">
    <div class="screen-bar"></div>
    <div class="screen-label">Màn hình</div>
</div>

<div class="grid-spinner" id="gridSpinner">
    <div class="spinner-border spinner-border-sm text-secondary me-2"></div>
    Đang tải sơ đồ ghế...
</div>

<div id="seatGrid" class="seat-grid"></div>

<div class="legend">
    <div class="legend-item"><div class="legend-box" style="background:#1e3a5f;border:2px solid #1e40af"></div>Thường</div>
    <div class="legend-item"><div class="legend-box" style="background:#92400e;border:2px solid #d97706"></div>VIP</div>
    <div class="legend-item"><div class="legend-box" style="background:#831843;border:2px solid #db2777"></div>Đôi</div>
    <div class="legend-item"><div class="legend-box" style="background:#374151;border:2px solid #4b5563"></div>Bảo trì</div>
    <div class="legend-item">
        <div class="legend-box" style="outline:3px solid #60a5fa;border-radius:8px;background:#1e3a5f"></div>Đang chọn
    </div>
    <div class="legend-item">
        <div class="legend-box" style="outline:3px solid #22c55e;border-radius:8px;background:#1e3a5f"></div>Có thể hoán đổi
    </div>
</div>

<div id="toast"><span id="t-ico"></span><span id="t-msg"></span></div>

<script>
'use strict';

// ── Hằng số ──────────────────────────────────────────────────────────
const ROOM_ID    = parseInt("{{ (int)($roomID ?? 0) }}", 10);
const CAPACITY   = parseInt("{{ $room?->capacity ?? 0 }}", 10);
const CSRF       = "{{ csrf_token() }}";
const TYPE_CLASS = { 1:'t-vip', 2:'t-normal', 3:'t-couple', 4:'t-maint' };
const TYPE_NAME  = { 1:'VIP', 2:'Thường', 3:'Đôi', 4:'Bảo trì' };
const TYPE_COLOR = { 1:'#f59e0b', 2:'#3b82f6', 3:'#ec4899', 4:'#6b7280' };

// ── State ─────────────────────────────────────────────────────────────
let seatsData   = [];          // tất cả ghế trong phòng
let appMode     = 'idle';      // 'idle' | 'selected' | 'swap_picking'
let sourceSeat  = null;        // { id, name, typeID, el }

// ── Load ghế ──────────────────────────────────────────────────────────
function loadSeats(silent = false) {
    if (!ROOM_ID) {
        document.getElementById('seatGrid').innerHTML =
            '<p style="color:var(--muted);padding:40px 0;text-align:center">Chưa có phòng nào.</p>';
        return;
    }
    if (!silent) showSpinner(true);

    fetch(`/admins/seat/ajax/${ROOM_ID}`)
        .then(r => {
            if (!r.ok) return r.text().then(t => { throw new Error(t.includes('<') ? `Lỗi ${r.status}` : t); });
            return r.json();
        })
        .then(data => {
            seatsData = Array.isArray(data) ? data : [];
            renderGrid();
            updateStats();
            checkCapacityWarning();
        })
        .catch(err => showToast(err.message, 'error'))
        .finally(() => showSpinner(false));
}

// ── Stats ─────────────────────────────────────────────────────────────
function updateStats() {
    const rows   = [...new Set(seatsData.map(s => s.rowSeat))];
    const maxCol = seatsData.reduce((m, s) => Math.max(m, +s.colSeat), 0);
    const cnt    = {1:0, 2:0, 3:0, 4:0};
    seatsData.forEach(s => cnt[s.seatTypeID] = (cnt[s.seatTypeID]||0)+1);

    set('statTotal',  seatsData.length);
    set('statNormal', cnt[2]||0);
    set('statVip',    cnt[1]||0);
    set('statCouple', cnt[3]||0);
    set('statMaint',  cnt[4]||0);
    set('statRows',   rows.length);
    set('statCols',   maxCol);
}

function checkCapacityWarning() {
    const full = seatsData.length >= CAPACITY;
    document.getElementById('addLimitWarn').style.display = full ? 'inline' : 'none';
    document.getElementById('btnAddSeat').disabled = full;
}

// ── Render grid ───────────────────────────────────────────────────────
function renderGrid() {
    const grid   = document.getElementById('seatGrid');
    const seatMap = {};
    seatsData.forEach(s => { seatMap[`${s.rowSeat}-${s.colSeat}`] = s; });

    const rows   = [...new Set(seatsData.map(s => s.rowSeat))].sort();
    const maxCol = seatsData.reduce((m, s) => Math.max(m, +s.colSeat), 0);

    let html = '';
    rows.forEach(row => {
        html += `<div class="seat-row"><span class="row-label">${row}</span>`;
        for (let col = 1; col <= maxCol; col++) {
            const seat = seatMap[`${row}-${col}`];
            if (seat) {
                const cls = TYPE_CLASS[seat.seatTypeID] || 't-normal';
                html += `<div class="seat ${cls}"
                              data-id="${seat.seatID}"
                              data-row="${row}" data-col="${col}"
                              data-type="${seat.seatTypeID}"
                              data-name="${row}${col}">
                            ${row}${col}
                            <span class="del-btn"
                                  onmousedown="event.stopPropagation()"
                                  onclick="deleteSeat(${seat.seatID},event)">×</span>
                         </div>`;
            } else {
                html += `<div class="empty-slot" data-row="${row}" data-col="${col}">+</div>`;
            }
        }
        html += `</div>`;
    });

    grid.innerHTML = html;
    bindGridEvents();
    reApplyModeStyles(); // khôi phục trạng thái swap nếu đang picking
}

// ── Bind events ───────────────────────────────────────────────────────
function bindGridEvents() {
    // Ghế có sẵn
    document.querySelectorAll('.seat').forEach(el => {
        el.addEventListener('click', function() {
            if (appMode === 'swap_picking') {
                // Chỉ cho click ghế đang target (is-target)
                if (this.classList.contains('is-target')) {
                    confirmSwap(this);
                }
            } else {
                selectSeatAsSource(this);
            }
        });
    });

    // Ô trống → thêm ghế nhanh (điền vào form thêm ghế)
    document.querySelectorAll('.empty-slot').forEach(el => {
        el.addEventListener('click', function() {
            const row = this.dataset.row;
            const col = this.dataset.col;
            document.getElementById('addRow').value = row;
            document.getElementById('addCol').value = col;
            document.getElementById('addRow').focus();
            showToast(`Đã điền hàng ${row}, cột ${col} vào form thêm ghế`, 'info');
        });
    });
}

// ── Apply class theo mode lên grid ────────────────────────────────────
function reApplyModeStyles() {
    if (appMode === 'idle') return;

    const seats = document.querySelectorAll('.seat');

    if (appMode === 'selected' && sourceSeat) {
        seats.forEach(el => {
            el.classList.toggle('is-source', el.dataset.id === String(sourceSeat.id));
        });
    }

    if (appMode === 'swap_picking' && sourceSeat) {
        const targetTypeID = getSwapTargetTypeID();
        seats.forEach(el => {
            const id = el.dataset.id;
            if (id === String(sourceSeat.id)) {
                el.classList.add('is-source');
            } else if (+el.dataset.type === targetTypeID) {
                el.classList.add('is-target');
            } else {
                el.classList.add('is-dimmed');
            }
        });
    }
}

function clearGridStyles() {
    document.querySelectorAll('.seat').forEach(el => {
        el.classList.remove('is-source','is-target','is-dimmed');
    });
}

// ── Chọn ghế làm nguồn ───────────────────────────────────────────────
function selectSeatAsSource(el) {
    clearGridStyles();

    sourceSeat = {
        id:     el.dataset.id,
        name:   el.dataset.name,
        typeID: +el.dataset.type,
        el
    };

    appMode = 'selected';
    el.classList.add('is-source');

    // Hiện panel swap
    document.getElementById('swapIdleHint').style.display = 'none';
    document.getElementById('swapInfo').classList.add('show');
    document.getElementById('swapPickHint').classList.remove('show');

    // Badge ghế nguồn
    const badge = document.getElementById('swapSourceBadge');
    badge.innerHTML = `
        <span style="color:${TYPE_COLOR[sourceSeat.typeID]}">■</span>
        ${sourceSeat.name} (${TYPE_NAME[sourceSeat.typeID]})
    `;

    // Set default target = loại khác với nguồn
    const sel = document.getElementById('swapTargetType');
    for (let opt of sel.options) {
        if (+opt.value !== sourceSeat.typeID) { opt.selected = true; break; }
    }
}

// ── Bắt đầu chọn ghế đích ────────────────────────────────────────────
function startSwapPicking() {
    if (!sourceSeat) return;

    const targetTypeID = getSwapTargetTypeID();

    if (targetTypeID === sourceSeat.typeID) {
        showToast('Loại ghế đích phải khác loại hiện tại', 'warning'); return;
    }

    // Kiểm tra có ghế đích không
    const targets = seatsData.filter(s => s.seatTypeID === targetTypeID);
    if (!targets.length) {
        showToast(`Không có ghế loại "${TYPE_NAME[targetTypeID]}" để hoán đổi`, 'warning'); return;
    }

    appMode = 'swap_picking';
    clearGridStyles();
    reApplyModeStyles();

    const hint = document.getElementById('swapPickHint');
    hint.textContent =
        `Đang hoán đổi: ${sourceSeat.name} (${TYPE_NAME[sourceSeat.typeID]}) ← chọn 1 ghế ${TYPE_NAME[targetTypeID]} được tô xanh bên dưới. Nhấn "Huỷ" để thoát.`;
    hint.classList.add('show');
}

// ── Xác nhận hoán đổi ────────────────────────────────────────────────
function confirmSwap(targetEl) {
    const targetID   = targetEl.dataset.id;
    const targetName = targetEl.dataset.name;

    if (!confirm(`Hoán đổi loại ghế:\n${sourceSeat.name} (${TYPE_NAME[sourceSeat.typeID]}) ⇄ ${targetName} (${TYPE_NAME[+targetEl.dataset.type]})?`)) return;

    apiFetch('/admins/seat/ajax-swap-type', 'POST', {
        seatID_a: parseInt(sourceSeat.id),
        seatID_b: parseInt(targetID)
    })
    .then(res => {
        if (res.success === false) { showToast(res.message, 'error'); return; }
        showToast(`Đã hoán đổi ${sourceSeat.name} ⇄ ${targetName}`, 'success');
        resetSwapMode();
        loadSeats(true);
    })
    .catch(err => showToast(err.message, 'error'));
}

// ── Bảo trì toggle ───────────────────────────────────────────────────
function toggleMaintenance() {
    if (!sourceSeat) return;
    const isMaint = sourceSeat.typeID === 4;
    const newType = isMaint ? 2 : 4; // Bảo trì ↔ Thường
    const label   = isMaint ? 'Phục hồi ghế thường' : 'Chuyển sang bảo trì';

    if (!confirm(`${label}: ghế ${sourceSeat.name}?`)) return;

    apiFetch('/admins/seat/ajax-update-type', 'POST', {
        seatID:     parseInt(sourceSeat.id),
        seatTypeID: newType
    })
    .then(res => {
        if (res.success === false) { showToast(res.message, 'error'); return; }
        showToast(`Ghế ${sourceSeat.name} → ${TYPE_NAME[newType]}`, 'success');
        resetSwapMode();
        loadSeats(true);
    })
    .catch(err => showToast(err.message, 'error'));
}

// ── Reset về idle ─────────────────────────────────────────────────────
function resetSwapMode() {
    appMode    = 'idle';
    sourceSeat = null;
    clearGridStyles();
    document.getElementById('swapIdleHint').style.display = '';
    document.getElementById('swapInfo').classList.remove('show');
    document.getElementById('swapPickHint').classList.remove('show');
}

// ── Thêm ghế mới ─────────────────────────────────────────────────────
function addSeat() {
    const row    = document.getElementById('addRow').value.trim().toUpperCase();
    const col    = parseInt(document.getElementById('addCol').value);
    const typeID = parseInt(document.getElementById('addType').value);

    if (!row || !/^[A-Z]$/.test(row)) {
        showToast('Hàng phải là 1 chữ cái (A–Z)', 'error'); return;
    }
    if (!col || col < 1 || col > 50) {
        showToast('Cột phải từ 1 đến 50', 'error'); return;
    }

    // Kiểm tra sức chứa client-side
    if (seatsData.length >= CAPACITY) {
        showToast('Phòng đã đầy, không thể thêm ghế', 'error'); return;
    }

    // Kiểm tra trùng ghế
    const exists = seatsData.some(s => s.rowSeat === row && +s.colSeat === col);
    if (exists) {
        showToast(`Ghế ${row}${col} đã tồn tại`, 'error'); return;
    }

    apiFetch('/admins/seat/ajax-add', 'POST', {
        roomID: ROOM_ID, rowSeat: row, colSeat: col, seatTypeID: typeID
    })
    .then(res => {
        if (res.error) { showToast(res.error, 'error'); return; }
        showToast(`Đã thêm ghế ${row}${col} (${TYPE_NAME[typeID]})`, 'success');
        document.getElementById('addRow').value = '';
        document.getElementById('addCol').value = '';
        loadSeats(true);
    })
    .catch(err => showToast(err.message, 'error'));
}

// ── Xoá ghế ──────────────────────────────────────────────────────────
function deleteSeat(id, event) {
    event.stopPropagation();
    if (!confirm('Xoá ghế này?')) return;

    fetch(`/admins/seat/ajax-delete/${id}`, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(res => {
        if (res.error) { showToast(res.error, 'error'); return; }
        if (sourceSeat && String(sourceSeat.id) === String(id)) resetSwapMode();
        loadSeats(true);
        showToast('Đã xoá ghế', 'success');
    })
    .catch(() => showToast('Lỗi kết nối', 'error'));
}

// ── Helpers ───────────────────────────────────────────────────────────
function getSwapTargetTypeID() {
    return parseInt(document.getElementById('swapTargetType').value);
}

function apiFetch(url, method, body) {
    return fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(body)
    }).then(r => {
        if (!r.ok) return r.text().then(t => {
            try { const j = JSON.parse(t); throw new Error(j.message||j.error||`Lỗi ${r.status}`); }
            catch { throw new Error(t.includes('<') ? `Lỗi ${r.status}` : t); }
        });
        return r.json();
    });
}

function set(id, val)      { document.getElementById(id).textContent = val; }
function showSpinner(on)   { document.getElementById('gridSpinner').classList.toggle('show', on); }

let _tt;
const T_ICON = { success:'✓', error:'✕', info:'ℹ', warning:'⚠' };
function showToast(msg, type='success') {
    const el = document.getElementById('toast');
    document.getElementById('t-ico').textContent = T_ICON[type]||'';
    document.getElementById('t-msg').textContent = msg;
    el.className = `show ${type}`;
    clearTimeout(_tt);
    _tt = setTimeout(() => { el.className=''; }, 3500);
}

// Enter trên input thêm ghế
document.getElementById('addRow').addEventListener('keydown', e => { if(e.key==='Enter') document.getElementById('addCol').focus(); });
document.getElementById('addCol').addEventListener('keydown', e => { if(e.key==='Enter') addSeat(); });

// Escape để thoát swap mode
document.addEventListener('keydown', e => { if(e.key==='Escape') resetSwapMode(); });

// ── Session flash ────────────────────────────────────────────────────
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => showToast({{ Js::from(session('success')) }}, 'success'));
@endif
@if(session('error'))
    document.addEventListener('DOMContentLoaded', () => showToast({{ Js::from(session('error')) }}, 'error'));
@endif

document.addEventListener('DOMContentLoaded', loadSeats);
</script>
@endsection