<x-layouts.main title="Lịch chiếu phim - CineStar">
    <div class="w-[95%] max-w-[1600px] mx-auto px-4 py-8">
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-6">📅 Lịch chiếu phim</h1>

        {{-- Date Tabs --}}
        <div class="flex gap-2 mb-8 overflow-x-auto pb-2" id="date-tabs"></div>

        {{-- Schedule List --}}
        <div id="schedule-list">
            <div class="text-center py-12 text-[var(--color-cinema-text-muted)]">Đang tải lịch chiếu...</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateTabs = document.getElementById('date-tabs');
            const dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
            const days = [];

            for (let i = 0; i < 7; i++) {
                const d = new Date();
                d.setDate(d.getDate() + i);
                days.push({
                    date: d.toISOString().split('T')[0],
                    label: i === 0 ? 'Hôm nay' : (i === 1 ? 'Ngày mai' : dayNames[d.getDay()] + ' ' + d.getDate() + '/' + (d.getMonth() + 1)),
                });
            }

            dateTabs.innerHTML = days.map((day, i) => `
                <button onclick="loadSchedule('${day.date}')" class="schedule-tab whitespace-nowrap px-5 py-2.5 rounded-lg text-sm font-medium transition-all ${i === 0 ? 'btn-primary' : ''}"
                        style="${i !== 0 ? 'background: var(--color-cinema-card); color: var(--color-cinema-text-muted); border: 1px solid var(--color-cinema-border);' : ''}"
                        data-date="${day.date}">
                    ${day.label}
                </button>
            `).join('');

            loadSchedule(days[0].date);
        });

        function loadSchedule(date) {
            document.querySelectorAll('.schedule-tab').forEach(tab => {
                if (tab.dataset.date === date) {
                    tab.className = 'schedule-tab whitespace-nowrap px-5 py-2.5 rounded-lg text-sm font-medium transition-all btn-primary';
                    tab.removeAttribute('style');
                } else {
                    tab.className = 'schedule-tab whitespace-nowrap px-5 py-2.5 rounded-lg text-sm font-medium transition-all';
                    tab.style = 'background: var(--color-cinema-card); color: var(--color-cinema-text-muted); border: 1px solid var(--color-cinema-border);';
                }
            });

            fetch(`/api/shows?date=${date}`)
                .then(res => res.json())
                .then(result => {
                    const shows = result.data || [];
                    const container = document.getElementById('schedule-list');

                    if (shows.length === 0) {
                        container.innerHTML = '<div class="glass rounded-2xl p-12 text-center text-[var(--color-cinema-text-muted)]">Không có suất chiếu trong ngày này.</div>';
                        return;
                    }

                    // Group by movie
                    const grouped = {};
                    shows.forEach(show => {
                        const movieId = show.movie?.id;
                        if (!grouped[movieId]) {
                            grouped[movieId] = { movie: show.movie, shows: [] };
                        }
                        grouped[movieId].shows.push(show);
                    });

                    container.innerHTML = Object.values(grouped).map(group => `
                        <div class="glass rounded-xl p-5 mb-4 animate-fade-in-up">
                            <div class="flex gap-4">
                                <img src="${group.movie?.poster_url || '/images/placeholder.png'}"
                                     alt="${group.movie?.title}" class="w-20 h-28 object-cover rounded-lg flex-shrink-0"
                                     onerror="this.onerror=null; this.src='/images/placeholder.png';">
                                <div class="flex-1">
                                    <a href="/movies/${group.movie?.id}" class="font-bold text-white hover:text-[var(--color-cinema-primary)] transition-colors">${group.movie?.title || 'Phim'}</a>
                                    <p class="text-xs text-[var(--color-cinema-text-muted)] mt-1">${group.movie?.duration_minutes || '?'} phút • ${group.movie?.genre || ''}</p>
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        ${group.shows.map(show => {
                                            const t = new Date(show.start_time);
                                            const isPast = t < new Date();
                                            return `<a href="${isPast ? '#' : '/booking/seats/' + show.id}"
                                                       class="px-3 py-2 rounded-lg text-sm font-medium text-center transition-all ${isPast ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105 hover:border-[var(--color-cinema-primary)]'}"
                                                       style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"
                                                       ${isPast ? 'onclick="event.preventDefault()"' : ''}>
                                                        <span class="text-white">${t.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'})}</span>
                                                        <span class="text-xs text-[var(--color-cinema-text-muted)] block">${show.room?.name || ''}</span>
                                                    </a>`;
                                        }).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                });
        }
    </script>
</x-layouts.app>
