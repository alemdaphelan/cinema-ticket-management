<x-layouts.main title="Khám phá Phim theo Thể loại - CineStar">
    <div class="w-[95%] max-w-[1600px] mx-auto px-4 py-8">
        {{-- Hero Header --}}
        <div class="relative rounded-3xl overflow-hidden p-8 md:p-12 mb-10 border border-[var(--color-cinema-border)] shadow-2xl" 
             style="background: linear-gradient(135deg, rgba(20, 20, 30, 0.95) 0%, rgba(30, 15, 35, 0.9) 100%);">
            <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-[var(--color-cinema-primary)]/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-80 h-80 rounded-full bg-[var(--color-cinema-accent)]/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider mb-4 border border-[var(--color-cinema-primary)]/30 text-[var(--color-cinema-primary)] bg-[var(--color-cinema-primary)]/10">
                    Khám Phá Kho Phim Đặc Sắc
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-tight mb-4">
                    Vũ Trụ Điện Ảnh <span class="gradient-text">Theo Thể Loại</span>
                </h1>
                <p class="text-[var(--color-cinema-text-muted)] text-base md:text-lg leading-relaxed">
                    Khám phá hàng chục tác phẩm điện ảnh đỉnh cao kết nối trực tiếp từ cơ sở dữ liệu rạp CineStar. Thưởng thức trọn vẹn từng khoảnh khắc theo sở thích của bạn!
                </p>
            </div>
        </div>

        {{-- Genre Category Pills --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[var(--color-cinema-primary)] animate-pulse"></span>
                    Chọn thể loại yêu thích
                </h2>
                <span id="genre-result-count" class="text-xs md:text-sm text-[var(--color-cinema-text-muted)]">Đang tải dữ liệu...</span>
            </div>

            <div class="flex gap-2.5 overflow-x-auto pb-3 scrollbar-none" id="genre-pills">
                <!-- Dynamically Rendered & Active Pills -->
            </div>
        </div>

        {{-- Filter & Sort Toolbar --}}
        <div class="glass rounded-2xl p-4 md:p-5 mb-8 border border-[var(--color-cinema-border)] flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Status Tabs --}}
            <div class="flex items-center gap-2 bg-[var(--color-cinema-card)] p-1.5 rounded-xl border border-[var(--color-cinema-border)] self-start sm:self-auto">
                <button type="button" onclick="setStatusFilter('')" id="status-btn-all" 
                        class="status-tab px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-all btn-primary">
                    Tất cả
                </button>
                <button type="button" onclick="setStatusFilter('showing')" id="status-btn-showing" 
                        class="status-tab px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-all text-[var(--color-cinema-text-muted)] hover:text-white">
                    Đang chiếu
                </button>
                <button type="button" onclick="setStatusFilter('coming_soon')" id="status-btn-coming_soon" 
                        class="status-tab px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-all text-[var(--color-cinema-text-muted)] hover:text-white">
                    Sắp chiếu
                </button>
            </div>

            {{-- Search & Sort --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                {{-- Search inside genres --}}
                <div class="relative min-w-[240px]">
                    <input type="text" id="genre-search-input" placeholder="Tìm kiếm tên phim..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)] transition-all"
                           style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--color-cinema-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                {{-- Sort Select --}}
                <div class="relative">
                    <select id="genre-sort-select" onchange="setSortFilter(this.value)"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)] transition-all cursor-pointer"
                            style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                        <option value="latest">Mới cập nhật</option>
                        <option value="rating">Đánh giá cao nhất</option>
                        <option value="duration">Thời lượng dài nhất</option>
                        <option value="title">Tên phim (A - Z)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Active Filter Tags Indicator --}}
        <div id="filter-indicator-container" class="mb-6 hidden flex-wrap items-center gap-2">
            <span class="text-xs text-[var(--color-cinema-text-muted)]">Bộ lọc đang áp dụng:</span>
            <div id="filter-tags" class="flex flex-wrap items-center gap-2"></div>
            <button onclick="resetAllFilters()" class="text-xs text-red-400 hover:text-red-300 ml-2 font-medium hover:underline cursor-pointer">
                Xóa tất cả bộ lọc ✕
            </button>
        </div>

        {{-- Movies Grid --}}
        <div id="movies-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6 min-h-[400px]">
            {{-- Skeleton or Items will be rendered here --}}
        </div>

        {{-- Pagination --}}
        <div id="pagination-container" class="flex justify-center items-center gap-2 mt-12 mb-8"></div>
    </div>

    <script>
        const GENRES_LIST = [
            { name: '', label: 'Tất cả' },
            { name: 'Hành Động', label: 'Hành Động' },
            { name: 'Hoạt Hình', label: 'Hoạt Hình' },
            { name: 'Kinh Dị', label: 'Kinh Dị' },
            { name: 'Hài Hước', label: 'Hài Hước' },
            { name: 'Tình Cảm', label: 'Tình Cảm' },
            { name: 'Khoa Học Viễn Tưởng', label: 'Viễn Tưởng' },
            { name: 'Phiêu Lưu', label: 'Phiêu Lưu' },
            { name: 'Tâm Lý', label: 'Tâm Lý' },
            { name: 'Giật Gân', label: 'Giật Gân' },
            { name: 'Tội Phạm', label: 'Tội Phạm' },
            { name: 'Gia Đình', label: 'Gia Đình' },
            { name: 'Võ Thuật', label: 'Võ Thuật' }
        ];

        // Read initial state from URL
        const urlParams = new URLSearchParams(window.location.search);
        let currentGenre = urlParams.get('genre') || '';
        let currentStatus = urlParams.get('status') || '';
        let currentSort = urlParams.get('sort') || 'latest';
        let currentSearch = urlParams.get('search') || '';
        let currentPage = parseInt(urlParams.get('page')) || 1;
        let searchTimeout = null;

        document.addEventListener('DOMContentLoaded', () => {
            renderGenrePills();
            updateToolbarUI();
            fetchMovies();

            // Setup search debounce
            const searchInput = document.getElementById('genre-search-input');
            searchInput.value = currentSearch;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentSearch = e.target.value.trim();
                    currentPage = 1;
                    syncUrlParams();
                    fetchMovies();
                }, 400);
            });
        });

        function renderGenrePills() {
            const container = document.getElementById('genre-pills');
            container.innerHTML = GENRES_LIST.map(g => {
                const isActive = (currentGenre.toLowerCase() === g.name.toLowerCase());
                return `
                    <button type="button" onclick="setGenreFilter('${g.name}')" 
                            class="genre-pill shrink-0 px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all duration-300 border cursor-pointer ${
                                isActive 
                                    ? 'btn-primary shadow-lg shadow-[var(--color-cinema-primary)]/20 scale-105' 
                                    : 'hover:border-[var(--color-cinema-primary)] hover:text-white'
                            }"
                            style="${!isActive ? 'background: var(--color-cinema-card); border-color: var(--color-cinema-border); color: var(--color-cinema-text-muted);' : ''}">
                        <span>${g.label}</span>
                    </button>
                `;
            }).join('');
        }

        function updateToolbarUI() {
            // Status tabs
            document.querySelectorAll('.status-tab').forEach(tab => {
                tab.className = 'status-tab px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-all text-[var(--color-cinema-text-muted)] hover:text-white';
                tab.removeAttribute('style');
            });
            const activeStatusId = currentStatus ? `status-btn-${currentStatus}` : 'status-btn-all';
            const activeStatusTab = document.getElementById(activeStatusId);
            if (activeStatusTab) {
                activeStatusTab.className = 'status-tab px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-all btn-primary shadow-md';
            }

            // Sort select
            const sortSelect = document.getElementById('genre-sort-select');
            if (sortSelect) sortSelect.value = currentSort;

            // Indicator tags
            updateFilterIndicators();
        }

        function updateFilterIndicators() {
            const container = document.getElementById('filter-indicator-container');
            const tags = document.getElementById('filter-tags');
            const chips = [];

            if (currentGenre) {
                chips.push(`
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-[var(--color-cinema-primary)]/15 text-[var(--color-cinema-primary)] border border-[var(--color-cinema-primary)]/30">
                        Thể loại: <b>${currentGenre}</b>
                        <button onclick="setGenreFilter('')" class="hover:text-white cursor-pointer ml-1">✕</button>
                    </span>
                `);
            }
            if (currentStatus) {
                const label = currentStatus === 'showing' ? 'Đang chiếu' : 'Sắp chiếu';
                chips.push(`
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                        Trạng thái: <b>${label}</b>
                        <button onclick="setStatusFilter('')" class="hover:text-white cursor-pointer ml-1">✕</button>
                    </span>
                `);
            }
            if (currentSearch) {
                chips.push(`
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-purple-500/15 text-purple-400 border border-purple-500/30">
                        Tìm kiếm: <b>"${currentSearch}"</b>
                        <button onclick="clearSearchFilter()" class="hover:text-white cursor-pointer ml-1">✕</button>
                    </span>
                `);
            }

            if (chips.length > 0) {
                tags.innerHTML = chips.join('');
                container.classList.remove('hidden');
                container.classList.add('flex');
            } else {
                container.classList.add('hidden');
                container.classList.remove('flex');
            }
        }

        function setGenreFilter(genre) {
            currentGenre = genre;
            currentPage = 1;
            renderGenrePills();
            syncUrlParams();
            updateToolbarUI();
            fetchMovies();
        }

        function setStatusFilter(status) {
            currentStatus = status;
            currentPage = 1;
            syncUrlParams();
            updateToolbarUI();
            fetchMovies();
        }

        function setSortFilter(sort) {
            currentSort = sort;
            currentPage = 1;
            syncUrlParams();
            fetchMovies();
        }

        function clearSearchFilter() {
            currentSearch = '';
            document.getElementById('genre-search-input').value = '';
            currentPage = 1;
            syncUrlParams();
            updateToolbarUI();
            fetchMovies();
        }

        function resetAllFilters() {
            currentGenre = '';
            currentStatus = '';
            currentSearch = '';
            currentSort = 'latest';
            currentPage = 1;
            document.getElementById('genre-search-input').value = '';
            renderGenrePills();
            updateToolbarUI();
            syncUrlParams();
            fetchMovies();
        }

        function syncUrlParams() {
            const params = new URLSearchParams();
            if (currentGenre) params.set('genre', currentGenre);
            if (currentStatus) params.set('status', currentStatus);
            if (currentSort && currentSort !== 'latest') params.set('sort', currentSort);
            if (currentSearch) params.set('search', currentSearch);
            if (currentPage > 1) params.set('page', currentPage);

            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.replaceState({}, '', newUrl);
        }

        function fetchMovies() {
            const container = document.getElementById('movies-container');
            const countText = document.getElementById('genre-result-count');

            // Render skeleton
            container.innerHTML = Array(12).fill(0).map(() => `
                <div class="glass rounded-2xl overflow-hidden animate-pulse border border-[var(--color-cinema-border)]">
                    <div class="aspect-[2/3] bg-[var(--color-cinema-card)]"></div>
                    <div class="p-3.5 space-y-2.5">
                        <div class="h-4 bg-[var(--color-cinema-border)] rounded w-3/4"></div>
                        <div class="h-3 bg-[var(--color-cinema-border)] rounded w-1/2"></div>
                    </div>
                </div>
            `).join('');

            const queryParams = new URLSearchParams({
                page: currentPage,
                status: currentStatus || 'all',
                sort: currentSort,
            });
            if (currentGenre) queryParams.set('genre', currentGenre);
            if (currentSearch) queryParams.set('search', currentSearch);

            fetch(`/api/movies?${queryParams.toString()}`)
                .then(res => res.json())
                .then(result => {
                    const movies = result.data || [];
                    const total = result.total || 0;
                    countText.textContent = `Tìm thấy ${total} bộ phim`;

                    if (movies.length === 0) {
                        container.innerHTML = `
                            <div class="col-span-full flex flex-col items-center justify-center py-20 text-center glass rounded-3xl border border-[var(--color-cinema-border)]">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 text-[var(--color-cinema-text-muted)]" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-2">Không tìm thấy bộ phim nào</h3>
                                <p class="text-[var(--color-cinema-text-muted)] text-sm max-w-md mb-6">
                                    Không có bộ phim nào phù hợp với bộ lọc hiện tại. Vui lòng thử chọn thể loại khác hoặc xóa bớt tiêu chí lọc.
                                </p>
                                <button onclick="resetAllFilters()" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">
                                    Khám phá tất cả phim
                                </button>
                            </div>
                        `;
                        document.getElementById('pagination-container').innerHTML = '';
                        return;
                    }

                    container.innerHTML = movies.map(m => renderMovieCard(m)).join('');
                    renderPagination(result);
                })
                .catch(err => {
                    console.error('Lỗi tải danh sách phim:', err);
                    container.innerHTML = `
                        <div class="col-span-full text-center py-16 text-red-400">
                            Có lỗi khi kết nối dữ liệu. Vui lòng thử lại sau!
                        </div>
                    `;
                });
        }

        function renderMovieCard(m) {
            const poster = m.poster_url || '/images/default-poster.jpg';
            const avgRating = m.reviews_avg_rating ? parseFloat(m.reviews_avg_rating).toFixed(1) : (m.rating ? parseFloat(m.rating).toFixed(1) : null);
            const reviewsCount = m.reviews_count || 0;
            const ageRating = m.age_rating || 'T13';

            const ageBadgeClass = ageRating === 'P' ? 'bg-green-500/80 text-white' :
                                 ageRating === 'T18' ? 'bg-red-600/90 text-white' :
                                 ageRating === 'T16' ? 'bg-orange-500/90 text-white' :
                                 'bg-yellow-500/90 text-black';

            const statusBadge = m.status === 'showing' 
                ? `<span class="bg-emerald-500/90 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">Đang chiếu</span>`
                : `<span class="bg-amber-500/90 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">Sắp chiếu</span>`;

            // Display up to 2 genres
            const genresArr = (m.genre || '').split(',').map(s => s.trim()).filter(Boolean);
            const genreDisplay = genresArr.slice(0, 2).join(' • ');

            return `
                <div class="movie-card group glass rounded-2xl overflow-hidden flex flex-col border border-[var(--color-cinema-border)] hover:border-[var(--color-cinema-primary)] transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-[var(--color-cinema-primary)]/10">
                    {{-- Poster Area --}}
                    <div class="relative aspect-[2/3] overflow-hidden bg-[var(--color-cinema-card)]">
                        <img src="${poster}" alt="${m.title}" loading="lazy"
                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity"></div>
                        
                        {{-- Top Badges --}}
                        <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between">
                            <span class="${ageBadgeClass} text-[10px] font-extrabold px-1.5 py-0.5 rounded tracking-wider shadow">
                                ${ageRating}
                            </span>
                            ${statusBadge}
                        </div>

                        {{-- Rating Badge --}}
                        ${avgRating ? `
                            <div class="absolute bottom-2.5 left-2.5 flex items-center gap-1.5 bg-black/75 backdrop-blur-md px-2 py-1 rounded-lg border border-yellow-500/30">
                                <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-white font-bold text-xs">${avgRating}</span>
                                ${reviewsCount > 0 ? `<span class="text-[10px] text-gray-400">(${reviewsCount})</span>` : ''}
                            </div>
                        ` : ''}
                    </div>

                    {{-- Info Area --}}
                    <div class="p-3.5 flex flex-col flex-1 justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-sm text-white line-clamp-1 group-hover:text-[var(--color-cinema-primary)] transition-colors" title="${m.title}">
                                ${m.title}
                            </h3>
                            <p class="text-xs text-[var(--color-cinema-text-muted)] mt-1 truncate" title="${m.genre || ''}">
                                ${genreDisplay || 'Điện ảnh'}
                            </p>
                        </div>

                        <div class="pt-2 border-t border-[var(--color-cinema-border)]/50 flex items-center justify-between text-[11px] text-[var(--color-cinema-text-muted)]">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>${m.duration_minutes || 120} phút</span>
                            </span>
                            <a href="/movies/${m.id}" class="font-semibold text-[var(--color-cinema-primary)] hover:underline">
                                Chi tiết →
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderPagination(result) {
            const container = document.getElementById('pagination-container');
            const current = result.current_page || 1;
            const last = result.last_page || 1;

            if (last <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '';
            // Prev
            if (current > 1) {
                html += `
                    <button onclick="goToPage(${current - 1})" class="px-3.5 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all cursor-pointer glass hover:border-[var(--color-cinema-primary)] text-white">
                        ← Trước
                    </button>
                `;
            }

            // Pages
            for (let i = 1; i <= last; i++) {
                if (i === 1 || i === last || (i >= current - 1 && i <= current + 1)) {
                    html += `
                        <button onclick="goToPage(${i})" class="w-9 h-9 md:w-10 md:h-10 rounded-xl text-xs md:text-sm font-semibold transition-all cursor-pointer ${
                            i === current ? 'btn-primary' : 'glass hover:border-[var(--color-cinema-primary)] text-white'
                        }">
                            ${i}
                        </button>
                    `;
                } else if (i === current - 2 || i === current + 2) {
                    html += `<span class="text-[var(--color-cinema-text-muted)] px-1">...</span>`;
                }
            }

            // Next
            if (current < last) {
                html += `
                    <button onclick="goToPage(${current + 1})" class="px-3.5 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all cursor-pointer glass hover:border-[var(--color-cinema-primary)] text-white">
                        Sau →
                    </button>
                `;
            }

            container.innerHTML = html;
        }

        function goToPage(page) {
            currentPage = page;
            syncUrlParams();
            fetchMovies();
            window.scrollTo({ top: 300, behavior: 'smooth' });
        }
    </script>
</x-layouts.main>
