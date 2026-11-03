<x-layouts.admin title="Thêm Suất Chiếu Mới" header="📅 Thêm Suất Chiếu Mới">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <a href="/admin/shows" class="text-sm text-[var(--color-cinema-text-muted)] hover:text-white transition-colors flex items-center gap-1.5 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Quay lại danh sách suất chiếu</span>
            </a>
            <span class="text-xs text-[var(--color-cinema-text-muted)]">
                Quy định: <span class="text-amber-400 font-semibold">Giãn cách tối thiểu 30 phút dọn rạp</span>
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Column (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="glass rounded-2xl p-6 sm:p-8 border border-[var(--color-cinema-border)] shadow-xl relative overflow-hidden">
                    {{-- Ambient top bar --}}
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[var(--color-cinema-primary)] via-amber-500 to-[var(--color-cinema-primary)] opacity-40"></div>

                    <form onsubmit="submitShow(event)" id="create-show-form" class="space-y-6">
                        {{-- Chọn Phim --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-white">
                                🎬 Chọn phim chiếu <span class="text-red-500">*</span>
                            </label>
                            <select id="movie_id" required onchange="onMovieChange()"
                                    class="w-full px-4 py-3 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)] transition-all cursor-pointer"
                                    style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                                <option value="">-- Chọn phim --</option>
                                @foreach($movies as $movie)
                                    <option value="{{ $movie->id }}"
                                            data-duration="{{ $movie->duration_minutes ?? 120 }}"
                                            data-poster="{{ $movie->poster_url }}"
                                            data-genre="{{ $movie->genre ?? 'Chưa phân loại' }}"
                                            data-rating="{{ $movie->age_rating ?? 'P' }}">
                                        {{ $movie->title }} ({{ $movie->duration_minutes ?? 120 }} phút)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Chọn Phòng Chiếu & Ngày Chiếu --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-white">
                                    🚪 Phòng chiếu <span class="text-red-500">*</span>
                                </label>
                                <select id="room_id" required onchange="onRoomOrDateChange()"
                                        class="w-full px-4 py-3 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)] transition-all cursor-pointer"
                                        style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2 text-white">
                                    📆 Ngày chiếu <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="show_date" required
                                       value="{{ now()->format('Y-m-d') }}"
                                       min="{{ now()->format('Y-m-d') }}"
                                       onchange="onRoomOrDateChange()"
                                       class="w-full px-4 py-3 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)] transition-all cursor-pointer"
                                       style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                            </div>
                        </div>

                        {{-- Giờ Bắt Đầu --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-white">
                                    ⏰ Giờ bắt đầu chiếu <span class="text-red-500">*</span>
                                </label>
                                <span class="text-xs text-[var(--color-cinema-text-muted)]" id="quick-time-hint">
                                    Định dạng 24h (VD: 14:30)
                                </span>
                            </div>
                            <input type="time" id="show_time" required oninput="onTimeChange()"
                                   class="w-full px-4 py-3 rounded-xl text-white text-base font-mono focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)] transition-all"
                                   style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                            
                            {{-- Realtime Calculation & Conflict Status Banner --}}
                            <div id="time-preview-box" class="mt-3 hidden p-4 rounded-xl border text-sm transition-all duration-300">
                                <!-- Populated dynamically by JS -->
                            </div>
                        </div>

                        {{-- Giá Vé Cơ Bản --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-white">
                                💵 Giá vé cơ bản (VNĐ) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="price" required min="10000" step="1000" value="85000"
                                   class="w-full px-4 py-3 rounded-xl text-white text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)] transition-all"
                                   style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                            
                            {{-- Quick price presets --}}
                            <div class="flex flex-wrap gap-2 mt-2.5">
                                <span class="text-xs text-[var(--color-cinema-text-muted)] self-center mr-1">Giá gợi ý:</span>
                                @foreach([75000, 85000, 95000, 100000, 120000] as $presetPrice)
                                    <button type="button" onclick="setPrice({{ $presetPrice }})"
                                            class="text-xs px-2.5 py-1 rounded-lg border border-[var(--color-cinema-border)] hover:border-[var(--color-cinema-accent)] text-[var(--color-cinema-text-muted)] hover:text-white transition-all cursor-pointer">
                                        {{ number_format($presetPrice) }}đ
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2">
                            <button type="submit" id="submit-btn"
                                    class="btn-primary w-full py-3.5 rounded-xl font-bold text-base shadow-lg shadow-red-500/25 flex items-center justify-center gap-2 cursor-pointer transition-all hover:scale-[1.01]">
                                <span>Tạo suất chiếu</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sidebar Column (1/3): Existing Schedule & Movie Info --}}
            <div class="space-y-6">
                {{-- Movie Info Card (if selected) --}}
                <div id="movie-preview-card" class="glass rounded-2xl p-5 border border-[var(--color-cinema-border)] hidden">
                    <h4 class="text-xs font-bold text-[var(--color-cinema-accent)] uppercase tracking-wider mb-3">Thông tin phim</h4>
                    <div class="flex gap-3.5">
                        <img id="mp-poster" src="" alt="Poster" class="w-16 h-24 object-cover rounded-lg shrink-0 border border-white/10 hidden">
                        <div class="space-y-1 text-xs">
                            <h5 id="mp-title" class="font-bold text-white text-sm leading-snug"></h5>
                            <p class="text-[var(--color-cinema-text-muted)]">Thời lượng: <span id="mp-duration" class="text-amber-400 font-bold"></span></p>
                            <p class="text-[var(--color-cinema-text-muted)]">Thể loại: <span id="mp-genre" class="text-white"></span></p>
                            <p class="text-[var(--color-cinema-text-muted)]">Độ tuổi: <span id="mp-rating" class="px-1.5 py-0.5 rounded bg-red-500/20 text-red-400 font-bold"></span></p>
                        </div>
                    </div>
                </div>

                {{-- Existing Schedule in this Room on this Date --}}
                <div class="glass rounded-2xl p-5 border border-[var(--color-cinema-border)]">
                    <div class="flex items-center justify-between mb-3 border-b border-[var(--color-cinema-border)] pb-2.5">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                            <span>📋 Lịch chiếu trong ngày</span>
                        </h4>
                        <span id="room-date-badge" class="text-[11px] text-[var(--color-cinema-accent)] font-medium"></span>
                    </div>

                    <div id="existing-shows-container" class="space-y-2.5 max-h-[420px] overflow-y-auto pr-1">
                        <div class="text-center py-6 text-xs text-[var(--color-cinema-text-muted)]">
                            <div class="inline-block w-4 h-4 border-2 border-red-500 border-t-transparent rounded-full animate-spin mb-1"></div>
                            <p>Đang tải lịch phòng...</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-[var(--color-cinema-border)]/50 text-[11px] text-[var(--color-cinema-text-muted)] flex items-start gap-1.5">
                        <span class="text-amber-400 text-sm leading-none">ℹ</span>
                        <span>Sau mỗi suất chiếu, phòng cần <strong>30 phút dọn rạp</strong>. Suất tiếp theo chỉ được đặt sau khi dọn rạp xong.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let existingShows = [];
        let selectedMovieDuration = 120;

        document.addEventListener('DOMContentLoaded', function() {
            onMovieChange();
            onRoomOrDateChange();
        });

        function setPrice(val) {
            document.getElementById('price').value = val;
        }

        function onMovieChange() {
            const select = document.getElementById('movie_id');
            const card = document.getElementById('movie-preview-card');
            const option = select.options[select.selectedIndex];

            if (!option || !option.value) {
                card.classList.add('hidden');
                selectedMovieDuration = 120;
                validateTimeSlot();
                return;
            }

            selectedMovieDuration = parseInt(option.getAttribute('data-duration')) || 120;
            const poster = option.getAttribute('data-poster');
            const genre = option.getAttribute('data-genre');
            const rating = option.getAttribute('data-rating');

            document.getElementById('mp-title').textContent = option.text.split(' (')[0];
            document.getElementById('mp-duration').textContent = `${selectedMovieDuration} phút`;
            document.getElementById('mp-genre').textContent = genre;
            document.getElementById('mp-rating').textContent = rating;

            const posterImg = document.getElementById('mp-poster');
            if (poster) {
                posterImg.src = poster;
                posterImg.classList.remove('hidden');
            } else {
                posterImg.classList.add('hidden');
            }

            card.classList.remove('hidden');
            validateTimeSlot();
        }

        function onRoomOrDateChange() {
            const roomId = document.getElementById('room_id').value;
            const date = document.getElementById('show_date').value;
            const container = document.getElementById('existing-shows-container');
            const badge = document.getElementById('room-date-badge');

            if (!roomId || !date) return;

            const roomText = document.getElementById('room_id').options[document.getElementById('room_id').selectedIndex]?.text || '';
            const dObj = new Date(date);
            badge.textContent = `${roomText} • ${dObj.toLocaleDateString('vi-VN')}`;

            container.innerHTML = `
                <div class="text-center py-6 text-xs text-[var(--color-cinema-text-muted)]">
                    <div class="inline-block w-4 h-4 border-2 border-red-500 border-t-transparent rounded-full animate-spin mb-1"></div>
                    <p>Đang tải lịch phòng...</p>
                </div>
            `;

            fetch(`/api/admin/shows/room-schedule?room_id=${roomId}&date=${date}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                existingShows = result.data || [];
                renderExistingShows();
                validateTimeSlot();
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = `<p class="text-xs text-red-400 text-center py-4">Không thể tải lịch phòng</p>`;
            });
        }

        function renderExistingShows() {
            const container = document.getElementById('existing-shows-container');
            if (existingShows.length === 0) {
                container.innerHTML = `
                    <div class="p-4 rounded-xl text-center bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs">
                        <span class="block font-bold text-sm mb-1">✨ Phòng đang trống cả ngày</span>
                        <span>Chưa có suất chiếu nào. Bạn có thể chọn bất kỳ giờ nào!</span>
                    </div>
                `;
                return;
            }

            container.innerHTML = existingShows.map((s, idx) => `
                <div class="p-3 rounded-xl border text-xs space-y-1 transition-all"
                     style="background: var(--color-cinema-card); border-color: var(--color-cinema-border);">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-white truncate max-w-[170px]" title="${s.movie_title}">${s.movie_title}</span>
                        <span class="font-mono font-bold text-[var(--color-cinema-accent)]">${s.start_time} - ${s.end_time}</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-[var(--color-cinema-text-muted)]">
                        <span>Thời lượng: ${s.duration}p</span>
                        <span class="text-amber-400/90 font-medium">Nghỉ dọn rạp đến: <strong>${s.clean_end_time}</strong></span>
                    </div>
                </div>
            `).join('');
        }

        function onTimeChange() {
            validateTimeSlot();
        }

        function validateTimeSlot() {
            const timeVal = document.getElementById('show_time').value;
            const previewBox = document.getElementById('time-preview-box');

            if (!timeVal) {
                previewBox.classList.add('hidden');
                return;
            }

            // Calculate start and end times in minutes from midnight
            const [h, m] = timeVal.split(':').map(Number);
            const newStartMin = h * 60 + m;
            const newEndMin = newStartMin + selectedMovieDuration;
            const newCleanEndMin = newEndMin + 30; // 30 mins buffer

            const formatMin = (mins) => {
                const totalH = Math.floor(mins / 60) % 24;
                const totalM = mins % 60;
                return `${String(totalH).padStart(2, '0')}:${String(totalM).padStart(2, '0')}`;
            };

            const newStartStr = formatMin(newStartMin);
            const newEndStr = formatMin(newEndMin);
            const newCleanEndStr = formatMin(newCleanEndMin);

            // Check conflict with existing shows
            let conflictMsg = null;

            for (const s of existingShows) {
                const [sh, sm] = s.start_time.split(':').map(Number);
                const exStartMin = sh * 60 + sm;
                const exEndMin = exStartMin + s.duration;
                const exCleanEndMin = exEndMin + 30; // 30 mins buffer

                // Check overlap between [newStartMin, newCleanEndMin] and [exStartMin, exCleanEndMin]
                if (newStartMin < exCleanEndMin && newCleanEndMin > exStartMin) {
                    if (newStartMin >= exStartMin && newStartMin < exCleanEndMin) {
                        conflictMsg = `Chưa đủ 30 phút giãn cách sau suất '${s.movie_title}' (${s.start_time} - ${s.end_time}). Phòng chỉ sẵn sàng nhận suất mới từ <strong>${s.clean_end_time}</strong> trở đi!`;
                    } else if (newCleanEndMin > exStartMin && newStartMin < exStartMin) {
                        const neededFinish = formatMin(exStartMin - 30);
                        conflictMsg = `Suất mới kết thúc lúc ${newEndStr} và cần 30 phút dọn rạp (đến ${newCleanEndStr}), bị lấn vào suất '${s.movie_title}' bắt đầu lúc ${s.start_time}. Suất mới phải kết thúc trước <strong>${neededFinish}</strong>!`;
                    } else {
                        conflictMsg = `Trùng giờ với suất chiếu '${s.movie_title}' (${s.start_time} - ${s.end_time}). Vui lòng chọn khung giờ khác!`;
                    }
                    break;
                }
            }

            previewBox.classList.remove('hidden');

            if (conflictMsg) {
                previewBox.className = 'mt-3 p-3.5 rounded-xl border border-red-500/40 bg-red-500/10 text-xs text-red-300 leading-relaxed space-y-1.5';
                previewBox.innerHTML = `
                    <div class="flex items-center gap-1.5 font-bold text-red-400">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Không thể đặt vào giờ này</span>
                    </div>
                    <div>${conflictMsg}</div>
                    <div class="text-[11px] text-red-400/80 pt-1 border-t border-red-500/20">
                        Suất dự kiến: ${newStartStr} ~ ${newEndStr} (kết thúc dọn dẹp: ${newCleanEndStr})
                    </div>
                `;
            } else {
                previewBox.className = 'mt-3 p-3.5 rounded-xl border border-emerald-500/40 bg-emerald-500/10 text-xs text-emerald-300 leading-relaxed space-y-1';
                previewBox.innerHTML = `
                    <div class="flex items-center gap-1.5 font-bold text-emerald-400">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Khung giờ hợp lệ (Đủ 30 phút giãn cách)</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] pt-1 border-t border-emerald-500/20">
                        <span>Chiếu phim: <strong>${newStartStr} ~ ${newEndStr}</strong></span>
                        <span class="text-amber-400">Dọn rạp xong: <strong>${newCleanEndStr}</strong></span>
                    </div>
                `;
            }
        }

        function submitShow(e) {
            e.preventDefault();

            const movieId = document.getElementById('movie_id').value;
            const roomId = document.getElementById('room_id').value;
            const date = document.getElementById('show_date').value;
            const time = document.getElementById('show_time').value;
            const price = document.getElementById('price').value;
            const submitBtn = document.getElementById('submit-btn');

            if (!movieId || !roomId || !date || !time || !price) {
                alert('Vui lòng điền đầy đủ các trường bắt buộc!');
                return;
            }

            const startTime = `${date} ${time}:00`;

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <span>Đang kiểm tra & tạo suất...</span>
            `;

            fetch('/api/admin/shows', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    movie_id: parseInt(movieId),
                    room_id: parseInt(roomId),
                    start_time: startTime,
                    price: parseFloat(price),
                }),
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(data => { throw new Error(data.message || 'Lỗi tạo suất chiếu!'); });
                }
                return res.json();
            })
            .then(result => {
                if (window.showToast) {
                    window.showToast('Tạo suất chiếu thành công!', 'success');
                }
                setTimeout(() => {
                    window.location.href = '/admin/shows';
                }, 800);
            })
            .catch(err => {
                if (window.showNotificationModal) {
                    window.showNotificationModal({
                        title: 'Không thể tạo suất chiếu',
                        message: err.message,
                        type: 'error'
                    });
                } else {
                    alert(err.message);
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Tạo suất chiếu</span>';
            });
        }
    </script>
</x-layouts.admin>
