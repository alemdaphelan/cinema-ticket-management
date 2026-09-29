<x-layouts.admin title="Sửa phim" header="Sửa thông tin phim">
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
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Thể loại</label><input type="text" id="genre" value="${m.genre||''}" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></div>
                            <div>
                                <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Giới hạn độ tuổi</label>
                                <select id="age_rating" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                                    <option value="" ${!m.age_rating?'selected':''}>Không giới hạn</option>
                                    <option value="P" ${m.age_rating==='P'?'selected':''}>P - Phổ biến</option>
                                    <option value="K" ${m.age_rating==='K'?'selected':''}>K - Dưới 13 tuổi</option>
                                    <option value="T13" ${m.age_rating==='T13'?'selected':''}>T13 - Từ 13 tuổi</option>
                                    <option value="T16" ${m.age_rating==='T16'?'selected':''}>T16 - Từ 16 tuổi</option>
                                    <option value="T18" ${m.age_rating==='T18'?'selected':''}>T18 - Từ 18 tuổi</option>
                                    <option value="C" ${m.age_rating==='C'?'selected':''}>C - Không phổ biến</option>
                                </select>
                            </div>
                        </div>
                        <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Ngày khởi chiếu</label><input type="date" id="release_date" value="${m.release_date ? m.release_date.split('T')[0] : ''}" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></div>

                        <div>
                            <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Poster - Tải file lên</label>
                            <input type="file" id="poster_file" accept="image/*" onchange="previewPoster(this)" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                            ${m.poster_url ? `<div class="mt-3"><img src="${m.poster_url}" alt="Poster hiện tại" class="w-32 h-48 object-cover rounded-lg" id="poster-current" onerror="this.style.display='none'"></div>` : ''}
                            <div id="poster-preview" class="mt-3 hidden"><img id="poster-preview-img" src="" alt="Preview" class="w-32 h-48 object-cover rounded-lg"></div>
                        </div>
                        <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Hoặc nhập URL Poster</label><input type="url" id="poster_url" value="${m.poster_url||''}" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></div>

                        <div>
                            <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Teaser - Tải file MP4</label>
                            <input type="file" id="teaser_file" accept="video/mp4" onchange="previewTeaser(this)" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                            ${m.teaser_url && (m.teaser_url.endsWith('.mp4') || m.teaser_url.includes('/storage/teasers/')) ? `<div class="mt-3"><video src="${m.teaser_url}" class="w-full max-w-md rounded-lg" controls></video></div>` : ''}
                            <div id="teaser-file-preview" class="mt-3 hidden"><video id="teaser-preview-video" src="" class="w-full max-w-md rounded-lg" controls></video></div>
                        </div>
                        <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Hoặc nhập URL Teaser</label><input type="url" id="teaser_url" value="${m.teaser_url||''}" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></div>

                        <div><label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Mô tả</label><textarea id="description" rows="3" class="w-full px-4 py-3 rounded-lg text-white text-sm" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">${m.description||''}</textarea></div>
                        <button type="submit" id="submit-btn" class="btn-primary w-full py-3">Cập nhật phim</button>
                    </form>`;
            });
        });

        function previewPoster(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('poster-preview-img').src = e.target.result;
                    document.getElementById('poster-preview').classList.remove('hidden');
                    const currentImg = document.getElementById('poster-current');
                    if (currentImg) currentImg.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewTeaser(input) {
            if (input.files && input.files[0]) {
                const url = URL.createObjectURL(input.files[0]);
                document.getElementById('teaser-preview-video').src = url;
                document.getElementById('teaser-file-preview').classList.remove('hidden');
            }
        }

        function updateMovie(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.textContent = 'Đang xử lý...';

            let formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('title', document.getElementById('title').value);
            formData.append('director', document.getElementById('director').value);
            formData.append('duration_minutes', document.getElementById('duration').value);
            formData.append('status', document.getElementById('status').value);
            formData.append('genre', document.getElementById('genre').value);
            formData.append('age_rating', document.getElementById('age_rating').value);
            formData.append('release_date', document.getElementById('release_date').value);
            formData.append('poster_url', document.getElementById('poster_url').value);
            formData.append('description', document.getElementById('description').value);

            // Teaser: ưu tiên file, nếu không có thì dùng URL
            let teaserFile = document.getElementById('teaser_file').files[0];
            if (teaserFile) {
                formData.append('teaser_file', teaserFile);
            } else {
                formData.append('teaser_url', document.getElementById('teaser_url').value);
            }

            let posterFile = document.getElementById('poster_file').files[0];
            if (posterFile) {
                formData.append('poster', posterFile);
            }

            fetch(`/api/admin/movies/${movieId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
                body: formData,
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok) { alert('Cập nhật thành công!'); window.location.href = '/admin/movies'; }
                else {
                    alert(JSON.stringify(data.errors || data.message));
                    btn.disabled = false;
                    btn.textContent = 'Cập nhật phim';
                }
            })
            .catch(() => {
                alert('Có lỗi xảy ra');
                btn.disabled = false;
                btn.textContent = 'Cập nhật phim';
            });
        }
    </script>
</x-layouts.admin>
