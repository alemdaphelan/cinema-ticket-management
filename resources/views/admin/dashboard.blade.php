<x-layouts.admin title="Dashboard - Admin" header="Dashboard Thống Kê">
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="flex flex-col gap-8">
        {{-- Filter & Control Header Panel (Đồng bộ nút chọn và bộ lọc) --}}
        <div class="glass rounded-2xl p-6 sm:p-8 border border-[var(--color-cinema-border)] shadow-xl">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 pb-6 border-b border-[var(--color-cinema-border)]">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-wide">
                        Bộ lọc & Thống kê doanh thu
                    </h2>
                    <p class="text-xs sm:text-sm text-[var(--color-cinema-text-muted)] mt-1">
                        Chọn nhanh mốc thống kê theo ngày, tuần, tháng, năm hoặc lọc khoảng thời gian tùy chọn
                    </p>
                </div>

                {{-- Nút chọn nhanh (Đồng bộ 2 chiều với các ô chọn ngày/tháng/năm) --}}
                <div class="flex items-center bg-[var(--color-cinema-card)] p-1.5 rounded-xl border border-[var(--color-cinema-border)] shrink-0 gap-1">
                    <button type="button" onclick="selectQuickFilter('day')" id="btn-quick-day" class="px-4 py-2 rounded-lg text-xs font-semibold transition-all quick-filter-btn active-filter bg-[var(--color-cinema-primary)] text-white shadow-sm cursor-pointer">
                        Theo Ngày
                    </button>
                    <button type="button" onclick="selectQuickFilter('week')" id="btn-quick-week" class="px-4 py-2 rounded-lg text-xs font-semibold text-[var(--color-cinema-text-muted)] hover:text-white transition-all quick-filter-btn cursor-pointer">
                        Theo Tuần
                    </button>
                    <button type="button" onclick="selectQuickFilter('month')" id="btn-quick-month" class="px-4 py-2 rounded-lg text-xs font-semibold text-[var(--color-cinema-text-muted)] hover:text-white transition-all quick-filter-btn cursor-pointer">
                        Theo Tháng
                    </button>
                    <button type="button" onclick="selectQuickFilter('year')" id="btn-quick-year" class="px-4 py-2 rounded-lg text-xs font-semibold text-[var(--color-cinema-text-muted)] hover:text-white transition-all quick-filter-btn cursor-pointer">
                        Theo Năm
                    </button>
                    <button type="button" onclick="selectQuickFilter('all')" id="btn-quick-all" class="px-4 py-2 rounded-lg text-xs font-semibold text-[var(--color-cinema-text-muted)] hover:text-white transition-all quick-filter-btn cursor-pointer">
                        Toàn bộ
                    </button>
                </div>
            </div>

            {{-- Filter Inputs Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 mt-6 items-end">
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Chế độ lọc dữ liệu</label>
                    <select id="filter-period-type" onchange="onPeriodTypeDropdownChange()" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]">
                        <option value="day">Theo ngày cụ thể</option>
                        <option value="week">Theo tuần này</option>
                        <option value="month">Theo tháng</option>
                        <option value="year">Theo năm</option>
                        <option value="custom">Khoảng ngày tùy chọn</option>
                        <option value="all">Tất cả thời gian</option>
                    </select>
                </div>

                {{-- Input theo ngày --}}
                <div id="filter-input-day">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Chọn ngày</label>
                    <input type="date" id="filter-date" onchange="loadStatistics()" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]" value="{{ now()->toDateString() }}">
                </div>

                {{-- Input theo tháng --}}
                <div id="filter-input-month" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Chọn tháng</label>
                    <input type="month" id="filter-month" onchange="loadStatistics()" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]" value="{{ now()->format('Y-m') }}">
                </div>

                {{-- Input theo năm --}}
                <div id="filter-input-year" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Chọn năm</label>
                    <select id="filter-year" onchange="loadStatistics()" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]">
                        @for($y = now()->year; $y >= 2024; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Input khoảng ngày --}}
                <div id="filter-input-from" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Từ ngày</label>
                    <input type="date" id="filter-from-date" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]" value="{{ now()->subDays(7)->toDateString() }}">
                </div>
                <div id="filter-input-to" class="hidden">
                    <label class="block text-xs font-semibold text-[var(--color-cinema-text-muted)] mb-1.5">Đến ngày</label>
                    <input type="date" id="filter-to-date" class="w-full bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)] rounded-xl px-3.5 py-2.5 text-sm text-white focus:outline-none focus:border-[var(--color-cinema-primary)]" value="{{ now()->toDateString() }}">
                </div>

                <div>
                    <button type="button" onclick="loadStatistics()" class="btn-primary w-full py-2.5 text-sm font-semibold flex items-center justify-center gap-2 shadow-lg shadow-red-500/25 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Áp dụng bộ lọc</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Overview Cards (Đồng bộ 100% dữ liệu theo bộ lọc đang chọn) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="stats-grid">
            {{-- Tổng doanh thu theo kỳ lọc --}}
            <div class="glass rounded-2xl p-6 sm:p-7 border border-[var(--color-cinema-border)] shadow-xl hover:border-purple-500/40 transition-all">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wider text-[var(--color-cinema-text-muted)] font-semibold" id="card-revenue-title">Doanh thu kỳ lọc</p>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-semibold" id="card-period-tag">Hôm nay</span>
                </div>
                <p class="text-3xl font-bold text-purple-400 mt-3" id="stat-total">—</p>
                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-2">Tổng số vé bán: <span id="stat-all-tickets" class="text-white font-medium">—</span> vé</p>
            </div>

            {{-- Phim đang chiếu --}}
            <div class="glass rounded-2xl p-6 sm:p-7 border border-[var(--color-cinema-border)] shadow-xl hover:border-blue-500/40 transition-all">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wider text-[var(--color-cinema-text-muted)] font-semibold">Phim đang chiếu</p>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-500/20 text-blue-300 font-semibold">Hiện tại</span>
                </div>
                <p class="text-3xl font-bold text-blue-400 mt-3" id="stat-movies">—</p>
                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-2">Suất chiếu hôm nay: <span id="stat-upcoming-shows" class="text-white font-medium">—</span> suất</p>
            </div>

            {{-- Tỷ lệ thanh toán theo kỳ lọc --}}
            <div class="glass rounded-2xl p-6 sm:p-7 border border-[var(--color-cinema-border)] shadow-xl hover:border-emerald-500/40 transition-all">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wider text-[var(--color-cinema-text-muted)] font-semibold">Tỷ lệ thanh toán</p>
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-semibold">Kỳ lọc</span>
                </div>
                <div class="mt-3 space-y-2 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-emerald-400 font-medium">Chuyển khoản:</span>
                        <span class="font-bold text-white text-sm" id="stat-ratio-transfer">—</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-amber-400 font-medium">Tiền mặt:</span>
                        <span class="font-bold text-white text-sm" id="stat-ratio-cash">—</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Visualizations (Charts & Payment Breakdown) --}}
        <div class="glass rounded-2xl p-6 sm:p-8 border border-[var(--color-cinema-border)] shadow-2xl">
            {{-- Summary Breakdown Boxes --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="p-5 rounded-2xl bg-gradient-to-br from-indigo-950/40 to-slate-900/60 border border-indigo-500/30">
                    <p class="text-xs text-indigo-300 font-semibold uppercase tracking-wider">Tổng doanh thu kỳ lọc</p>
                    <p class="text-2xl font-bold text-white mt-2" id="box-filtered-total">—</p>
                    <p class="text-xs text-[var(--color-cinema-text-muted)] mt-1" id="box-filtered-tickets">— đơn vé</p>
                </div>

                <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-950/40 to-slate-900/60 border border-emerald-500/30">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-emerald-300 font-semibold uppercase tracking-wider">Chuyển khoản</p>
                        <span class="text-xs bg-emerald-500/20 text-emerald-400 px-2.5 py-0.5 rounded-full font-bold">Online</span>
                    </div>
                    <p class="text-2xl font-bold text-emerald-400 mt-2" id="box-filtered-transfer">—</p>
                    <p class="text-xs text-[var(--color-cinema-text-muted)] mt-1" id="box-filtered-transfer-tickets">— đơn chuyển khoản</p>
                </div>

                <div class="p-5 rounded-2xl bg-gradient-to-br from-amber-950/40 to-slate-900/60 border border-amber-500/30">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-amber-300 font-semibold uppercase tracking-wider">Tiền mặt</p>
                        <span class="text-xs bg-amber-500/20 text-amber-400 px-2.5 py-0.5 rounded-full font-bold">Offline</span>
                    </div>
                    <p class="text-2xl font-bold text-amber-400 mt-2" id="box-filtered-cash">—</p>
                    <p class="text-xs text-[var(--color-cinema-text-muted)] mt-1" id="box-filtered-cash-tickets">— đơn tiền mặt</p>
                </div>
            </div>

            {{-- Charts Display --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
                {{-- Revenue Bar / Line Chart (2 Cols) --}}
                <div class="lg:col-span-2 p-5 rounded-2xl bg-[var(--color-cinema-surface)] border border-[var(--color-cinema-border)]">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-bold text-white flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            <span>Biến động doanh thu (Chuyển khoản vs Tiền mặt)</span>
                        </h4>
                        <span class="text-xs px-2.5 py-1 rounded-lg bg-[var(--color-cinema-card)] text-[var(--color-cinema-text-muted)] border border-[var(--color-cinema-border)]" id="chart-sub-label">Theo ngày</span>
                    </div>
                    <div class="relative h-[320px] w-full">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                {{-- Payment Method Pie / Doughnut Chart (1 Col) --}}
                <div class="p-5 rounded-2xl bg-[var(--color-cinema-surface)] border border-[var(--color-cinema-border)] flex flex-col">
                    <h4 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-purple-400"></span>
                        <span>Cơ cấu hình thức thanh toán</span>
                    </h4>
                    <div class="relative h-[270px] w-full flex-1 flex items-center justify-center">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentGroupBy = 'day';
        let revenueChartInstance = null;
        let paymentChartInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Mặc định chọn theo ngày
            selectQuickFilter('day');
        });

        // Hàm chọn nhanh đồng bộ hai chiều với thanh nút và dropdown/input
        function selectQuickFilter(type) {
            const dropdown = document.getElementById('filter-period-type');
            dropdown.value = type;

            updateQuickButtonsUI(type);
            updateFilterInputsUI(type);

            // Tự động gán group_by tối ưu cho từng chu kỳ
            if (type === 'day') currentGroupBy = 'day';
            else if (type === 'week') currentGroupBy = 'day';
            else if (type === 'month') currentGroupBy = 'day'; // Xem từng ngày trong tháng
            else if (type === 'year') currentGroupBy = 'month'; // Xem từng tháng trong năm
            else currentGroupBy = 'month';

            const labelMap = { day: 'Theo ngày', week: 'Theo ngày trong tuần', month: 'Theo ngày trong tháng', year: 'Theo tháng trong năm', all: 'Toàn thời gian' };
            document.getElementById('chart-sub-label').textContent = labelMap[type] || 'Theo chu kỳ';

            loadStatistics();
        }

        // Khi người dùng thay đổi dropdown
        function onPeriodTypeDropdownChange() {
            const val = document.getElementById('filter-period-type').value;
            updateQuickButtonsUI(val);
            updateFilterInputsUI(val);
            loadStatistics();
        }

        function updateQuickButtonsUI(activeType) {
            document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                btn.classList.remove('bg-[var(--color-cinema-primary)]', 'text-white', 'shadow-sm');
                btn.classList.add('text-[var(--color-cinema-text-muted)]');
            });

            const activeBtn = document.getElementById(`btn-quick-${activeType}`);
            if (activeBtn) {
                activeBtn.classList.remove('text-[var(--color-cinema-text-muted)]');
                activeBtn.classList.add('bg-[var(--color-cinema-primary)]', 'text-white', 'shadow-sm');
            }
        }

        function updateFilterInputsUI(type) {
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

            if (type === 'day') {
                inputDay.classList.remove('hidden');
            } else if (type === 'week') {
                // Tuần: Hiển thị khoảng 7 ngày gần nhất
                inputFrom.classList.remove('hidden');
                inputTo.classList.remove('hidden');
                const today = new Date();
                const past7 = new Date(today);
                past7.setDate(today.getDate() - 6);
                document.getElementById('filter-to-date').value = today.toISOString().split('T')[0];
                document.getElementById('filter-from-date').value = past7.toISOString().split('T')[0];
            } else if (type === 'month') {
                inputMonth.classList.remove('hidden');
            } else if (type === 'year') {
                inputYear.classList.remove('hidden');
            } else if (type === 'custom') {
                inputFrom.classList.remove('hidden');
                inputTo.classList.remove('hidden');
            }
        }

        function loadStatistics() {
            const periodType = document.getElementById('filter-period-type').value;
            const params = new URLSearchParams({
                group_by: currentGroupBy,
                period_type: periodType,
            });

            const periodTag = document.getElementById('card-period-tag');
            const revenueTitle = document.getElementById('card-revenue-title');

            if (periodType === 'day') {
                const dateVal = document.getElementById('filter-date').value;
                params.append('date', dateVal);
                periodTag.textContent = dateVal ? `Ngày ${dateVal}` : 'Ngày';
                revenueTitle.textContent = `Doanh thu ngày ${dateVal}`;
            } else if (periodType === 'week') {
                const from = document.getElementById('filter-from-date').value;
                const to = document.getElementById('filter-to-date').value;
                params.append('from_date', from);
                params.append('to_date', to);
                params.set('period_type', 'custom');
                periodTag.textContent = `Tuần: ${from} đến ${to}`;
                revenueTitle.textContent = 'Doanh thu trong tuần';
            } else if (periodType === 'month') {
                const monthVal = document.getElementById('filter-month').value;
                params.append('month', monthVal);
                periodTag.textContent = monthVal ? `Tháng ${monthVal}` : 'Tháng';
                revenueTitle.textContent = `Doanh thu tháng ${monthVal}`;
            } else if (periodType === 'year') {
                const yearVal = document.getElementById('filter-year').value;
                params.append('year', yearVal);
                periodTag.textContent = `Năm ${yearVal}`;
                revenueTitle.textContent = `Doanh thu năm ${yearVal}`;
            } else if (periodType === 'custom') {
                const from = document.getElementById('filter-from-date').value;
                const to = document.getElementById('filter-to-date').value;
                params.append('from_date', from);
                params.append('to_date', to);
                periodTag.textContent = `${from} đến ${to}`;
                revenueTitle.textContent = 'Doanh thu khoảng ngày';
            } else {
                periodTag.textContent = 'Toàn thời gian';
                revenueTitle.textContent = 'Tổng doanh thu toàn rạp';
            }

            fetch(`/api/admin/statistics?${params.toString()}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                const d = result.data;
                const f = d.filtered || {};

                // Cập nhật card doanh thu và số vé bán phản ánh CHÍNH XÁC theo bộ lọc
                const totalFiltered = Number(f.total_revenue || 0);
                const transferFiltered = Number(f.transfer_revenue || 0);
                const cashFiltered = Number(f.cash_revenue || 0);
                const ticketsFiltered = Number(f.total_tickets || 0);

                document.getElementById('stat-total').textContent = totalFiltered.toLocaleString('vi-VN') + 'đ';
                document.getElementById('stat-all-tickets').textContent = ticketsFiltered;
                document.getElementById('stat-movies').textContent = d.movies_showing;
                document.getElementById('stat-upcoming-shows').textContent = d.upcoming_shows_today;

                // Các box chi tiết
                document.getElementById('box-filtered-total').textContent = totalFiltered.toLocaleString('vi-VN') + 'đ';
                document.getElementById('box-filtered-tickets').textContent = `${ticketsFiltered} vé bán thành công`;

                document.getElementById('box-filtered-transfer').textContent = transferFiltered.toLocaleString('vi-VN') + 'đ';
                document.getElementById('box-filtered-transfer-tickets').textContent = `${f.transfer_tickets || 0} giao dịch online`;

                document.getElementById('box-filtered-cash').textContent = cashFiltered.toLocaleString('vi-VN') + 'đ';
                document.getElementById('box-filtered-cash-tickets').textContent = `${f.cash_tickets || 0} giao dịch tiền mặt`;

                // Top right ratio card
                const transferPct = totalFiltered > 0 ? Math.round((transferFiltered / totalFiltered) * 100) : 0;
                const cashPct = totalFiltered > 0 ? Math.round((cashFiltered / totalFiltered) * 100) : 0;
                document.getElementById('stat-ratio-transfer').textContent = `${transferFiltered.toLocaleString('vi-VN')}đ (${transferPct}%)`;
                document.getElementById('stat-ratio-cash').textContent = `${cashFiltered.toLocaleString('vi-VN')}đ (${cashPct}%)`;

                // Render Chart.js
                renderCharts(d.chart || [], transferFiltered, cashFiltered);
            })
            .catch(err => console.error('Error fetching statistics:', err));
        }

        function renderCharts(chartData, transferTotal, cashTotal) {
            const labels = chartData.map(c => c.label);
            const transferData = chartData.map(c => c.transfer_revenue);
            const cashData = chartData.map(c => c.cash_revenue);

            // 1. Revenue Stacked Bar Chart
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            if (revenueChartInstance) {
                revenueChartInstance.destroy();
            }

            revenueChartInstance = new Chart(ctxRevenue, {
                type: 'bar',
                data: {
                    labels: labels.length > 0 ? labels : ['Không có dữ liệu'],
                    datasets: [
                        {
                            label: 'Chuyển khoản',
                            data: transferData.length > 0 ? transferData : [0],
                            backgroundColor: 'rgba(16, 185, 129, 0.85)',
                            borderColor: '#10b981',
                            borderWidth: 1,
                            borderRadius: 6,
                        },
                        {
                            label: 'Tiền mặt',
                            data: cashData.length > 0 ? cashData : [0],
                            backgroundColor: 'rgba(245, 158, 11, 0.85)',
                            borderColor: '#f59e0b',
                            borderWidth: 1,
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: { color: '#94a3b8', font: { size: 11 } }
                        },
                        y: {
                            stacked: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)' },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 11 },
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                                    return value;
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: { color: '#e5e5e5', font: { size: 12 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${Number(context.raw).toLocaleString('vi-VN')}đ`;
                                }
                            }
                        }
                    }
                }
            });

            // 2. Payment Method Doughnut Chart
            const ctxPayment = document.getElementById('paymentMethodChart').getContext('2d');
            if (paymentChartInstance) {
                paymentChartInstance.destroy();
            }

            const hasData = (transferTotal + cashTotal) > 0;

            paymentChartInstance = new Chart(ctxPayment, {
                type: 'doughnut',
                data: {
                    labels: ['Chuyển khoản', 'Tiền mặt'],
                    datasets: [{
                        data: hasData ? [transferTotal, cashTotal] : [1, 0],
                        backgroundColor: hasData
                            ? ['rgba(16, 185, 129, 0.85)', 'rgba(245, 158, 11, 0.85)']
                            : ['rgba(255, 255, 255, 0.1)', 'rgba(255, 255, 255, 0.05)'],
                        borderColor: '#1a1a2e',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#e5e5e5', font: { size: 11 }, boxWidth: 12 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (!hasData) return 'Chưa có giao dịch';
                                    const val = Number(context.raw);
                                    const total = transferTotal + cashTotal;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${val.toLocaleString('vi-VN')}đ (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-layouts.admin>
