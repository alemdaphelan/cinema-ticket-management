<x-layouts.main title="CineStar - Đặt vé xem phim online">
    {{-- Hero Section --}}
    <section class="relative overflow-hidden" style="min-height: 500px;" id="hero-carousel">
        <div id="hero-bg" class="absolute inset-0 transition-opacity duration-1000 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1440404653325-ab127d49abc1?q=80&w=1600'); opacity: 0.4;"></div>
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(229,9,20,0.3) 0%, rgba(15,15,35,0.9) 60%, rgba(245,197,24,0.1) 100%);"></div>
        <div class="absolute inset-0" style="background: radial-gradient(circle at 20% 50%, rgba(229,9,20,0.15) 0%, transparent 60%);"></div>

        <div class="relative w-[95%] max-w-[1600px] mx-auto px-4 py-20 md:py-32">
            <div class="text-center animate-fade-in-up">
                <h1 class="text-4xl md:text-6xl font-extrabold mb-6">
                    <span class="gradient-text">Trải nghiệm điện ảnh</span>
                    <br>
                    <span class="text-white">tuyệt vời nhất</span>
                </h1>
                <p class="text-lg md:text-xl text-[var(--color-cinema-text-muted)] mb-8 max-w-2xl mx-auto">
                    Đặt vé nhanh chóng, chọn ghế yêu thích, thưởng thức bộ phim bom tấn cùng gia đình và bạn bè.
                </p>
                <div class="flex justify-center gap-4">
                    <a href="#now-showing" class="btn-primary text-lg px-8 py-3">
                        Phim đang chiếu
                    </a>
                    <a href="/schedule" class="btn-accent text-lg px-8 py-3">
                        Lịch chiếu
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Genres Section --}}
    <section id="genres" class="w-[95%] max-w-[1600px] mx-auto px-4 py-16 border-b border-[var(--color-cinema-border)]">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-white">Khám phá thể loại</h2>
                <p class="text-[var(--color-cinema-text-muted)] text-sm mt-1">Tìm bộ phim hoàn hảo theo sở thích của bạn</p>
            </div>
            <button onclick="filterByGenre('')" class="text-sm font-medium text-[var(--color-cinema-primary)] hover:text-[var(--color-cinema-primary-hover)] transition-colors">
                Tất cả phim →
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
            @php
                $genres = [
                    ['name' => 'Hành Động', 'image' => 'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Hài', 'image' => 'https://images.unsplash.com/photo-1543536448-d209d2d13a1c?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Kinh Dị', 'image' => 'https://images.unsplash.com/photo-1505635552518-3448ff116af3?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Hoạt Hình', 'image' => 'https://images.unsplash.com/photo-1524253482453-3fed8d2fe12b?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Tình Cảm', 'image' => 'https://images.unsplash.com/photo-1474552226712-ac0f0961a954?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Viễn Tưởng', 'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Phiêu Lưu', 'image' => 'https://images.unsplash.com/photo-1536697246787-1f276329e4fa?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Tâm Lý', 'image' => 'https://images.unsplash.com/photo-1518066000714-58c45f1a2c0a?q=80&w=800&auto=format&fit=crop'],
                ];
            @endphp

            @foreach($genres as $genre)
                <div onclick="filterByGenre('{{ $genre['name'] }}')" class="relative h-32 md:h-40 rounded-2xl overflow-hidden cursor-pointer group shadow-lg border border-[var(--color-cinema-border)] hover:border-[var(--color-cinema-primary)] transition-all duration-300 transform hover:-translate-y-1">
                    <img src="{{ $genre['image'] }}" alt="{{ $genre['name'] }}" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700 ease-in-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
                    <div class="absolute inset-0 p-4 flex flex-col justify-end items-center text-center">
                        <h3 class="text-white font-bold text-lg md:text-xl tracking-wide group-hover:text-[var(--color-cinema-primary)] transition-colors">{{ $genre['name'] }}</h3>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Now Showing --}}
    <section id="now-showing" class="w-[95%] max-w-[1600px] mx-auto px-4 py-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-white">
                    Phim đang chiếu
                </h2>
                <p class="text-[var(--color-cinema-text-muted)] text-sm mt-1">Những bộ phim hot nhất tại rạp</p>
            </div>
            <a href="/schedule" class="text-sm font-medium text-[var(--color-cinema-primary)] hover:text-[var(--color-cinema-primary-hover)] transition-colors">
                Xem tất cả →
            </a>
        </div>

        @if(request('search'))
        <div class="mb-6 flex items-center justify-between bg-[var(--color-cinema-card)] p-4 rounded-xl border border-[var(--color-cinema-border)]">
            <p class="text-white">Kết quả tìm kiếm cho: <span class="font-bold text-[var(--color-cinema-primary)]">"{{ request('search') }}"</span></p>
            <a href="/" class="text-sm text-[var(--color-cinema-text-muted)] hover:text-white">Xóa tìm kiếm ✕</a>
        </div>
        @endif

        <div id="active-genre-indicator" class="mb-6 hidden items-center justify-between bg-[var(--color-cinema-card)] p-4 rounded-xl border border-[var(--color-cinema-border)] transition-all duration-300">
            <p class="text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-[var(--color-cinema-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Đang lọc theo thể loại: <span id="active-genre-text" class="font-bold text-[var(--color-cinema-primary)]"></span>
            </p>
            <button onclick="filterByGenre('')" class="text-sm font-medium text-red-400 hover:text-red-300 transition-colors bg-red-400/10 px-3 py-1.5 rounded-lg">Xóa bộ lọc ✕</button>
        </div>

        <div id="movies-showing" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
            {{-- Loaded via JavaScript --}}
            <div class="col-span-full text-center py-12 text-[var(--color-cinema-text-muted)]">
                <div class="inline-block w-8 h-8 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin"></div>
                <p class="mt-3">Đang tải phim...</p>
            </div>
        </div>
        <div id="pagination-showing" class="flex justify-center items-center gap-2 mt-8"></div>
    </section>

    {{-- Hot Movies Section --}}
    <section id="hot-movies" class="w-[95%] max-w-[1600px] mx-auto px-4 py-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-white">Phim Nổi Bật</h2>
                <p class="text-[var(--color-cinema-text-muted)] text-sm mt-1">Những tác phẩm được yêu thích nhất</p>
            </div>
            <a href="/?genre=Hành Động" class="text-sm text-[var(--color-cinema-primary)] hover:text-white transition-colors">Xem tất cả →</a>
        </div>
        <div id="movies-hot" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
            <div class="col-span-full text-center py-12 text-[var(--color-cinema-text-muted)]">
                <div class="inline-block w-8 h-8 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin"></div>
                <p class="mt-3">Đang tải...</p>
            </div>
        </div>
    </section>

    {{-- Coming Soon --}}
    <section class="w-[95%] max-w-[1600px] mx-auto px-4 py-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-white">
                    Phim sắp chiếu
                </h2>
                <p class="text-[var(--color-cinema-text-muted)] text-sm mt-1">Đón chờ những bom tấn sắp ra mắt</p>
            </div>
        </div>

        <div id="movies-coming" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
            <div class="col-span-full text-center py-12 text-[var(--color-cinema-text-muted)]">
                <div class="inline-block w-8 h-8 border-2 border-[var(--color-cinema-accent)] border-t-transparent rounded-full animate-spin"></div>
                <p class="mt-3">Đang tải...</p>
            </div>
        </div>
    </section>

    {{-- Voucher Section --}}
    <section class="w-[95%] max-w-[1600px] mx-auto px-4 py-16">
        <div class="glass rounded-2xl p-8 md:p-12 text-center" style="background: linear-gradient(135deg, rgba(229,9,20,0.1), rgba(245,197,24,0.1));">
            <h2 class="text-2xl md:text-3xl font-bold mb-4">Ưu đãi đặc biệt</h2>
            <p class="text-[var(--color-cinema-text-muted)] mb-6">Sử dụng mã giảm giá khi đặt vé để nhận ưu đãi hấp dẫn!</p>
            <div class="flex flex-wrap justify-center gap-4">
                <div class="glass px-6 py-3 rounded-xl">
                    <span class="text-[var(--color-cinema-accent)] font-bold text-lg">WELCOME50</span>
                    <span class="text-sm text-[var(--color-cinema-text-muted)] block">Giảm 50K đơn từ 200K</span>
                </div>
                <div class="glass px-6 py-3 rounded-xl">
                    <span class="text-[var(--color-cinema-accent)] font-bold text-lg">GIAM10PT</span>
                    <span class="text-sm text-[var(--color-cinema-text-muted)] block">Giảm 10% đơn từ 150K</span>
                </div>
                <div class="glass px-6 py-3 rounded-xl">
                    <span class="text-[var(--color-cinema-accent)] font-bold text-lg">SINHVIEN30K</span>
                    <span class="text-sm text-[var(--color-cinema-text-muted)] block">Giảm 30K đơn từ 100K</span>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Hero Carousel Logic
        const heroImages = [
            'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?q=80&w=1600',
            'https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=1600',
            'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?q=80&w=1600',
            'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=1600'
        ];
        let currentHeroIdx = 0;
        const heroBg = document.getElementById('hero-bg');
        
        setInterval(() => {
            currentHeroIdx = (currentHeroIdx + 1) % heroImages.length;
            heroBg.style.opacity = 0; // fade out
            setTimeout(() => {
                heroBg.style.backgroundImage = `url('${heroImages[currentHeroIdx]}')`;
                heroBg.style.opacity = 0.4; // fade in
            }, 1000);
        }, 5000);

        let currentSearch = '{{ request('search', '') }}';
        let currentGenre = '';

        document.addEventListener('DOMContentLoaded', function() {
            loadMovies('showing', 'movies-showing', 1);
            loadMovies('coming_soon', 'movies-coming', 1);
            loadMovies('showing', 'movies-hot', 1, 'Hành Động'); // Load some specific genre for "hot" movies
        });

        function filterByGenre(genre) {
            currentGenre = genre;
            
            const indicator = document.getElementById('active-genre-indicator');
            const genreText = document.getElementById('active-genre-text');
            
            if (genre) {
                indicator.classList.remove('hidden');
                indicator.classList.add('flex');
                genreText.textContent = `"${genre}"`;
            } else {
                indicator.classList.add('hidden');
                indicator.classList.remove('flex');
            }

            document.getElementById('now-showing').scrollIntoView({ behavior: 'smooth' });
            loadMovies('showing', 'movies-showing', 1);
        }

        function loadMovies(status, containerId, page = 1, forceGenre = null) {
            let url = `/api/movies?status=${status}&page=${page}`;
            if (forceGenre) {
                url += `&genre=${encodeURIComponent(forceGenre)}`;
            } else if (status === 'showing') {
                if (currentSearch) url += `&search=${encodeURIComponent(currentSearch)}`;
                if (currentGenre) url += `&genre=${encodeURIComponent(currentGenre)}`;
            }

            fetch(url)
                .then(res => res.json())
                .then(result => {
                    const container = document.getElementById(containerId);
                    const movies = result.data || [];

                    if (movies.length === 0) {
                        container.innerHTML = '<div class="col-span-full text-center py-12 text-[var(--color-cinema-text-muted)]">Chưa có phim nào.</div>';
                        if(status === 'showing') document.getElementById('pagination-showing').innerHTML = '';
                        return;
                    }

                    container.innerHTML = movies.map((movie, index) => `
                        <a href="/movies/${movie.id}" class="movie-card rounded-xl overflow-hidden group" style="animation-delay: ${index * 0.1}s; background: var(--color-cinema-surface);">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="${movie.poster_url || '/images/placeholder.png'}"
                                     alt="${movie.title}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                     onerror="this.onerror=null; this.src='/images/placeholder.png';">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="absolute bottom-3 left-3 right-3">
                                        <span class="btn-primary text-xs !py-1.5 !px-3 w-full text-center block">
                                            ${status === 'showing' ? 'Đặt vé' : 'Sắp chiếu'}
                                        </span>
                                    </div>
                                </div>
                                ${movie.duration_minutes ? `<span class="absolute top-2 right-2 text-xs px-2 py-1 rounded-full glass font-medium">${movie.duration_minutes} phút</span>` : ''}
                                ${movie.age_rating ? `<span class="absolute top-2 left-2 text-xs px-2 py-1 rounded-full bg-red-600 text-white font-bold">${movie.age_rating}</span>` : ''}
                            </div>
                            <div class="p-3">
                                <h3 class="font-semibold text-sm text-white line-clamp-2 group-hover:text-[var(--color-cinema-primary)] transition-colors">${movie.title}</h3>
                                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-1">${movie.genre || movie.director || ''}</p>
                            </div>
                        </a>
                    `).join('');

                    if (status === 'showing' && result.last_page) {
                        renderPagination(result.current_page, result.last_page);
                    }
                })
                .catch(err => {
                    console.error('Error loading movies:', err);
                    document.getElementById(containerId).innerHTML = '<div class="col-span-full text-center py-12 text-red-400">Lỗi tải dữ liệu phim.</div>';
                });
        }

        function renderPagination(currentPage, lastPage) {
            const pagination = document.getElementById('pagination-showing');
            if (lastPage <= 1) {
                pagination.innerHTML = '';
                return;
            }

            let html = '';
            
            // Prev button
            if (currentPage > 1) {
                html += `<button onclick="loadMovies('showing', 'movies-showing', ${currentPage - 1})" class="px-3 py-1 rounded bg-[var(--color-cinema-surface)] text-white hover:bg-[var(--color-cinema-primary)]">&laquo;</button>`;
            }

            // Pages
            for (let i = 1; i <= lastPage; i++) {
                if (i === currentPage) {
                    html += `<button class="px-3 py-1 rounded bg-[var(--color-cinema-primary)] text-white font-bold">${i}</button>`;
                } else {
                    html += `<button onclick="loadMovies('showing', 'movies-showing', ${i})" class="px-3 py-1 rounded bg-[var(--color-cinema-surface)] text-white hover:bg-[var(--color-cinema-primary)]">${i}</button>`;
                }
            }

            // Next button
            if (currentPage < lastPage) {
                html += `<button onclick="loadMovies('showing', 'movies-showing', ${currentPage + 1})" class="px-3 py-1 rounded bg-[var(--color-cinema-surface)] text-white hover:bg-[var(--color-cinema-primary)]">&raquo;</button>`;
            }

            pagination.innerHTML = html;
        }
    </script>
</x-layouts.main>
