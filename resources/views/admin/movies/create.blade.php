<x-layouts.admin title="Thêm phim mới" header="Thêm phim mới">
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
                        <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Thời lượng *</label>
                        <input type="number" id="duration" required min="1" placeholder="Số phút" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Thể loại</label>
                        <input type="text" id="genre" placeholder="VD: Hành động, Tâm lý" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Giới hạn độ tuổi</label>
                        <select id="age_rating" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                            <option value="">Không giới hạn</option>
                            <option value="P">P - Phổ biến</option>
                            <option value="K">K - Dưới 13 tuổi</option>
                            <option value="T13">T13 - Từ 13 tuổi</option>
                            <option value="T16">T16 - Từ 16 tuổi</option>
                            <option value="T18">T18 - Từ 18 tuổi</option>
                            <option value="C">C - Không phổ biến</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Ngày khởi chiếu</label>
                    <input type="date" id="release_date" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                </div>

                {{-- Poster --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Poster - Tải file lên</label>
                    <input type="file" id="poster_file" accept="image/*" onchange="previewPoster(this)" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                    <div id="poster-preview" class="mt-3 hidden">
                        <img id="poster-preview-img" src="" alt="Preview" class="w-32 h-48 object-cover rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Hoặc nhập URL Poster</label>
                    <input type="url" id="poster_url" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                </div>

                {{-- Teaser --}}
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Teaser - Tải file MP4</label>
                    <input type="file" id="teaser_file" accept="video/mp4" onchange="previewTeaser(this)" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                    <div id="teaser-file-preview" class="mt-3 hidden">
                        <video id="teaser-preview-video" src="" class="w-full max-w-md rounded-lg" controls></video>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Hoặc nhập URL Teaser</label>
                    <input type="url" id="teaser_url" placeholder="https://www.youtube.com/embed/..." class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Mô tả</label>
                    <textarea id="description" rows="3" class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"></textarea>
                </div>
                <button type="submit" id="submit-btn" class="btn-primary w-full py-3">Thêm phim</button>
            </form>
        </div>
    </div>

    <script>
        function previewPoster(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('poster-preview-img').src = e.target.result;
                    document.getElementById('poster-preview').classList.remove('hidden');
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

        function submitMovie(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.textContent = 'Đang xử lý...';

            let formData = new FormData();
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

            fetch('/api/admin/movies', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
                body: formData,
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok) { alert('Thêm phim thành công!'); window.location.href = '/admin/movies'; }
                else {
                    alert(JSON.stringify(data.errors || data.message));
                    btn.disabled = false;
                    btn.textContent = 'Thêm phim';
                }
            })
            .catch(() => {
                alert('Có lỗi xảy ra');
                btn.disabled = false;
                btn.textContent = 'Thêm phim';
            });
        }
    </script>
</x-layouts.admin>
