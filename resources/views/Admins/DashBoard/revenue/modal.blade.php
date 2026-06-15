<style>
    .invoice-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .55);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(3px);
    }

    .invoice-modal-backdrop.open {
        display: flex;
    }

    .invoice-modal {
        background: #fff;
        border-radius: 20px;
        width: min(960px, 96vw);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 32px 80px rgba(0, 0, 0, .22);
        animation: modalIn .22s ease;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: translateY(24px) scale(.97);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    .invoice-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px 16px;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
        border-radius: 20px 20px 0 0;
        flex-shrink: 0;
    }

    .invoice-modal-header h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1rem;
        color: var(--text);
    }

    .modal-tab-strip {
        display: flex;
        gap: 6px;
        padding: 12px 24px;
        border-bottom: 1px solid var(--border);
        background: #fff;
        flex-shrink: 0;
    }

    .modal-tab-btn {
        padding: 6px 16px;
        border-radius: 8px;
        font-size: .82rem;
        font-weight: 600;
        border: 1.5px solid var(--border);
        background: var(--surface);
        color: var(--muted);
        cursor: pointer;
        transition: all .15s;
    }

    .modal-tab-btn.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .modal-tab-btn:hover:not(.active) {
        border-color: var(--primary);
        color: var(--primary);
        background: #eef2ff;
    }

    .invoice-modal-close {
        background: #e2e8f0;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: var(--muted);
        transition: all .15s;
    }

    .invoice-modal-close:hover {
        background: var(--danger);
        color: #fff;
    }

    .invoice-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
    }

    .invoice-modal-footer {
        padding: 12px 24px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        background: var(--surface);
        border-radius: 0 0 20px 20px;
        flex-shrink: 0;
    }

    /* Stat cards */
    .inv-stat-row {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .inv-stat {
        flex: 1;
        min-width: 130px;
        border-radius: 10px;
        padding: 12px 16px;
        border-left: 4px solid var(--primary);
        background: #eef2ff;
    }

    .inv-stat.food {
        border-color: var(--accent);
        background: #ecfeff;
    }

    .inv-stat.total {
        border-color: var(--success);
        background: #ecfdf5;
    }

    .inv-stat .s-label {
        font-size: .75rem;
        color: var(--muted);
        margin-bottom: 2px;
    }

    .inv-stat .s-val {
        font-weight: 700;
        font-size: 1rem;
        color: var(--text);
    }

    /* Movie / food highlight section inside modal */
    .modal-hl-section {
        margin-bottom: 20px;
    }

    .modal-hl-title {
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--muted);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .modal-hl-card {
        flex: 1;
        min-width: 185px;
        border-radius: 10px;
        padding: 11px 14px;
    }

    .modal-hl-card .hc-badge {
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .03em;
        margin-bottom: 4px;
        display: block;
    }

    .modal-hl-card .hc-name {
        font-weight: 600;
        font-size: .87rem;
        color: var(--text);
        margin-bottom: 3px;
    }

    .modal-hl-card .hc-rev {
        font-weight: 700;
        font-size: .94rem;
    }

    .modal-hl-card .hc-sub {
        font-size: .74rem;
        color: var(--muted);
    }

    /* Ranking table */
    .rank-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .84rem;
        margin-top: 10px;
    }

    .rank-table thead th {
        background: var(--surface);
        color: var(--muted);
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 8px 10px;
        border-bottom: 2px solid var(--border);
        text-align: left;
    }

    .rank-table tbody td {
        padding: 8px 10px;
        border-bottom: 1px solid var(--border);
        color: var(--text);
        vertical-align: middle;
    }

    .rank-table tbody tr:last-child td {
        border-bottom: none;
    }

    .rank-table tbody tr:hover {
        background: #f8fafc;
    }

    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 6px;
        font-size: .72rem;
        font-weight: 800;
    }

    .ranking-toggle {
        background: none;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 4px 12px;
        font-size: .76rem;
        font-weight: 600;
        color: var(--muted);
        cursor: pointer;
        transition: all .15s;
        white-space: nowrap;
    }

    .ranking-toggle:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Invoice list table */
    .modal-table thead th {
        background: var(--surface);
        color: var(--muted);
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .05em;
        border-bottom: 2px solid var(--border);
        padding: 10px 12px;
    }

    .modal-table tbody td {
        padding: 10px 12px;
        font-size: .86rem;
        border-color: var(--border);
    }

    .modal-table tbody tr:hover {
        background: #f8fafc;
    }

    /* Spinner */
    .inv-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 48px 0;
        color: var(--muted);
        gap: 12px;
    }

    .inv-spinner {
        width: 36px;
        height: 36px;
        border: 3px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin .7s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<div class="invoice-modal-backdrop" id="invoiceBackdrop" onclick="closeModal(event)">
    <div class="invoice-modal" id="invoiceModal">

        <div class="invoice-modal-header">
            <h5 id="invoiceModalTitle">Chi tiết hóa đơn</h5>
            <button class="invoice-modal-close" onclick="closeModal(null,true)">✕</button>
        </div>

        <div class="modal-tab-strip">
            <button class="modal-tab-btn" id="tabAll" onclick="switchModalTab('all')"><i class="bi bi-bar-chart-line"></i> Tất cả</button>
            <button class="modal-tab-btn" id="tabTicket" onclick="switchModalTab('ticket')"><i class="bi bi-ticket-perforated-fill"></i> Hóa đơn vé</button>
            <button class="modal-tab-btn" id="tabFood" onclick="switchModalTab('food')"><i class="bi bi-cup-straw"></i> Hóa đơn đồ ăn</button>
        </div>

        <div class="invoice-modal-body" id="invoiceModalBody">
            <div class="inv-loading">
                <div class="inv-spinner"></div><span>Đang tải dữ liệu...</span>
            </div>
        </div>

        <div class="invoice-modal-footer">
            <button class="btn btn-sm fw-semibold px-4" onclick="closeModal(null,true)"
                style="background:var(--surface);border:1.5px solid var(--border);border-radius:8px;color:var(--text)">
                Đóng
            </button>
        </div>
    </div>
</div>

<script>
    let _currentPeriod = null;
    let _currentType = 'all';
    let _cachedResponses = {};

    const fmt = v => new Intl.NumberFormat('vi-VN').format(Math.round(v)) + ' đ';

    // ── Detect period type ────────────────────────────────────────────
    // period formats: "2026" (year), "2026-05" (month), "2026-05-25" (day)
    function _periodLabel(period) {
        if (!period) return '';
        const parts = String(period).split('-');
        if (parts.length === 1) return 'năm ' + parts[0];
        if (parts.length === 2) return 'tháng ' + parts[1] + '/' + parts[0];
        if (parts.length === 3) return 'ngày ' + parts[2] + '/' + parts[1] + '/' + parts[0];
        return period;
    }

    // ── Open / close ──────────────────────────────────────────────────
    function openModal(period, type, label) {
        if (!period) return;
        _currentPeriod = period;
        _currentType = type || 'all';
        document.getElementById('invoiceModalTitle').textContent = label || 'Chi tiết hóa đơn';
        document.getElementById('invoiceBackdrop').classList.add('open');
        _setActiveTab(_currentType);
        loadModalData(period, _currentType);
    }

    function switchModalTab(type) {
        _currentType = type;
        _setActiveTab(type);
        loadModalData(_currentPeriod, type);
    }

    function _setActiveTab(type) {
        ['all', 'ticket', 'food'].forEach(t => {
            document.getElementById('tab' + t.charAt(0).toUpperCase() + t.slice(1))
                .classList.toggle('active', t === type);
        });
    }

    function closeModal(evt, force = false) {
        if (!force && evt && document.getElementById('invoiceModal').contains(evt.target)) return;
        document.getElementById('invoiceBackdrop').classList.remove('open');
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal(null, true);
    });

    // ── Fetch & cache ─────────────────────────────────────────────────
    function loadModalData(period, type) {
        const key = period + '|' + type;
        if (_cachedResponses[key]) {
            renderModal(document.getElementById('invoiceModalBody'), _cachedResponses[key]);
            return;
        }
        const body = document.getElementById('invoiceModalBody');
        body.innerHTML = `<div class="inv-loading"><div class="inv-spinner"></div><span>Đang tải dữ liệu...</span></div>`;

        fetch(`/admins/reports/invoices-by-period?period=${encodeURIComponent(period)}&type=${type}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                credentials: 'same-origin',
            })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => {
                if (data.error) throw new Error(data.error);
                _cachedResponses[key] = data;
                renderModal(body, data);
            })
            .catch(err => {
                body.innerHTML = `
            <div class="text-center py-5">
                <div style="font-size:2.4rem"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="mt-2 fw-semibold" style="color:#ef4444">Không thể tải dữ liệu hóa đơn.</div>
                <small style="color:#94a3b8">${err.message}</small>
            </div>`;
            });
    }

    // ── Render ────────────────────────────────────────────────────────
    function renderModal(container, data) {
        const invoices = data.invoices ?? [];
        const movieList = data.movieList ?? [];
        const foodList = data.foodList ?? [];
        const type = data.type ?? 'all';
        const period = data.period ?? _currentPeriod ?? '';
        const isTicket = type === 'ticket';
        const isFood = type === 'food';
        const isAll = type === 'all';

        // Đếm hóa đơn vé trong danh sách (dùng để quyết định hiện section phim)
        const ticketInvoices = invoices.filter(i => i.type === 'ticket');
        const foodInvoices = invoices.filter(i => i.type === 'food');

        const periodLabel = _periodLabel(period);

        // ── Stat boxes ────────────────────────────────────────────────
        let html = '<div class="inv-stat-row">';
        if (isAll || isTicket)
            html += `<div class="inv-stat"><div class="s-label"><i class="bi bi-ticket-perforated-fill"></i> Doanh thu vé</div><div class="s-val" style="color:#6366f1">${fmt(data.ticketRevenue ?? 0)}</div></div>`;
        if (isAll || isFood)
            html += `<div class="inv-stat food"><div class="s-label"><i class="bi bi-cup-straw"></i> Doanh thu đồ ăn</div><div class="s-val" style="color:#06b6d4">${fmt(data.foodRevenue ?? 0)}</div></div>`;
        if (isAll)
            html += `<div class="inv-stat total"><div class="s-label"><i class="bi bi-bank"></i> Tổng</div><div class="s-val" style="color:#10b981">${fmt(data.totalRevenue ?? 0)}</div></div>`;
        html += '</div>';

        // ── Phim nổi bật (tab Vé & Tất cả) ──────────────────────────
        const hasMovieData = data.topMovie || data.bottomMovie || movieList.length > 0;
        const hasTicketInvs = ticketInvoices.length > 0 || (data.ticketRevenue ?? 0) > 0;

        if (isAll || isTicket) {
            html += `<div class="modal-hl-section">`;
            html += `<div class="d-flex align-items-center justify-content-between mb-2">
                <div class="modal-hl-title">
                    <i class="bi bi-film" style="color:#6366f1"></i>
                    Phim nổi bật theo doanh thu vé
                    <span style="font-size:.7rem;font-weight:400;color:var(--muted);text-transform:none;letter-spacing:0">(${periodLabel})</span>
                </div>
                ${movieList.length > 2
                    ? `<button class="ranking-toggle" id="btnMovieRank" onclick="toggleRanking('movieRankList', this, ${movieList.length})"><i class="bi bi-clipboard2-fill"></i> Xem bảng xếp hạng (${movieList.length})</button>`
                    : ''}
            </div>`;

            if (hasMovieData) {
                html += `<div class="d-flex gap-2 flex-wrap mb-2">`;
                if (data.topMovie)
                    html += _hlCard('#eef2ff', '#6366f1', '🏆', 'DOANH THU CAO NHẤT',
                        data.topMovie.movieName, fmt(data.topMovie.revenue), data.topMovie.count + ' vé đã bán');
                if (data.bottomMovie)
                    html += _hlCard('#fff7ed', '#f59e0b', '<i class="bi bi-graph-down-arrow"></i>', 'DOANH THU THẤP NHẤT',
                        data.bottomMovie.movieName, fmt(data.bottomMovie.revenue), data.bottomMovie.count + ' vé đã bán');
                html += `</div>`;

                if (movieList.length) {
                    html += `<div id="movieRankList" style="display:none">
                        <table class="rank-table">
                            <thead><tr>
                                <th style="width:42px">#</th>
                                <th>Tên phim</th>
                                <th style="text-align:right">Số vé</th>
                                <th style="text-align:right">Doanh thu</th>
                            </tr></thead><tbody>`;
                    movieList.forEach((m, i) => {
                        const { rc, rb } = _rankColors(i);
                        html += `<tr>
                            <td><span class="rank-badge" style="background:${rb};color:${rc}">${i+1}</span></td>
                            <td class="fw-semibold">${m.movieName ?? 'Không rõ'}</td>
                            <td style="text-align:right;color:#64748b">${m.count ?? 0}</td>
                            <td style="text-align:right;font-weight:700;color:#6366f1">${fmt(m.revenue ?? 0)}</td>
                        </tr>`;
                    });
                    html += `</tbody></table></div>`;
                }
            } else if (hasTicketInvs) {
                html += `<div style="background:#fff7ed;border:1.5px dashed #fcd34d;border-radius:10px;padding:12px 16px;color:#92400e;font-size:.83rem">
                    <i class="bi bi-exclamation-triangle-fill"></i> Có doanh thu vé trong kỳ này nhưng chưa có dữ liệu phim. Vui lòng kiểm tra lại backend (query <code>topMovie/movieList</code> theo <code>period</code>).
                </div>`;
            } else {
                html += `<p class="text-muted small mb-0" style="color:#94a3b8">Chưa có dữ liệu vé trong kỳ này.</p>`;
            }
            html += `</div>`;
        }

        // ── Món ăn nổi bật (tab Đồ ăn & Tất cả) ─────────────────────
        const hasFoodData = data.topFood || data.bottomFood || foodList.length > 0;
        const hasFoodInvs = foodInvoices.length > 0 || (data.foodRevenue ?? 0) > 0;

        if (isAll || isFood) {
            html += `<div class="modal-hl-section">`;
            html += `<div class="d-flex align-items-center justify-content-between mb-2">
                <div class="modal-hl-title">
                    <i class="bi bi-cup-hot-fill" style="color:#06b6d4"></i>
                    Món ăn nổi bật theo doanh thu
                    <span style="font-size:.7rem;font-weight:400;color:var(--muted);text-transform:none;letter-spacing:0">(${periodLabel})</span>
                </div>
                ${foodList.length > 2
                    ? `<button class="ranking-toggle" id="btnFoodRank" onclick="toggleRanking('foodRankList', this, ${foodList.length})"><i class="bi bi-clipboard2-fill"></i> Xem bảng xếp hạng (${foodList.length})</button>`
                    : ''}
            </div>`;

            if (hasFoodData) {
                html += `<div class="d-flex gap-2 flex-wrap mb-2">`;
                if (data.topFood)
                    html += _hlCard('#ecfeff', '#06b6d4', '🏆', 'DOANH THU CAO NHẤT',
                        data.topFood.foodName, fmt(data.topFood.revenue), data.topFood.quantity + ' phần đã bán');
                if (data.bottomFood)
                    html += _hlCard('#fdf4ff', '#a855f7', '<i class="bi bi-graph-down-arrow"></i>', 'DOANH THU THẤP NHẤT',
                        data.bottomFood.foodName, fmt(data.bottomFood.revenue), data.bottomFood.quantity + ' phần đã bán');
                html += `</div>`;

                if (foodList.length) {
                    html += `<div id="foodRankList" style="display:none">
                        <table class="rank-table">
                            <thead><tr>
                                <th style="width:42px">#</th>
                                <th>Tên món</th>
                                <th style="text-align:right">Số phần</th>
                                <th style="text-align:right">Doanh thu</th>
                            </tr></thead><tbody>`;
                    foodList.forEach((f, i) => {
                        const { rc, rb } = _rankColors(i, true);
                        html += `<tr>
                            <td><span class="rank-badge" style="background:${rb};color:${rc}">${i+1}</span></td>
                            <td class="fw-semibold">${f.foodName ?? 'Không rõ'}</td>
                            <td style="text-align:right;color:#64748b">${f.quantity ?? 0}</td>
                            <td style="text-align:right;font-weight:700;color:#06b6d4">${fmt(f.revenue ?? 0)}</td>
                        </tr>`;
                    });
                    html += `</tbody></table></div>`;
                }
            } else if (hasFoodInvs) {
                html += `<div style="background:#fff7ed;border:1.5px dashed #fcd34d;border-radius:10px;padding:12px 16px;color:#92400e;font-size:.83rem">
                    <i class="bi bi-exclamation-triangle"></i> Có doanh thu đồ ăn trong kỳ này nhưng chưa có dữ liệu món. Vui lòng kiểm tra lại backend (query <code>topFood/foodList</code> theo <code>period</code>).
                </div>`;
            } else {
                html += `<p class="text-muted small mb-0" style="color:#94a3b8">Chưa có dữ liệu đồ ăn trong kỳ này.</p>`;
            }
            html += `</div>`;
        }

        // ── Danh sách hóa đơn ────────────────────────────────────────
        if (!invoices.length) {
            html += `<div class="text-center py-5" style="color:#94a3b8">
                <div style="font-size:2rem">🧾</div>
                <div class="mt-2">Không có hóa đơn nào trong kỳ này.</div>
            </div>`;
        } else {
            html += `<div style="font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">
                <i class="bi bi-clipboard2-fill"></i> Danh sách hóa đơn (${invoices.length})
            </div>
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 modal-table">
                <thead><tr>
                    <th>#</th><th>Mã HĐ</th><th>Khách hàng</th>
                    <th>Thanh toán</th><th>Ngày</th><th>Tổng tiền</th><th></th>
                </tr></thead>
                <tbody>
                ${invoices.map((inv, i) => {
                    const isF   = inv.type === 'food';
                    const color = isF ? '#06b6d4' : '#6366f1';
                    const idStr = isF ? `F-${inv.invoiceID}` : `INV-${inv.invoiceID}`;
                    const badge = isF
                        ? `<span style="background:#ecfeff;color:#06b6d4;border:1px solid #a5f3fc;font-size:.63rem;font-weight:700;padding:2px 7px;border-radius:5px">Đồ ăn</span>`
                        : `<span style="background:#eef2ff;color:#6366f1;border:1px solid #c7d2fe;font-size:.63rem;font-weight:700;padding:2px 7px;border-radius:5px">Vé</span>`;

                    // FIX: isF (đồ ăn) → /admins/foodInvoice/, !isF (vé) → /admins/invoices/
                    const action = isF
                        ? `<a href="/admins/foodInvoice/${inv.invoiceID}" target="_blank" class="btn btn-sm fw-semibold"
                              style="background:#ecfeff;color:#06b6d4;border:1px solid #a5f3fc;border-radius:7px">Chi tiết</a>`
                        : `<a href="/admins/invoices/${inv.invoiceID}" target="_blank" class="btn btn-sm fw-semibold"
                              style="background:#eef2ff;color:#6366f1;border:1px solid #c7d2fe;border-radius:7px">Chi tiết</a>`;

                    return `<tr>
                        <td style="color:#94a3b8">${i+1}</td>
                        <td class="fw-semibold text-nowrap" style="color:${color}">${idStr}</td>
                        <td>${inv.customer ?? 'Khách vãng lai'}</td>
                        <td><span class="badge fw-semibold" style="background:#ecfdf5;color:#10b981;border:1px solid #a7f3d0;border-radius:6px;padding:4px 10px">${inv.paymentMethod ?? '---'}</span></td>
                        <td class="text-nowrap" style="color:#64748b">${inv.createDate ?? ''}</td>
                        <td class="fw-bold text-nowrap" style="color:#ef4444">${fmt(inv.totalAmount ?? 0)}</td>
                        <td>${action}</td>
                    </tr>`;
                }).join('')}
                </tbody>
            </table></div>`;
        }

        container.innerHTML = html;
    }

    // ── Helpers ───────────────────────────────────────────────────────
    function _hlCard(bg, color, icon, badge, name, rev, sub) {
        return `<div class="modal-hl-card" style="background:${bg};border-left:4px solid ${color}">
            <span class="hc-badge" style="color:${color}">${icon} ${badge}</span>
            <div class="hc-name">${name}</div>
            <div class="hc-rev" style="color:${color}">${rev}</div>
            <div class="hc-sub">${sub}</div>
        </div>`;
    }

    function _rankColors(i, isFood = false) {
        const top = isFood ? '#06b6d4' : '#6366f1';
        const colors = [top, '#f59e0b', '#ef4444'];
        const bgs = [isFood ? '#ecfeff' : '#eef2ff', '#fef3c7', '#fee2e2'];
        return {
            rc: colors[i] ?? '#94a3b8',
            rb: bgs[i] ?? '#f1f5f9'
        };
    }

    // FIX: toggle text dùng data-attribute thay vì .replace() dễ bị lỗi
    function toggleRanking(id, btn, total) {
        const el = document.getElementById(id);
        if (!el) return;
        const willShow = el.style.display === 'none';
        el.style.display = willShow ? 'block' : 'none';
        if (btn) {
            btn.innerHTML = willShow ?
                '<i class="bi bi-arrow-down-square"></i> Ẩn bảng xếp hạng' :
                `<i class="bi bi-clipboard2-fill"></i> Xem bảng xếp hạng (${total ?? ''})`;
        }
    }
</script>