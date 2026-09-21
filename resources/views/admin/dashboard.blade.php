<x-layouts.admin title="Dashboard - Admin" header="Dashboard">
    <div class="space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="stats-grid">
            <div class="glass rounded-xl p-5 animate-fade-in-up">
                <p class="text-sm text-[var(--color-cinema-text-muted)]">Doanh thu hôm nay</p>
                <p class="text-2xl font-bold text-[var(--color-cinema-accent)] mt-2" id="stat-revenue">—</p>
            </div>
            <div class="glass rounded-xl p-5 animate-fade-in-up" style="animation-delay: 0.1s;">
                <p class="text-sm text-[var(--color-cinema-text-muted)]">Vé bán hôm nay</p>
                <p class="text-2xl font-bold text-green-400 mt-2" id="stat-tickets">—</p>
            </div>
            <div class="glass rounded-xl p-5 animate-fade-in-up" style="animation-delay: 0.2s;">
                <p class="text-sm text-[var(--color-cinema-text-muted)]">Phim đang chiếu</p>
                <p class="text-2xl font-bold text-blue-400 mt-2" id="stat-movies">—</p>
            </div>
            <div class="glass rounded-xl p-5 animate-fade-in-up" style="animation-delay: 0.3s;">
                <p class="text-sm text-[var(--color-cinema-text-muted)]">Tổng doanh thu</p>
                <p class="text-2xl font-bold text-purple-400 mt-2" id="stat-total">—</p>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="glass rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-white">Đơn hàng gần đây</h3>
                <a href="/admin/orders" class="text-sm text-[var(--color-cinema-primary)]">Xem tất cả →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[var(--color-cinema-text-muted)] border-b border-[var(--color-cinema-border)]">
                            <th class="text-left py-3 px-2">ID</th>
                            <th class="text-left py-3 px-2">Khách hàng</th>
                            <th class="text-left py-3 px-2">Phim</th>
                            <th class="text-right py-3 px-2">Tổng tiền</th>
                            <th class="text-center py-3 px-2">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="recent-orders">
                        <tr><td colspan="5" class="text-center py-8 text-[var(--color-cinema-text-muted)]">Đang tải...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load stats
            fetch('/api/admin/statistics', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                const d = result.data;
                document.getElementById('stat-revenue').textContent = Number(d.today_revenue).toLocaleString('vi-VN') + 'đ';
                document.getElementById('stat-tickets').textContent = d.today_tickets;
                document.getElementById('stat-movies').textContent = d.movies_showing;
                document.getElementById('stat-total').textContent = Number(d.total_revenue).toLocaleString('vi-VN') + 'đ';
            });

            // Load recent orders
            fetch('/api/admin/orders', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                const orders = (result.data || []).slice(0, 10);
                const tbody = document.getElementById('recent-orders');
                const statusColors = { pending: 'bg-yellow-500/20 text-yellow-400', paid: 'bg-green-500/20 text-green-400', completed: 'bg-gray-500/20 text-gray-400', cancelled: 'bg-red-500/20 text-red-400' };

                tbody.innerHTML = orders.length > 0 ? orders.map(o => `
                    <tr class="border-b border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)] transition-colors">
                        <td class="py-3 px-2">#${o.id}</td>
                        <td class="py-3 px-2">${o.user?.name || 'Khách'}</td>
                        <td class="py-3 px-2">${o.show?.movie?.title || '—'}</td>
                        <td class="py-3 px-2 text-right font-medium text-[var(--color-cinema-accent)]">${Number(o.total_amount).toLocaleString('vi-VN')}đ</td>
                        <td class="py-3 px-2 text-center"><span class="text-xs px-2 py-1 rounded-full ${statusColors[o.status] || ''}">${o.status}</span></td>
                    </tr>
                `).join('') : '<tr><td colspan="5" class="text-center py-8 text-[var(--color-cinema-text-muted)]">Chưa có đơn hàng nào.</td></tr>';
            });
        });
    </script>
</x-layouts.admin>
