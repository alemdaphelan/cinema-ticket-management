<x-layouts.admin title="Quản lý Đơn hàng" header="📋 Quản lý Đơn hàng">
    <div class="glass rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[var(--color-cinema-text-muted)] border-b border-[var(--color-cinema-border)]" style="background: var(--color-cinema-card);">
                        <th class="text-left py-3 px-4">Mã ĐH</th>
                        <th class="text-left py-3 px-4">Khách hàng</th>
                        <th class="text-left py-3 px-4">Phim & Suất chiếu</th>
                        <th class="text-right py-3 px-4">Tổng tiền</th>
                        <th class="text-center py-3 px-4">Trạng thái</th>
                        <th class="text-left py-3 px-4">Ngày đặt</th>
                    </tr>
                </thead>
                <tbody id="orders-table">
                    <tr><td colspan="6" class="text-center py-8 text-[var(--color-cinema-text-muted)]">Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/admin/orders', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } })
            .then(res => res.json())
            .then(result => {
                const orders = result.data || [];
                const tbody = document.getElementById('orders-table');
                const statusColors = { pending: 'bg-yellow-500/20 text-yellow-400', paid: 'bg-green-500/20 text-green-400', completed: 'bg-gray-500/20 text-gray-400', cancelled: 'bg-red-500/20 text-red-400' };

                tbody.innerHTML = orders.map(o => {
                    const created = new Date(o.created_at);
                    const showTime = new Date(o.show?.start_time);
                    return `
                    <tr class="border-b border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)] transition-colors">
                        <td class="py-3 px-4 font-medium text-white">#${o.id}</td>
                        <td class="py-3 px-4">${o.user?.name || '—'}<br><span class="text-xs text-[var(--color-cinema-text-muted)]">${o.user?.email || ''}</span></td>
                        <td class="py-3 px-4">
                            <span class="font-medium">${o.show?.movie?.title || '—'}</span><br>
                            <span class="text-xs text-[var(--color-cinema-text-muted)]">${showTime.toLocaleString('vi-VN')}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-medium text-[var(--color-cinema-accent)]">${Number(o.total_amount).toLocaleString('vi-VN')}đ</td>
                        <td class="py-3 px-4 text-center">
                            <span class="text-xs px-2 py-1 rounded-full ${statusColors[o.status] || ''}">${o.status}</span>
                        </td>
                        <td class="py-3 px-4 text-[var(--color-cinema-text-muted)]">${created.toLocaleString('vi-VN')}</td>
                    </tr>
                `}).join('');
            });
        });
    </script>
</x-layouts.admin>
