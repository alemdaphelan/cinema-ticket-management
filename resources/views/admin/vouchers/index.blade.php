<x-layouts.admin title="Quản lý Voucher" header="🎟️ Quản lý Voucher">
    <div class="glass rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[var(--color-cinema-text-muted)] border-b border-[var(--color-cinema-border)]" style="background: var(--color-cinema-card);">
                        <th class="text-left py-3 px-4">Mã Voucher</th>
                        <th class="text-left py-3 px-4">Giá trị giảm</th>
                        <th class="text-left py-3 px-4">Đơn tối thiểu</th>
                        <th class="text-left py-3 px-4">Hạn dùng</th>
                        <th class="text-center py-3 px-4">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="vouchers-table">
                    <tr><td colspan="5" class="text-center py-8 text-[var(--color-cinema-text-muted)]">Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/admin/vouchers', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } })
            .then(res => res.json())
            .then(result => {
                const vouchers = result.data || [];
                const tbody = document.getElementById('vouchers-table');
                tbody.innerHTML = vouchers.map(v => {
                    const toDate = new Date(v.valid_to);
                    const isExpired = toDate < new Date();
                    return `
                    <tr class="border-b border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)] transition-colors">
                        <td class="py-3 px-4 font-bold text-white">${v.code}</td>
                        <td class="py-3 px-4 text-[var(--color-cinema-accent)] font-medium">
                            ${v.discount_type === 'fixed' ? Number(v.discount_value).toLocaleString('vi-VN') + 'đ' : v.discount_value + '%'}
                        </td>
                        <td class="py-3 px-4">${Number(v.min_order_value).toLocaleString('vi-VN')}đ</td>
                        <td class="py-3 px-4 ${isExpired ? 'text-red-400' : ''}">${toDate.toLocaleDateString('vi-VN')}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="text-xs px-2 py-1 rounded-full ${(v.is_active && !isExpired) ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'}">
                                ${(v.is_active && !isExpired) ? 'Đang kích hoạt' : 'Hết hạn/Khóa'}
                            </span>
                        </td>
                    </tr>
                `}).join('');
            });
        });
    </script>
</x-layouts.admin>
