<x-layouts.admin title="Thêm phim mới" header="🎥 Thêm phim mới">
    <div class="max-w-2xl">
        <a href="/admin/movies" class="text-sm text-[var(--color-cinema-text-muted)] hover:text-white mb-4 inline-block">← Quay lại</a>

        <div class="glass rounded-xl p-6">
            <form onsubmit="submitMovie(event)" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Tên phim *</label>
                        <input type="text" id="title" required class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Đạo diễn</label>
                        <input type="text" id="director" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Thời lượng (phút) *</label>
                        <input type="number" id="duration" required min="1" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Trạng thái *</label>
                        <select id="status" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                            <option value="showing">Đang chiếu</option>
                            <option value="coming_soon">Sắp chiếu</option>
                            <option value="stopped">Ngừng chiếu</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Thể loại</label>
                    <input type="text" id="genre" placeholder="VD: Hành động, Tâm lý" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">URL Poster</label>
                    <input type="url" id="poster_url" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">URL Teaser (YouTube embed)</label>
                    <input type="url" id="teaser_url" placeholder="https://www.youtube.com/embed/..." class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Mô tả</label>
                    <textarea id="description" rows="3" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full py-3">Thêm phim</button>
            </form>
        </div>
    </div>

    <script>
        function submitMovie(e) {
            e.preventDefault();
            fetch('/api/admin/movies', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({
                    title: document.getElementById('title').value,
                    director: document.getElementById('director').value,
                    duration_minutes: parseInt(document.getElementById('duration').value),
                    status: document.getElementById('status').value,
                    genre: document.getElementById('genre').value,
                    poster_url: document.getElementById('poster_url').value,
                    teaser_url: document.getElementById('teaser_url').value,
                    description: document.getElementById('description').value,
                }),
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok) { alert('Thêm phim thành công!'); window.location.href = '/admin/movies'; }
                else { alert(JSON.stringify(data.errors || data.message)); }
            });
        }
    </script>
</x-layouts.admin>
