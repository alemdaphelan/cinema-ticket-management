<x-layouts.admin title="Sửa phim" header="🎥 Sửa thông tin phim">
    <div class="max-w-2xl">
        <a href="/admin/movies" class="text-sm text-[var(--color-cinema-text-muted)] hover:text-white mb-4 inline-block">← Quay lại</a>
        <div class="glass rounded-xl p-6" id="edit-form">
            <div class="text-center py-8 text-[var(--color-cinema-text-muted)]">Đang tải...</div>
        </div>
    </div>
    <script>
        const movieId = {{ $movieId }};
        document.addEventListener('DOMContentLoaded', function() {
            fetch(`/api/admin/movies`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }})
            .then(res => res.json())
            .then(result => {
                const m = (result.data || []).find(x => x.id === movieId);
                if (!m) { document.getElementById('edit-form').innerHTML = '<p class="text-red-400">Không tìm thấy phim.</p>'; return; }
                document.getElementById('edit-form').innerHTML = `
                    <form onsubmit="updateMovie(event)" class="space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Tên phim</label><input type="text" id="title" value="${m.title}" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></div>
                            <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Đạo diễn</label><input type="text" id="director" value="${m.director||''}" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Thời lượng</label><input type="number" id="duration" value="${m.duration_minutes}" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></div>
                            <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Trạng thái</label><select id="status" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"><option value="showing" ${m.status==='showing'?'selected':''}>Đang chiếu</option><option value="coming_soon" ${m.status==='coming_soon'?'selected':''}>Sắp chiếu</option><option value="stopped" ${m.status==='stopped'?'selected':''}>Ngừng chiếu</option></select></div>
                        </div>
                        <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Thể loại</label><input type="text" id="genre" value="${m.genre||''}" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></div>
                        <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Poster URL</label><input type="url" id="poster_url" value="${m.poster_url||''}" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></div>
                        <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Teaser URL</label><input type="url" id="teaser_url" value="${m.teaser_url||''}" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></div>
                        <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Mô tả</label><textarea id="description" rows="3" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">${m.description||''}</textarea></div>
                        <button type="submit" class="btn-primary w-full py-3">Cập nhật phim</button>
                    </form>`;
            });
        });
        function updateMovie(e) {
            e.preventDefault();
            fetch(`/api/admin/movies/${movieId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ title: document.getElementById('title').value, director: document.getElementById('director').value, duration_minutes: parseInt(document.getElementById('duration').value), status: document.getElementById('status').value, genre: document.getElementById('genre').value, poster_url: document.getElementById('poster_url').value, teaser_url: document.getElementById('teaser_url').value, description: document.getElementById('description').value }),
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => { if (ok) { alert('Cập nhật thành công!'); window.location.href = '/admin/movies'; } else { alert(JSON.stringify(data.errors || data.message)); } });
        }
    </script>
</x-layouts.admin>
