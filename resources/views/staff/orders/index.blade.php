<x-layouts.staff title="Đơn hàng & Doanh thu - Nhân viên" header="Đơn hàng & Doanh thu">
    <div class="flex flex-col gap-8">
        {{-- Shift Turnover Summary Header --}}
        <div class="glass rounded-2xl p-6 sm:p-7 border border-[var(--color-cinema-border)] shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div>
                <h2 class="text-xl font-bold text-white tracking-wide">Doanh thu & Đơn hàng kết ca</h2>
                <p class="text-xs sm:text-sm text-[var(--color-cinema-text-muted)] mt-1">
                    Theo dõi doanh thu thực tế, tiền mặt, chuyển khoản và chi tiết vé để kiểm và bàn giao ca
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="exportStaffExcel()" id="btn-export" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Xuất báo cáo Excel</span>
                </button>
            </div>
        </div>

        {{-- Shift Revenue KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6" id="shift-stats-grid">
            <div class="glass rounded-2xl p-6 sm:p-7 border border-[var(--color-cinema-border)] shadow-xl hover:border-purple-500/40 transition-all">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wider text-[var(--color-cinema-text-muted)] font-semibold">Tổng doanh thu kỳ lọc</p>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-semibold" id="card-period-tag">Hôm nay</span>
                </div>
                <p class="text-3xl font-bold text-purple-400 mt-3" id="stat-total">—</p>
                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-2">Tổng số vé bán: <span id="stat-all-tickets" class="text-white font-medium">—</span> vé</p>
            </div>

            <div class="glass rounded-2xl p-6 sm:p-7 border border-[var(--color-cinema-border)] shadow-xl hover:border-amber-500/40 transition-all">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wider text-amber-300 font-semibold">Tiền mặt tại két</p>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 font-semibold">Tiền mặt</span>
                </div>
                <p class="text-3xl font-bold text-amber-400 mt-3" id="stat-cash">—</p>
                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-2">Số đơn tiền mặt: <span id="stat-cash-orders" class="text-white font-medium">—</span> đơn</p>
            </div>

            <div class="glass rounded-2xl p-6 sm:p-7 border border-[var(--color-cinema-border)] shadow-xl hover:border-emerald-500/40 transition-all">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wider text-emerald-300 font-semibold">Chuyển khoản</p>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-semibold">Chuyển khoản</span>
                </div>
                <p class="text-3xl font-bold text-emerald-400 mt-3" id="stat-transfer">—</p>
                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-2">Số đơn chuyển khoản: <span id="stat-transfer-orders" class="text-white font-medium">—</span> đơn</p>
            </div>
        </div>

        {{-- Filter Control Panel (Tương tự Admin) --}}
        <div class="glass rounded-2xl p-6 sm:p-7 border border-[var(--color-cinema-border)] shadow-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Tra cứu theo khách hàng --}}
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Khách hàng</label>
                    <input type="text" id="filter-user" placeholder="Nhập tên, email hoặc mã user..."
                           class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[var(--color-cinema-success)]">
                </div>

                {{-- Tra cứu theo tên phim --}}
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Tên phim</label>
                    <input type="text" id="filter-movie" placeholder="Nhập tên phim..."
                           class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[var(--color-cinema-success)]">
                </div>

                {{-- Lọc trạng thái --}}
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Trạng thái đơn hàng</label>
                    <select id="filter-status" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-success)]">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="paid">Đã thanh toán</option>
                        <option value="completed">Hoàn tất / Đã xem</option>
                        <option value="pending">Chờ thanh toán</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>

                {{-- Chế độ thời gian --}}
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Khoảng thời gian</label>
                    <select id="filter-period-type" onchange="onPeriodTypeChange()" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-success)]">
                        <option value="day">Theo ngày cụ thể</option>
                        <option value="week">Theo tuần này</option>
                        <option value="month">Theo tháng</option>
                        <option value="year">Theo năm</option>
                        <option value="custom">Khoảng ngày tùy chọn</option>
                        <option value="all">Tất cả thời gian</option>
                    </select>
                </div>
            </div>

            {{-- Dòng input ngày tháng linh hoạt --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-[var(--color-cinema-border)]/60 items-end">
                <div id="filter-input-day">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Chọn ngày</label>
                    <input type="date" id="filter-date" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-success)]" value="{{ now()->toDateString() }}">
                </div>

                <div id="filter-input-month" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Chọn tháng</label>
                    <input type="month" id="filter-month" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-success)]" value="{{ now()->format('Y-m') }}">
                </div>

                <div id="filter-input-year" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Chọn năm</label>
                    <select id="filter-year" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-success)]">
                        @for($y = now()->year; $y >= 2024; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div id="filter-input-from" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Từ ngày</label>
                    <input type="date" id="filter-from-date" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-success)]" value="{{ now()->subDays(7)->toDateString() }}">
                </div>

                <div id="filter-input-to" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Đến ngày</label>
                    <input type="date" id="filter-to-date" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-success)]" value="{{ now()->toDateString() }}">
                </div>

                {{-- Action buttons --}}
                <div class="sm:col-span-2 md:col-span-2 flex items-center gap-3">
                    <button type="button" onclick="applyFilters(1)" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[var(--color-cinema-success)] hover:brightness-110 text-white shadow-lg shadow-emerald-500/20 transition-all cursor-pointer">
                        Tìm kiếm
                    </button>
                    <button type="button" onclick="resetFilters()" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-[var(--color-cinema-card)] hover:bg-[var(--color-cinema-border)] text-[var(--color-cinema-text-muted)] hover:text-white transition-all cursor-pointer">
                        Đặt lại
                    </button>
                    <span id="filter-count-badge" class="text-xs text-[var(--color-cinema-text-muted)] self-center ml-auto font-medium"></span>
                </div>
            </div>
        </div>

        {{-- Orders Table Container --}}
        <div class="glass rounded-2xl border border-[var(--color-cinema-border)] shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[var(--color-cinema-text-muted)] border-b border-[var(--color-cinema-border)] text-xs uppercase tracking-wider bg-[var(--color-cinema-card)]/50">
                            <th class="text-left py-4 px-4">Mã ĐH</th>
                            <th class="text-left py-4 px-4">Khách hàng</th>
                            <th class="text-left py-4 px-4">Phim & Suất chiếu</th>
                            <th class="text-left py-4 px-4">Ghế ngồi</th>
                            <th class="text-center py-4 px-4">Hình thức</th>
                            <th class="text-right py-4 px-4">Tổng tiền</th>
                            <th class="text-center py-4 px-4">Trạng thái</th>
                            <th class="text-center py-4 px-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="orders-table">
                        <tr><td colspan="8" class="text-center py-10 text-[var(--color-cinema-text-muted)]">Đang tải dữ liệu...</td></tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination Bar --}}
            <div class="p-4 sm:p-5 border-t border-[var(--color-cinema-border)] flex flex-col sm:flex-row items-center justify-between gap-4 bg-[var(--color-cinema-card)]/20" id="pagination-container">
                <div class="text-xs text-[var(--color-cinema-text-muted)]" id="pagination-info">
                    Hiển thị 0 đơn hàng
                </div>
                <div class="flex items-center gap-2" id="pagination-buttons">
                    <!-- Loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    {{-- Order Detail Modal (Xem chi tiết đơn hàng cho nhân viên) --}}
    <div id="order-detail-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="glass rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 border border-[var(--color-cinema-border)] shadow-2xl relative">
            <button onclick="closeDetailModal()" class="absolute top-5 right-5 text-[var(--color-cinema-text-muted)] hover:text-white text-xl font-bold cursor-pointer">
                ✕
            </button>
            <div id="modal-content">
                <div class="text-center py-12 text-[var(--color-cinema-text-muted)]">Đang tải thông tin chi tiết đơn hàng...</div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let lastPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            // Nhấn Enter trong ô tìm kiếm
            ['filter-user', 'filter-movie'].forEach(id => {
                document.getElementById(id).addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') applyFilters(1);
                });
            });

            document.getElementById('filter-status').addEventListener('change', () => applyFilters(1));

            applyFilters(1);
        });

        function onPeriodTypeChange() {
            const val = document.getElementById('filter-period-type').value;
            const inputDay = document.getElementById('filter-input-day');
            const inputMonth = document.getElementById('filter-input-month');
            const inputYear = document.getElementById('filter-input-year');
            const inputFrom = document.getElementById('filter-input-from');
            const inputTo = document.getElementById('filter-input-to');

            inputDay.classList.add('hidden');
            inputMonth.classList.add('hidden');
            inputYear.classList.add('hidden');
            inputFrom.classList.add('hidden');
            inputTo.classList.add('hidden');

            if (val === 'day') inputDay.classList.remove('hidden');
            else if (val === 'week') {
                inputFrom.classList.remove('hidden');
                inputTo.classList.remove('hidden');
                const today = new Date();
                const past7 = new Date(today);
                past7.setDate(today.getDate() - 6);
                document.getElementById('filter-to-date').value = today.toISOString().split('T')[0];
                document.getElementById('filter-from-date').value = past7.toISOString().split('T')[0];
            } else if (val === 'month') inputMonth.classList.remove('hidden');
            else if (val === 'year') inputYear.classList.remove('hidden');
            else if (val === 'custom') {
                inputFrom.classList.remove('hidden');
                inputTo.classList.remove('hidden');
            }
        }

        function getFilterParams() {
            const params = new URLSearchParams();
            
            const userSearch = document.getElementById('filter-user').value.trim();
            if (userSearch) params.append('user_search', userSearch);

            const movieTitle = document.getElementById('filter-movie').value.trim();
            if (movieTitle) params.append('movie_title', movieTitle);

            const status = document.getElementById('filter-status').value;
            if (status && status !== 'all') params.append('status', status);

            const periodType = document.getElementById('filter-period-type').value;
            params.append('period_type', periodType);

            if (periodType === 'day') {
                params.append('date', document.getElementById('filter-date').value);
            } else if (periodType === 'week' || periodType === 'custom') {
                const from = document.getElementById('filter-from-date').value;
                const to = document.getElementById('filter-to-date').value;
                if (from) params.append('from_date', from);
                if (to) params.append('to_date', to);
            } else if (periodType === 'month') {
                params.append('month', document.getElementById('filter-month').value);
            } else if (periodType === 'year') {
                params.append('year', document.getElementById('filter-year').value);
            }

            return params;
        }

        function applyFilters(page = 1) {
            currentPage = page;
            const params = getFilterParams();
            params.append('page', page);
            params.append('per_page', 10);

            const tbody = document.getElementById('orders-table');
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-[var(--color-cinema-text-muted)]">
                <div class="inline-block w-6 h-6 border-2 border-[var(--color-cinema-success)] border-t-transparent rounded-full animate-spin mr-2"></div>
                Đang tìm kiếm...
            </td></tr>`;

            // 1. Tải danh sách đơn hàng
            fetch(`/api/staff/orders?${params.toString()}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                const orders = result.data || [];
                const total = result.total || 0;
                currentPage = result.current_page || 1;
                lastPage = result.last_page || 1;

                document.getElementById('filter-count-badge').textContent = `Tìm thấy ${total} đơn hàng`;
                renderOrders(orders);
                renderPagination(result);
            })
            .catch(err => {
                console.error('Error fetching orders:', err);
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-10 text-red-400">Không thể tải dữ liệu đơn hàng. Vui lòng thử lại.</td></tr>`;
            });

            // 2. Tải số liệu doanh thu kết ca theo bộ lọc
            loadShiftRevenueStats(params);
        }

        function loadShiftRevenueStats(params) {
            fetch(`/api/staff/statistics?${params.toString()}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                const f = result.data?.filtered || {};
                const total = Number(f.total_revenue || 0);
                const cash = Number(f.cash_revenue || 0);
                const transfer = Number(f.transfer_revenue || 0);
                const totalTickets = Number(f.total_tickets || 0);
                const cashOrders = Number(f.cash_tickets || 0);
                const transferOrders = Number(f.transfer_tickets || 0);

                document.getElementById('stat-total').textContent = total.toLocaleString('vi-VN') + 'đ';
                document.getElementById('stat-all-tickets').textContent = totalTickets;

                document.getElementById('stat-cash').textContent = cash.toLocaleString('vi-VN') + 'đ';
                document.getElementById('stat-cash-orders').textContent = cashOrders;

                document.getElementById('stat-transfer').textContent = transfer.toLocaleString('vi-VN') + 'đ';
                document.getElementById('stat-transfer-orders').textContent = transferOrders;

                const periodType = document.getElementById('filter-period-type').value;
                const periodTag = document.getElementById('card-period-tag');
                if (periodType === 'day') periodTag.textContent = `Ngày ${document.getElementById('filter-date').value}`;
                else if (periodType === 'week') periodTag.textContent = 'Tuần này';
                else if (periodType === 'month') periodTag.textContent = `Tháng ${document.getElementById('filter-month').value}`;
                else if (periodType === 'year') periodTag.textContent = `Năm ${document.getElementById('filter-year').value}`;
                else if (periodType === 'custom') periodTag.textContent = 'Khoảng ngày';
                else periodTag.textContent = 'Toàn thời gian';
            })
            .catch(err => console.error('Error fetching shift statistics:', err));
        }

        function resetFilters() {
            document.getElementById('filter-user').value = '';
            document.getElementById('filter-movie').value = '';
            document.getElementById('filter-status').value = 'all';
            document.getElementById('filter-period-type').value = 'day';
            document.getElementById('filter-date').value = '{{ now()->toDateString() }}';
            onPeriodTypeChange();
            applyFilters(1);
        }

        function renderOrders(orders) {
            const tbody = document.getElementById('orders-table');
            if (orders.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-12 text-[var(--color-cinema-text-muted)]">Không tìm thấy đơn hàng nào phù hợp với bộ lọc.</td></tr>`;
                return;
            }

            const statusMap = {
                pending: { label: 'Chờ thanh toán', class: 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' },
                paid: { label: 'Đã thanh toán', class: 'bg-green-500/20 text-green-400 border border-green-500/30' },
                completed: { label: 'Hoàn tất / Đã xem', class: 'bg-blue-500/20 text-blue-400 border border-blue-500/30' },
                cancelled: { label: 'Đã hủy', class: 'bg-red-500/20 text-red-400 border border-red-500/30' }
            };

            tbody.innerHTML = orders.map(o => {
                const st = statusMap[o.status] || { label: o.status, class: 'bg-gray-500/20 text-gray-400' };
                const pm = (o.payment_method || 'Chưa chọn').toLowerCase();
                const isCash = (pm === 'cash' || pm === 'tiền mặt');
                const pmBadge = isCash
                    ? '<span class="text-[11px] px-2.5 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/30 font-medium">Tiền mặt</span>'
                    : `<span class="text-[11px] px-2.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-medium">${(o.payment_method || 'Chuyển khoản').toUpperCase()}</span>`;

                const seats = (o.seats || []).map(s => {
                    if (!s.seat) return '';
                    const r = s.seat.row_label || s.seat.row || '';
                    const n = s.seat.seat_number || s.seat.number || '';
                    return `${r}${n}`;
                }).filter(Boolean).join(', ');

                const showTime = o.show?.start_time ? new Date(o.show.start_time).toLocaleString('vi-VN') : '—';

                return `
                <tr class="border-b border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)]/40 transition-colors">
                    <td class="py-3.5 px-4 font-mono font-medium text-white">#${o.id}</td>
                    <td class="py-3.5 px-4">
                        <span class="font-medium text-white block">${o.user?.name || 'Khách vãng lai'}</span>
                        <span class="text-xs text-[var(--color-cinema-text-muted)]">${o.user?.email || '—'}</span>
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="text-white block font-medium">${o.show?.movie?.title || '—'}</span>
                        <span class="text-xs text-[var(--color-cinema-text-muted)]">${o.show?.room?.name || ''} • ${showTime}</span>
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="text-xs px-2 py-0.5 rounded bg-slate-800 text-[var(--color-cinema-accent)] font-semibold border border-slate-700">${seats || '—'}</span>
                    </td>
                    <td class="py-3.5 px-4 text-center">${pmBadge}</td>
                    <td class="py-3.5 px-4 text-right font-bold text-[var(--color-cinema-accent)]">${Number(o.total_amount).toLocaleString('vi-VN')}đ</td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium ${st.class}">${st.label}</span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <button onclick="viewOrderDetail(${o.id})" class="px-3 py-1 rounded-lg text-xs font-semibold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/30 transition-all cursor-pointer">
                            Chi tiết
                        </button>
                    </td>
                </tr>
                `;
            }).join('');
        }

        function renderPagination(data) {
            const info = document.getElementById('pagination-info');
            const btns = document.getElementById('pagination-buttons');

            const from = data.from || 0;
            const to = data.to || 0;
            const total = data.total || 0;
            info.textContent = `Hiển thị ${from} - ${to} trên tổng số ${total} đơn hàng (Trang ${currentPage}/${lastPage})`;

            if (lastPage <= 1) {
                btns.innerHTML = '';
                return;
            }

            let html = '';
            html += `<button onclick="applyFilters(${currentPage - 1})" ${currentPage === 1 ? 'disabled class="opacity-40 cursor-not-allowed"' : 'class="hover:bg-[var(--color-cinema-card)]"'} class="px-3 py-1.5 rounded-lg border border-[var(--color-cinema-border)] text-xs text-white transition-all">Trước</button>`;

            for (let i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    if (i === currentPage) {
                        html += `<button class="px-3 py-1.5 rounded-lg bg-[var(--color-cinema-success)] text-xs font-bold text-white shadow-sm">${i}</button>`;
                    } else {
                        html += `<button onclick="applyFilters(${i})" class="px-3 py-1.5 rounded-lg border border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)] text-xs text-[var(--color-cinema-text-muted)] hover:text-white transition-all">${i}</button>`;
                    }
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += `<span class="px-1 text-xs text-slate-500">...</span>`;
                }
            }

            html += `<button onclick="applyFilters(${currentPage + 1})" ${currentPage === lastPage ? 'disabled class="opacity-40 cursor-not-allowed"' : 'class="hover:bg-[var(--color-cinema-card)]"'} class="px-3 py-1.5 rounded-lg border border-[var(--color-cinema-border)] text-xs text-white transition-all">Sau</button>`;

            btns.innerHTML = html;
        }

        function viewOrderDetail(orderId) {
            const modal = document.getElementById('order-detail-modal');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            content.innerHTML = `<div class="text-center py-12 text-[var(--color-cinema-text-muted)]">
                <div class="inline-block w-8 h-8 border-2 border-[var(--color-cinema-success)] border-t-transparent rounded-full animate-spin mb-2"></div>
                <p>Đang tải chi tiết đơn hàng #${orderId}...</p>
            </div>`;

            fetch(`/api/staff/orders/${orderId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                const o = result.data;
                const statusMap = {
                    pending: { label: 'Chờ thanh toán', class: 'bg-yellow-500/20 text-yellow-400' },
                    paid: { label: 'Đã thanh toán', class: 'bg-green-500/20 text-green-400' },
                    completed: { label: 'Hoàn tất / Đã xem', class: 'bg-blue-500/20 text-blue-400' },
                    cancelled: { label: 'Đã hủy', class: 'bg-red-500/20 text-red-400' }
                };
                const st = statusMap[o.status] || { label: o.status, class: 'bg-gray-500/20 text-gray-400' };

                const seats = (o.seats || []).map(s => {
                    const label = s.seat ? (s.seat.row_label || s.seat.row || '') + (s.seat.seat_number || s.seat.number || '') : '';
                    const price = Number(s.price || 0).toLocaleString('vi-VN');
                    return `<span class="inline-block px-2.5 py-1 rounded bg-slate-800 border border-slate-700 text-xs font-semibold text-white mr-2 mb-2">${label}: ${price}đ</span>`;
                }).join('');

                const snacks = (o.snacks || []).map(sn => {
                    const name = sn.snack?.name || 'Món ăn';
                    const qty = sn.quantity || 1;
                    const price = Number(sn.price || 0).toLocaleString('vi-VN');
                    return `<li class="flex justify-between py-1 text-xs border-b border-white/5"><span class="text-white">${name} x${qty}</span><span class="text-[var(--color-cinema-accent)] font-semibold">${price}đ</span></li>`;
                }).join('');

                const showTime = o.show?.start_time ? new Date(o.show.start_time).toLocaleString('vi-VN') : '—';
                const createdTime = o.created_at ? new Date(o.created_at).toLocaleString('vi-VN') : '—';

                content.innerHTML = `
                    <div class="space-y-6 text-sm">
                        <div class="border-b border-[var(--color-cinema-border)] pb-4 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-white">Chi tiết đơn hàng #${o.id}</h3>
                                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-0.5">Tạo lúc: ${createdTime}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold ${st.class}">${st.label}</span>
                        </div>

                        {{-- Thông tin khách hàng & Suất chiếu --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)]">
                                <span class="text-xs text-[var(--color-cinema-text-muted)] block uppercase tracking-wider mb-1">Khách hàng</span>
                                <p class="font-bold text-white text-base">${o.user?.name || 'Khách vãng lai'}</p>
                                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-0.5">Email: ${o.user?.email || '—'}</p>
                            </div>
                            <div class="p-4 rounded-xl bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)]">
                                <span class="text-xs text-[var(--color-cinema-text-muted)] block uppercase tracking-wider mb-1">Thông tin chiếu</span>
                                <p class="font-bold text-white text-base">${o.show?.movie?.title || '—'}</p>
                                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-0.5">${o.show?.room?.name || 'Phòng chiếu'} • ${showTime}</p>
                            </div>
                        </div>

                        {{-- Danh sách ghế --}}
                        <div>
                            <span class="text-xs text-[var(--color-cinema-text-muted)] block uppercase tracking-wider mb-2">Ghế ngồi đã đặt</span>
                            <div class="flex flex-wrap">${seats || '<span class="text-slate-500">Không có ghế</span>'}</div>
                        </div>

                        {{-- Bắp nước nếu có --}}
                        ${snacks ? `
                        <div>
                            <span class="text-xs text-[var(--color-cinema-text-muted)] block uppercase tracking-wider mb-2">Bắp nước & Combo</span>
                            <ul class="rounded-xl p-3 bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] space-y-1">${snacks}</ul>
                        </div>
                        ` : ''}

                        {{-- Thanh toán --}}
                        <div class="p-4 rounded-xl bg-slate-900/60 border border-[var(--color-cinema-border)] flex items-center justify-between">
                            <div>
                                <span class="text-xs text-[var(--color-cinema-text-muted)] block">Phương thức thanh toán</span>
                                <span class="text-sm font-bold text-white uppercase">${o.payment_method || 'Chưa chọn'}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-[var(--color-cinema-text-muted)] block">Tổng tiền</span>
                                <span class="text-2xl font-bold text-[var(--color-cinema-accent)]">${Number(o.total_amount).toLocaleString('vi-VN')}đ</span>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button onclick="closeDetailModal()" class="px-5 py-2.5 rounded-xl bg-[var(--color-cinema-card)] text-white hover:bg-[var(--color-cinema-border)] text-sm font-semibold transition-all">
                                Đóng
                            </button>
                        </div>
                    </div>
                `;
            })
            .catch(err => {
                console.error('Error fetching order detail:', err);
                content.innerHTML = `<div class="text-center py-8 text-red-400">Không thể tải thông tin chi tiết đơn hàng.</div>`;
            });
        }

        function closeDetailModal() {
            document.getElementById('order-detail-modal').classList.add('hidden');
        }

        function exportStaffExcel() {
            const params = getFilterParams();
            window.location.href = `/staff/orders/export?${params.toString()}`;
        }
    </script>
</x-layouts.staff>

