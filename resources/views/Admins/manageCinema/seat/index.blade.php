@extends('layouts.appAdmin')

@section('content')

<style>
    body {
        background: #0b1220;
        color: white;
    }

    .screen-bar {
        width: 60%;
        height: 40px;
        margin: auto;
        background: linear-gradient(to bottom, #fbbf24, transparent);
        border-radius: 50%;
    }

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
        text-align: center;
        font-size: 13px;
        color: #9ca3af;
    }

    .seat {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 11px;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
        user-select: none;
        position: relative;
        font-weight: 600;
    }

    .seat:hover {
        transform: translateY(-4px) scale(1.1);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        z-index: 10;
    }

    /* ✅ Tất cả loại ghế đều clickable */
    .seat.normal      { background: #374151; color: #e5e7eb; }
    .seat.vip         { background: #f97316; color: #fff; }
    .seat.couple      { background: #ff4d4f; color: #fff; }
    .seat.maintenance { background: #6F0E10; color: #fca5a5; }

    /* Loading state khi đang gọi API */
    .seat.loading {
        opacity: 0.5;
        pointer-events: none;
        animation: pulse 0.6s infinite alternate;
    }

    @keyframes pulse {
        from { opacity: 0.4; }
        to   { opacity: 0.8; }
    }

    .delete-btn {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #dc2626;
        color: white;
        font-size: 10px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: none;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        z-index: 30;
        line-height: 1;
    }

    .seat:hover .delete-btn {
        display: flex;
    }

    .legend {
        display: flex;
        justify-content: center;
        gap: 25px;
        margin-top: 24px;
        flex-wrap: wrap;
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
        flex-shrink: 0;
    }

    .box.normal      { background: #374151; }
    .box.vip         { background: #f97316; }
    .box.couple      { background: #ff4d4f; }
    .box.maintenance { background: #6f0e10; }
</style>


{{-- ======================= CHỌN PHÒNG ======================= --}}
<div class="mb-4">
    <div class="bg-dark border border-secondary rounded-4 shadow-lg p-4">
        <div class="row g-3 align-items-end">

            <div class="col-md-4">
                <form method="GET" id="roomForm">
                    <label class="form-label text-white fw-semibold">Chọn phòng</label>
                    <select name="roomID"
                        onchange="this.form.submit()"
                        class="form-select bg-secondary text-white border-0 rounded-3">
                        @foreach($rooms as $roomItem)
                            <option value="{{ $roomItem->roomID }}"
                                {{ $roomID == $roomItem->roomID ? 'selected' : '' }}>
                                Phòng {{ $roomItem->roomName }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="col-md-8">
                <div class="alert alert-info mb-0 py-2 rounded-3" style="font-size:13px;">
                    <strong>Click vào bất kỳ ghế nào</strong> để chuyển loại:
                    <span class="text-white">Thường → VIP → Đôi → Bảo trì → Thường</span>
                </div>
            </div>

        </div>
    </div>
</div>


{{-- ======================= SƠ ĐỒ GHẾ ======================= --}}
<div class="text-center mb-3">
    <div class="screen-bar"></div>
    <p class="text-warning mt-2 mb-0" style="letter-spacing:4px; font-size:12px;">MÀN HÌNH</p>
</div>

<div id="seatGrid" class="seat-grid mt-4"></div>


{{-- ======================= CHÚ THÍCH ======================= --}}
<div class="legend">
    <div class="legend-item">
        <span class="box normal"></span> Ghế thường
    </div>
    <div class="legend-item">
        <span class="box vip"></span> Ghế VIP
    </div>
    <div class="legend-item">
        <span class="box couple"></span> Ghế đôi
    </div>
    <div class="legend-item">
        <span class="box maintenance"></span> Ghế bảo trì
    </div>
</div>


<script>
    const currentRoom = "{{ $roomID ?? 0 }}";
    let seatsData = [];

    // Thứ tự xoay vòng khi click: Thường(2) → VIP(1) → Đôi(3) → Bảo trì(4) → Thường(2)
    const CYCLE = [2, 1, 3, 4];

    // Tên loại ghế để hiển thị tooltip / confirm
    const TYPE_LABEL = {
        1: 'VIP',
        2: 'Thường',
        3: 'Đôi',
        4: 'Bảo trì',
    };

    // CSS class theo loại
    const TYPE_CLASS = {
        1: 'vip',
        2: 'normal',
        3: 'couple',
        4: 'maintenance',
    };

    // Thứ tự hiển thị trong hàng: VIP trước, thường tiếp, đôi/bảo trì cuối
    const TYPE_ORDER = { 1: 0, 2: 1, 3: 2, 4: 2 };


    // ================= LOAD GHẾ =================
    function loadSeats() {
        if (!currentRoom || currentRoom == 0) {
            document.getElementById('seatGrid').innerHTML =
                "<p class='text-warning'>Vui lòng chọn phòng.</p>";
            return;
        }

        fetch(`/admins/seat/ajax/${currentRoom}`)
            .then(res => {
                if (!res.ok) throw new Error('Không thể tải dữ liệu ghế');
                return res.json();
            })
            .then(data => {
                seatsData = data || [];
                renderGrid();
            })
            .catch(err => {
                console.error(err);
                document.getElementById('seatGrid').innerHTML =
                    "<p class='text-warning'>Không tải được dữ liệu ghế.</p>";
            });
    }


    // ================= RENDER GRID =================
    function renderGrid() {
        const seatGrid = document.getElementById('seatGrid');

        if (!seatsData || seatsData.length === 0) {
            seatGrid.innerHTML = "<p class='text-warning'>Chưa có ghế.</p>";
            return;
        }

        const rows = [...new Set(seatsData.map(s => s.rowSeat))].sort();
        let html = '';

        rows.forEach(row => {
            // Sắp xếp: VIP → Thường → Đôi/Bảo trì, cùng loại thì theo colSeat
            const rowSeats = seatsData
                .filter(s => s.rowSeat === row)
                .sort((a, b) => {
                    const diff = (TYPE_ORDER[a.seatTypeID] ?? 9) - (TYPE_ORDER[b.seatTypeID] ?? 9);
                    return diff !== 0 ? diff : a.colSeat - b.colSeat;
                });

            html += `<div class="seat-row"><span class="row-label">${row}</span>`;

            rowSeats.forEach(seat => {
                const seatName  = `${seat.rowSeat}${seat.colSeat}`;
                const cls       = TYPE_CLASS[seat.seatTypeID] ?? 'normal';
                const nextType  = CYCLE[(CYCLE.indexOf(seat.seatTypeID) + 1) % CYCLE.length];
                const tipLabel  = TYPE_LABEL[nextType] ?? '';

                html += `
                    <div class="seat ${cls}"
                        id="seat-${seat.seatID}"
                        data-id="${seat.seatID}"
                        data-type="${seat.seatTypeID}"
                        title="Click → đổi sang ${tipLabel}"
                        onclick="cycleSeatType(${seat.seatID}, '${row}')">
                        ${seatName}
                        <div class="delete-btn" onclick="deleteSeat(${seat.seatID}, event)">×</div>
                    </div>`;
            });

            html += `</div>`;
        });

        seatGrid.innerHTML = html;
    }


    // ================= XoAY VÒNG LOẠI GHẾ =================
    function cycleSeatType(seatID, row) {
        const seat = seatsData.find(s => s.seatID == seatID);
        if (!seat) return;

        // Tính loại tiếp theo trong vòng
        const currentIndex = CYCLE.indexOf(seat.seatTypeID);
        const nextTypeID   = CYCLE[(currentIndex + 1) % CYCLE.length];

        // Lấy phần tử DOM để hiện loading ngay lập tức (UX nhanh)
        const el = document.getElementById(`seat-${seatID}`);
        if (el) el.classList.add('loading');

        // Gọi API cập nhật loại ghế
        fetch(`/admins/seat/ajax-update-type/${seatID}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ seatTypeID: nextTypeID })
        })
        .then(r => {
            if (!r.ok) throw new Error('Cập nhật thất bại');
            return r.json();
        })
        .then(() => {
            // Nếu chuyển sang VIP → swap colSeat lên đầu hàng
            if (nextTypeID === 1) {
                return swapToFront(seatID, row);
            }
            // Nếu rời khỏi VIP (đang là VIP) → swap xuống sau các VIP còn lại
            if (seat.seatTypeID === 1) {
                return swapAfterVip(seatID, row);
            }
        })
        .then(() => loadSeats())
        .catch(err => {
            console.error(err);
            alert('Lỗi: ' + err.message);
            loadSeats();
        });
    }


    // ================= SWAP LÊN ĐẦU (khi → VIP) =================
    function swapToFront(seatID, row) {
        // Ghế thường đầu tiên trong hàng (colSeat nhỏ nhất) → nhường vị trí cho VIP mới
        const firstNonVip = seatsData
            .filter(s => s.rowSeat === row && s.seatTypeID !== 1 && s.seatID != seatID)
            .sort((a, b) => a.colSeat - b.colSeat)[0];

        const target = seatsData.find(s => s.seatID == seatID);

        if (!firstNonVip || !target || firstNonVip.colSeat === target.colSeat) {
            return Promise.resolve(); // không cần swap
        }

        return swapCol(seatID, firstNonVip.colSeat, firstNonVip.seatID, target.colSeat);
    }


    // ================= SWAP XUỐNG (khi rời VIP) =================
    function swapAfterVip(seatID, row) {
        // Vị trí sau tất cả VIP còn lại trong hàng
        const remainingVips = seatsData
            .filter(s => s.rowSeat === row && s.seatTypeID === 1 && s.seatID != seatID)
            .sort((a, b) => b.colSeat - a.colSeat); // colSeat lớn nhất

        const firstNormal = seatsData
            .filter(s => s.rowSeat === row && s.seatTypeID === 2)
            .sort((a, b) => a.colSeat - b.colSeat)[0];

        const target = seatsData.find(s => s.seatID == seatID);

        if (!firstNormal || !target || firstNormal.colSeat === target.colSeat) {
            return Promise.resolve();
        }

        return swapCol(seatID, firstNormal.colSeat, firstNormal.seatID, target.colSeat);
    }


    // ================= GỌI API SWAP COL =================
    function swapCol(seatID_a, colSeat_a, seatID_b, colSeat_b) {
        return fetch(`/admins/seat/ajax-swap-col`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ seatID_a, colSeat_a, seatID_b, colSeat_b })
        }).then(r => {
            if (!r.ok) throw new Error('Swap vị trí thất bại');
            return r.json();
        });
    }


    // ================= XOÁ GHẾ =================
    function deleteSeat(id, event) {
        event.stopPropagation();
        if (!confirm('Bạn có chắc muốn xoá ghế này?')) return;

        fetch(`/admins/seat/ajax-delete/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => { if (!res.ok) throw new Error('Không thể xoá ghế'); return res.json(); })
        .then(() => loadSeats())
        .catch(err => { console.error(err); alert('Xoá ghế thất bại.'); });
    }


    // ================= KHỞI TẠO =================
    document.addEventListener('DOMContentLoaded', loadSeats);
</script>

@endsection