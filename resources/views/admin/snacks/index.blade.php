<x-layouts.admin title="Quản lý Bắp nước" header="Quản lý Bắp nước">
    <div class="glass rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[var(--color-cinema-text-muted)] border-b border-[var(--color-cinema-border)]" style="background: var(--color-cinema-card);">
                        <th class="text-left py-3 px-4">Tên / Combo</th>
                        <th class="text-right py-3 px-4">Giá bán</th>
                        <th class="text-center py-3 px-4">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="snacks-table">
                    <tr><td colspan="3" class="text-center py-8 text-[var(--color-cinema-text-muted)]">Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/admin/snacks', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } })
            .then(res => res.json())
            .then(result => {
                const snacks = result.data || [];
                const tbody = document.getElementById('snacks-table');
                tbody.innerHTML = snacks.map(s => `
                    <tr class="border-b border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)] transition-colors">
                        <td class="py-3 px-4 font-medium text-white">${s.name}</td>
                        <td class="py-3 px-4 text-right font-medium text-[var(--color-cinema-accent)]">${Number(s.price).toLocaleString('vi-VN')}đ</td>
                        <td class="py-3 px-4 text-center">
                            <span class="text-xs px-2 py-1 rounded-full ${s.is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'}">
                                ${s.is_active ? 'Đang bán' : 'Ngừng bán'}
                            </span>
                        </td>
                    </tr>
                `).join('');
            });
        });
    </script>
</x-layouts.admin>
