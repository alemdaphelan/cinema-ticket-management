<x-layouts.app title="Chi tiết phim - CineStar">
    <div id="movie-detail" class="max-w-7xl mx-auto px-4 py-8">
        <div class="text-center py-20 text-[var(--color-cinema-text-muted)]">
            <div class="inline-block w-8 h-8 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin"></div>
            <p class="mt-3">Đang tải thông tin phim...</p>
        </div>
    </div>

    <script>
        const movieId = {{ $movieId }};

        document.addEventListener('DOMContentLoaded', function() {
            fetch(`/api/movies/${movieId}`)
                .then(res => res.json())
                .then(result => {
                    const movie = result.data;
                    renderMovieDetail(movie);
                    loadShows(movieId);
                })
                .catch(err => {
                    document.getElementById('movie-detail').innerHTML = '<p class="text-center text-red-400 py-20">Không tìm thấy phim.</p>';
                });
        });

        function renderMovieDetail(movie) {
            const container = document.getElementById('movie-detail');
            container.innerHTML = `
                <div class="animate-fade-in-up">
                    {{-- Back button --}}
                    <a href="/" class="inline-flex items-center gap-2 text-sm text-[var(--color-cinema-text-muted)] hover:text-white mb-6 transition-colors">
                        ← Quay lại trang chủ
                    </a>

                    {{-- Movie Info --}}
                    <div class="glass rounded-2xl overflow-hidden">
                        <div class="md:flex">
                            {{-- Poster --}}
                            <div class="md:w-1/3 lg:w-1/4">
                                <img src="${movie.poster_url || '/images/placeholder.png'}"
                                     alt="${movie.title}"
                                     class="w-full h-full object-cover"
                                     style="min-height: 400px;"
                                     onerror="this.onerror=null; this.src='/images/placeholder.png';">
                            </div>

                            {{-- Info --}}
                            <div class="md:w-2/3 lg:w-3/4 p-6 md:p-8">
                                <div class="flex items-start justify-between flex-wrap gap-4">
                                    <div>
                                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">${movie.title}</h1>
                                        <div class="flex flex-wrap gap-3 text-sm text-[var(--color-cinema-text-muted)]">
                                            ${movie.director ? `<span>🎬 ${movie.director}</span>` : ''}
                                            ${movie.duration_minutes ? `<span>⏱️ ${movie.duration_minutes} phút</span>` : ''}
                                            ${movie.genre ? `<span>🎭 ${movie.genre}</span>` : ''}
                                        </div>
                                    </div>
                                    <span class="text-xs px-3 py-1.5 rounded-full font-semibold ${movie.status === 'showing' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400'}">
                                        ${movie.status === 'showing' ? '🟢 Đang chiếu' : '⏳ Sắp chiếu'}
                                    </span>
                                </div>

                                ${movie.description ? `
                                    <div class="mt-6">
                                        <h3 class="text-sm font-semibold text-[var(--color-cinema-accent)] mb-2">Nội dung phim</h3>
                                        <p class="text-sm text-[var(--color-cinema-text-muted)] leading-relaxed">${movie.description}</p>
                                    </div>
                                ` : ''}

                                ${movie.teaser_url ? `
                                    <div class="mt-6">
                                        <h3 class="text-sm font-semibold text-[var(--color-cinema-accent)] mb-3">🎥 Teaser / Trailer</h3>
                                        <div class="aspect-video rounded-xl overflow-hidden" style="background: var(--color-cinema-card);">
                                            <iframe src="${movie.teaser_url}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>

                    {{-- Shows Section --}}
                    ${movie.status === 'showing' ? `
                        <div class="mt-8">
                            <h2 class="text-xl font-bold text-white mb-4">📅 Lịch chiếu</h2>
                            <div class="flex gap-2 mb-6 overflow-x-auto pb-2" id="date-tabs"></div>
                            <div id="shows-list" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                <p class="col-span-full text-center text-[var(--color-cinema-text-muted)] py-8">Chọn ngày để xem lịch chiếu</p>
                            </div>
                        </div>
                    ` : `
                        <div class="mt-8 glass rounded-xl p-8 text-center">
                            <p class="text-[var(--color-cinema-text-muted)]">⏳ Phim sắp chiếu — Lịch chiếu sẽ được cập nhật sớm!</p>
                        </div>
                    `}
                </div>
            `;
        }

        function loadShows(movieId) {
            // Generate date tabs for 7 days
            const dateTabs = document.getElementById('date-tabs');
            if (!dateTabs) return;

            const days = [];
            const dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
            for (let i = 0; i < 7; i++) {
                const d = new Date();
                d.setDate(d.getDate() + i);
                days.push({
                    date: d.toISOString().split('T')[0],
                    label: i === 0 ? 'Hôm nay' : (i === 1 ? 'Ngày mai' : dayNames[d.getDay()] + ' ' + d.getDate() + '/' + (d.getMonth() + 1)),
                });
            }

            dateTabs.innerHTML = days.map((day, i) => `
                <button onclick="fetchShows('${day.date}')" class="date-tab whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition-all ${i === 0 ? 'btn-primary' : ''}"
                        style="${i !== 0 ? 'background: var(--color-cinema-card); color: var(--color-cinema-text-muted); border: 1px solid var(--color-cinema-border);' : ''}"
                        data-date="${day.date}">
                    ${day.label}
                </button>
            `).join('');

            // Load today's shows
            fetchShows(days[0].date);
        }

        function fetchShows(date) {
            // Update tab styles
            document.querySelectorAll('.date-tab').forEach(tab => {
                if (tab.dataset.date === date) {
                    tab.className = 'date-tab whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition-all btn-primary';
                    tab.removeAttribute('style');
                } else {
                    tab.className = 'date-tab whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition-all';
                    tab.style = 'background: var(--color-cinema-card); color: var(--color-cinema-text-muted); border: 1px solid var(--color-cinema-border);';
                }
            });

            const showsList = document.getElementById('shows-list');
            showsList.innerHTML = '<p class="col-span-full text-center py-8"><span class="inline-block w-5 h-5 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin"></span></p>';

            fetch(`/api/shows?movie_id=${movieId}&date=${date}`)
                .then(res => res.json())
                .then(result => {
                    const shows = result.data || [];

                    if (shows.length === 0) {
                        showsList.innerHTML = '<p class="col-span-full text-center text-[var(--color-cinema-text-muted)] py-8">Không có suất chiếu trong ngày này.</p>';
                        return;
                    }

                    showsList.innerHTML = shows.map(show => {
                        const startTime = new Date(show.start_time);
                        const timeStr = startTime.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                        const isPast = startTime < new Date();

                        return `
                            <a href="${isPast ? '#' : '/booking/seats/' + show.id}"
                               class="glass rounded-xl p-4 text-center transition-all ${isPast ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105 hover:border-[var(--color-cinema-primary)]'}"
                               ${isPast ? 'onclick="event.preventDefault()"' : ''}>
                                <div class="text-2xl font-bold text-white mb-1">${timeStr}</div>
                                <div class="text-xs text-[var(--color-cinema-text-muted)]">${show.room?.name || 'Phòng chiếu'}</div>
                                <div class="text-sm font-semibold mt-2" style="color: var(--color-cinema-accent);">${Number(show.price).toLocaleString('vi-VN')}đ</div>
                                ${isPast ? '<span class="text-xs text-red-400 mt-1 block">Đã chiếu</span>' : ''}
                            </a>
                        `;
                    }).join('');
                });
        }
    </script>
</x-layouts.app>
