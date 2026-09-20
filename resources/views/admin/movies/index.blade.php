<x-layouts.admin title="Quản lý Phim" header="🎥 Quản lý Phim">
    <div class="flex justify-between items-center mb-6">
        <div></div>
        <div class="flex gap-3">
            <button onclick="fetchTmdb()" class="btn-primary !bg-purple-600 hover:!bg-purple-700 !py-2 !px-4 text-sm font-bold">🚀 Kéo dữ liệu từ TMDB</button>
            <a href="/admin/movies/create" class="btn-primary !py-2 !px-4 text-sm">+ Thêm phim mới</a>
        </div>
    </div>

    <div class="glass rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[var(--color-cinema-text-muted)] border-b border-[var(--color-cinema-border)]" style="background: var(--color-cinema-card);">
                        <th class="text-left py-3 px-4">Phim</th>
                        <th class="text-left py-3 px-4">Đạo diễn</th>
                        <th class="text-center py-3 px-4">Thời lượng</th>
                        <th class="text-center py-3 px-4">Trạng thái</th>
                        <th class="text-center py-3 px-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="movies-table">
                    <tr><td colspan="5" class="text-center py-8 text-[var(--color-cinema-text-muted)]">Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadMovies();
        });

        function loadMovies() {
            fetch('/api/admin/movies', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN } })
            .then(res => res.json())
            .then(result => {
                const movies = result.data || [];
                const tbody = document.getElementById('movies-table');
                const statusColors = { showing: 'bg-green-500/20 text-green-400', coming_soon: 'bg-yellow-500/20 text-yellow-400', stopped: 'bg-red-500/20 text-red-400' };
                const statusLabels = { showing: 'Đang chiếu', coming_soon: 'Sắp chiếu', stopped: 'Ngừng chiếu' };

                tbody.innerHTML = movies.map(m => `
                    <tr class="border-b border-[var(--color-cinema-border)] hover:bg-[var(--color-cinema-card)] transition-colors">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <img src="${m.poster_url || '/images/placeholder.png'}" class="w-10 h-14 object-cover rounded" onerror="this.onerror=null; this.src='/images/placeholder.png';">
                                <span class="font-medium text-white">${m.title}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-[var(--color-cinema-text-muted)]">${m.director || '—'}</td>
                        <td class="py-3 px-4 text-center">${m.duration_minutes} phút</td>
                        <td class="py-3 px-4 text-center"><span class="text-xs px-2 py-1 rounded-full ${statusColors[m.status] || ''}">${statusLabels[m.status] || m.status}</span></td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="/admin/movies/${m.id}/edit" class="text-blue-400 hover:text-blue-300 text-xs font-medium">Sửa</a>
                                <button onclick="deleteMovie(${m.id})" class="text-red-400 hover:text-red-300 text-xs font-medium">Xóa</button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            });
        }

        function deleteMovie(id) {
            if (!confirm('Bạn có chắc muốn xóa phim này?')) return;
            fetch(`/api/admin/movies/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' } })
            .then(() => loadMovies());
        }

        function fetchTmdb() {
            if (!confirm('Bạn có chắc muốn kéo dữ liệu phim từ TMDB về không? Quá trình này có thể mất vài giây.')) return;
            
            // Show loading state on button
            const btn = document.querySelector('button[onclick="fetchTmdb()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '⏳ Đang kéo dữ liệu...';
            btn.disabled = true;

            fetch('/api/movies/fetch-tmdb', { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' } 
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    alert('Thành công! Đã kéo phim từ TMDB về hệ thống.');
                    loadMovies();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể kéo dữ liệu'));
                }
            })
            .catch(err => alert('Lỗi kết nối Server!'))
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
</x-layouts.admin>
