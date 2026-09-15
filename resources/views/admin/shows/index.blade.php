<x-layouts.admin title="Quản lý Suất chiếu" header="📅 Quản lý Suất chiếu">
    <div class="flex justify-between items-center mb-6">
        <div></div>
        <a href="/admin/shows/create" class="btn-primary !py-2 !px-4 text-sm">+ Thêm suất chiếu</a>
    </div>

    <div class="glass rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[var(--color-cinema-text-muted)] border-b border-[var(--color-cinema-border)]" style="background: var(--color-cinema-card);">
                        <th class="text-left py-3 px-4">ID</th>
                        <th class="text-left py-3 px-4">Phim</th>
                        <th class="text-left py-3 px-4">Phòng</th>
                        <th class="text-left py-3 px-4">Thời gian chiếu</th>
                        <th class="text-right py-3 px-4">Giá vé cơ bản</th>
                        <th class="text-center py-3 px-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="shows-table">
                    <tr><td colspan="6" class="text-center py-8 text-[var(--color-cinema-text-muted)]">Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadShows();
        });

        function loadShows() {
            fetch('/api/admin/shows', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } })
            .then(res => res.json())
            .then(result => {
                const shows = result.data || [];
                const tbody = document.getElementById('shows-table');

                tbody.innerHTML = shows.map(s => {
                    const st = new Date(s.start_time);
                    const et = new Date(s.end_time);
                    return `
                    <tr class="border-b border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)] transition-colors">
                        <td class="py-3 px-4">#${s.id}</td>
                        <td class="py-3 px-4 font-medium text-white">${s.movie?.title || '—'}</td>
                        <td class="py-3 px-4">${s.room?.name || '—'}</td>
                        <td class="py-3 px-4">
                            <span class="block text-white">${st.toLocaleDateString('vi-VN')}</span>
                            <span class="text-xs text-[var(--color-cinema-text-muted)]">${st.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'})} - ${et.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'})}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-medium text-[var(--color-cinema-accent)]">${Number(s.price).toLocaleString('vi-VN')}đ</td>
                        <td class="py-3 px-4 text-center">
                            <button onclick="deleteShow(${s.id})" class="text-red-400 hover:text-red-300 text-xs font-medium">Xóa</button>
                        </td>
                    </tr>
                `}).join('');
            });
        }

        function deleteShow(id) {
            if (!confirm('Bạn có chắc muốn xóa suất chiếu này?')) return;
            fetch(`/api/admin/shows/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' } })
            .then(() => loadShows());
        }
    </script>
</x-layouts.admin>
