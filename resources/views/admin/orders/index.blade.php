<x-layouts.admin title="Quản lý Đơn hàng - CineStar" header="Quản lý Đơn hàng">
    <div class="flex flex-col gap-8">
        {{-- Header & Summary Bar --}}
        <div class="glass rounded-2xl p-6 sm:p-7 border border-[var(--color-cinema-border)] shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div>
                <h2 class="text-xl font-bold text-white tracking-wide">Danh sách & Tra cứu Đơn hàng</h2>
                <p class="text-xs sm:text-sm text-[var(--color-cinema-text-muted)] mt-1">
                    Tra cứu đơn theo khách hàng, tên phim, trạng thái hoặc khoảng thời gian và xuất báo cáo Excel
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="exportExcel()" id="btn-export" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Xuất file Excel</span>
                </button>
            </div>
        </div>

        {{-- Filters Control Panel --}}
        <div class="glass rounded-2xl p-6 sm:p-7 border border-[var(--color-cinema-border)] shadow-xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Tra cứu theo người dùng --}}
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Khách hàng</label>
                    <input type="text" id="filter-user" placeholder="Nhập tên, email hoặc mã user..."
                           class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[var(--color-cinema-primary)]">
                </div>

                {{-- Tra cứu theo tên phim --}}
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Tên phim</label>
                    <input type="text" id="filter-movie" placeholder="Nhập tên phim..."
                           class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-[var(--color-cinema-primary)]">
                </div>

                {{-- Lọc trạng thái --}}
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Trạng thái đơn hàng</label>
                    <select id="filter-status" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]">
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
                    <select id="filter-period-type" onchange="onPeriodTypeChange()" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]">
                        <option value="all">Tất cả thời gian</option>
                        <option value="day">Theo ngày cụ thể</option>
                        <option value="month">Theo tháng</option>
                        <option value="year">Theo năm</option>
                        <option value="custom">Khoảng ngày tùy chọn</option>
                    </select>
                </div>
            </div>

            {{-- Dòng input ngày tháng linh hoạt --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-[var(--color-cinema-border)]/60">
                <div id="filter-input-day" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Chọn ngày</label>
                    <input type="date" id="filter-date" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]" value="{{ now()->toDateString() }}">
                </div>

                <div id="filter-input-month" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Chọn tháng</label>
                    <input type="month" id="filter-month" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]" value="{{ now()->format('Y-m') }}">
                </div>

                <div id="filter-input-year" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Chọn năm</label>
                    <input type="number" id="filter-year" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]" value="{{ now()->format('Y') }}" min="2020" max="2035">
                </div>

                <div id="filter-input-from" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Từ ngày</label>
                    <input type="date" id="filter-from-date" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]">
                </div>

                <div id="filter-input-to" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Đến ngày</label>
                    <input type="date" id="filter-to-date" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]">
                </div>

                {{-- Action buttons --}}
                <div class="sm:col-span-2 md:col-span-2 flex items-end gap-3">
                    <button type="button" onclick="applyFilters(1)" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-[var(--color-cinema-primary)] hover:bg-red-700 text-white shadow-lg shadow-red-600/30 transition-all cursor-pointer">
                        Tìm kiếm
                    </button>
                    <button type="button" onclick="resetFilters()" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-[var(--color-cinema-card)] hover:bg-[var(--color-cinema-border)] text-[var(--color-cinema-text-muted)] hover:text-white transition-all cursor-pointer">
                        Đặt lại
                    </button>
                    <span id="filter-count-badge" class="text-xs text-[var(--color-cinema-text-muted)] self-center ml-auto font-medium"></span>
                </div>
            </div>
        </div>

        {{-- Table Container --}}
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
                            <th class="text-left py-4 px-4">Ngày tạo</th>
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

    <script>
        let currentPage = 1;
        let lastPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            // Nhấn Enter trong các ô input để tìm kiếm nhanh
            ['filter-user', 'filter-movie'].forEach(id => {
                document.getElementById(id).addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        applyFilters(1);
                    }
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
            else if (val === 'month') inputMonth.classList.remove('hidden');
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
            } else if (periodType === 'month') {
                params.append('month', document.getElementById('filter-month').value);
            } else if (periodType === 'year') {
                params.append('year', document.getElementById('filter-year').value);
            } else if (periodType === 'custom') {
                const from = document.getElementById('filter-from-date').value;
                const to = document.getElementById('filter-to-date').value;
                if (from) params.append('from_date', from);
                if (to) params.append('to_date', to);
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
                <div class="inline-block w-6 h-6 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin mr-2"></div>
                Đang tìm kiếm...
            </td></tr>`;

            fetch(`/api/admin/orders?${params.toString()}`, {
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
        }

        function resetFilters() {
            document.getElementById('filter-user').value = '';
            document.getElementById('filter-movie').value = '';
            document.getElementById('filter-status').value = 'all';
            document.getElementById('filter-period-type').value = 'all';
            document.getElementById('filter-from-date').value = '';
            document.getElementById('filter-to-date').value = '';
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
                completed: { label: 'Hoàn tất', class: 'bg-blue-500/20 text-blue-400 border border-blue-500/30' },
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
                const createdTime = o.created_at ? new Date(o.created_at).toLocaleString('vi-VN') : '—';

                return `
                <tr class="border-b border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)]/40 transition-colors">
                    <td class="py-3.5 px-4 font-mono font-medium text-white">#${o.id}</td>
                    <td class="py-3.5 px-4">
                        <span class="font-medium text-white block cursor-pointer hover:underline hover:text-[var(--color-cinema-accent)]" onclick="filterByUser('${o.user?.name || ''}')">${o.user?.name || 'Khách vãng lai'}</span>
                        <span class="text-xs text-[var(--color-cinema-text-muted)]">${o.user?.email || '—'}</span>
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="text-white block font-medium cursor-pointer hover:underline hover:text-[var(--color-cinema-accent)]" onclick="filterByMovie('${o.show?.movie?.title || ''}')">${o.show?.movie?.title || '—'}</span>
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
                    <td class="py-3.5 px-4 text-xs text-[var(--color-cinema-text-muted)] whitespace-nowrap">${createdTime}</td>
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

            // Previous button
            html += `<button onclick="applyFilters(${currentPage - 1})" ${currentPage === 1 ? 'disabled class="opacity-40 cursor-not-allowed"' : 'class="hover:bg-[var(--color-cinema-card)]"'} class="px-3 py-1.5 rounded-lg border border-[var(--color-cinema-border)] text-xs text-white transition-all">Trước</button>`;

            // Page numbers
            for (let i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    if (i === currentPage) {
                        html += `<button class="px-3 py-1.5 rounded-lg bg-[var(--color-cinema-primary)] text-xs font-bold text-white shadow-sm">${i}</button>`;
                    } else {
                        html += `<button onclick="applyFilters(${i})" class="px-3 py-1.5 rounded-lg border border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)] text-xs text-[var(--color-cinema-text-muted)] hover:text-white transition-all">${i}</button>`;
                    }
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += `<span class="px-1 text-xs text-slate-500">...</span>`;
                }
            }

            // Next button
            html += `<button onclick="applyFilters(${currentPage + 1})" ${currentPage === lastPage ? 'disabled class="opacity-40 cursor-not-allowed"' : 'class="hover:bg-[var(--color-cinema-card)]"'} class="px-3 py-1.5 rounded-lg border border-[var(--color-cinema-border)] text-xs text-white transition-all">Sau</button>`;

            btns.innerHTML = html;
        }

        function filterByUser(userName) {
            if (!userName) return;
            document.getElementById('filter-user').value = userName;
            applyFilters(1);
        }

        function filterByMovie(movieTitle) {
            if (!movieTitle) return;
            document.getElementById('filter-movie').value = movieTitle;
            applyFilters(1);
        }

        function exportExcel() {
            const params = getFilterParams();
            // Điều hướng tải file trực tiếp qua endpoint /admin/orders/export
            window.location.href = `/admin/orders/export?${params.toString()}`;
        }
    </script>
</x-layouts.admin>
