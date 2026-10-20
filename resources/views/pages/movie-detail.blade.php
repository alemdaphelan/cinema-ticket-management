<x-layouts.main title="Chi tiết phim - CineStar">
    <div id="movie-detail" class="w-[95%] max-w-[1600px] mx-auto px-4 py-8">
        <!-- Skeleton Loader -->
        <div id="skeleton-loader" class="animate-pulse">
            <div class="mb-6 w-32 h-4 bg-[var(--color-cinema-border)] rounded"></div>
            <div class="glass rounded-2xl overflow-hidden mb-8">
                <div class="md:flex">
                    <div class="md:w-1/3 lg:w-1/4 h-[400px] bg-[var(--color-cinema-border)] opacity-30"></div>
                    <div class="md:w-2/3 lg:w-3/4 p-6 md:p-8 space-y-4">
                        <div class="h-10 bg-[var(--color-cinema-border)] rounded w-3/4 opacity-30"></div>
                        <div class="flex gap-3">
                            <div class="h-5 bg-[var(--color-cinema-border)] rounded w-20 opacity-30"></div>
                            <div class="h-5 bg-[var(--color-cinema-border)] rounded w-24 opacity-30"></div>
                            <div class="h-5 bg-[var(--color-cinema-border)] rounded w-24 opacity-30"></div>
                        </div>
                        <div class="pt-6 space-y-3">
                            <div class="h-4 bg-[var(--color-cinema-border)] rounded w-full opacity-30"></div>
                            <div class="h-4 bg-[var(--color-cinema-border)] rounded w-full opacity-30"></div>
                            <div class="h-4 bg-[var(--color-cinema-border)] rounded w-4/5 opacity-30"></div>
                        </div>
                        <div class="pt-6">
                            <div class="h-4 bg-[var(--color-cinema-border)] rounded w-32 mb-3 opacity-30"></div>
                            <div class="w-full aspect-video bg-[var(--color-cinema-border)] rounded-xl opacity-30"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="h-6 bg-[var(--color-cinema-border)] rounded w-32 mb-4 opacity-30"></div>
                <div class="flex gap-2 mb-6">
                    <div class="h-10 w-24 bg-[var(--color-cinema-border)] rounded-lg opacity-30"></div>
                    <div class="h-10 w-24 bg-[var(--color-cinema-border)] rounded-lg opacity-30"></div>
                    <div class="h-10 w-24 bg-[var(--color-cinema-border)] rounded-lg opacity-30"></div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <div class="h-24 bg-[var(--color-cinema-border)] rounded-xl opacity-30"></div>
                    <div class="h-24 bg-[var(--color-cinema-border)] rounded-xl opacity-30"></div>
                    <div class="h-24 bg-[var(--color-cinema-border)] rounded-xl opacity-30"></div>
                    <div class="h-24 bg-[var(--color-cinema-border)] rounded-xl opacity-30"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const movieId = {{ $movieId }};
        let movieReviewStats = {};
        let movieHasWatched = false;
        let movieHasReviewed = false;

        document.addEventListener('DOMContentLoaded', function() {
            fetch(`/api/movies/${movieId}`)
                .then(res => res.json())
                .then(result => {
                    const movie = result.data;
                    movieReviewStats = result.review_stats || { total_reviews: 0, average_rating: 0 };
                    movieHasWatched = result.has_watched || false;
                    movieHasReviewed = result.has_reviewed || false;
                    renderMovieDetail(movie);
                    loadShows(movieId);
                    loadReviews(movieId);
                })
                .catch(err => {
                    document.getElementById('movie-detail').innerHTML = '<p class="text-center text-red-400 py-20">Không tìm thấy phim.</p>';
                });
        });

        function renderTeaserPlayer(teaserUrl) {
            if (!teaserUrl) return '';
            const isMp4 = teaserUrl.endsWith('.mp4') || teaserUrl.includes('/storage/teasers/');
            if (isMp4) {
                return `<video src="${teaserUrl}" class="w-full h-full" controls preload="metadata"></video>`;
            }
            return `<iframe src="${teaserUrl}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>`;
        }

        function renderMovieDetail(movie) {
            const container = document.getElementById('movie-detail');
            const avgRating = movieReviewStats.average_rating || 0;
            const totalReviews = movieReviewStats.total_reviews || 0;

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
                                            ${movie.director ? `<span>${movie.director}</span>` : ''}
                                            ${movie.duration_minutes ? `<span>${movie.duration_minutes} phút</span>` : ''}
                                            ${movie.genre ? `<span>${movie.genre}</span>` : ''}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        ${totalReviews > 0 ? `
                                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full" style="background: var(--color-cinema-primary); background: linear-gradient(135deg, rgba(255,183,77,0.2), rgba(255,152,0,0.2)); border: 1px solid rgba(255,183,77,0.3);">
                                                <span class="text-lg font-bold" style="color: var(--color-cinema-accent);">${avgRating}</span>
                                                <span class="text-xs text-[var(--color-cinema-text-muted)]">/ 10</span>
                                                <span class="text-xs text-[var(--color-cinema-text-muted)] ml-1">${totalReviews} đánh giá</span>
                                            </div>
                                        ` : ''}
                                        <span class="text-xs px-3 py-1.5 rounded-full font-semibold ${movie.status === 'showing' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400'}">
                                            ${movie.status === 'showing' ? 'Đang chiếu' : 'Sắp chiếu'}
                                        </span>
                                    </div>
                                </div>

                                ${movie.description ? `
                                    <div class="mt-6">
                                        <h3 class="text-sm font-semibold text-[var(--color-cinema-accent)] mb-2">Nội dung phim</h3>
                                        <p class="text-sm text-[var(--color-cinema-text-muted)] leading-relaxed">${movie.description}</p>
                                    </div>
                                ` : ''}

                                ${movie.teaser_url ? `
                                    <div class="mt-6">
                                        <h3 class="text-sm font-semibold text-[var(--color-cinema-accent)] mb-3">Teaser / Trailer</h3>
                                        <div class="aspect-video rounded-xl overflow-hidden" style="background: var(--color-cinema-card);">
                                            ${renderTeaserPlayer(movie.teaser_url)}
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>

                    {{-- Shows Section --}}
                    ${movie.status === 'showing' ? `
                        <div class="mt-8">
                            <h2 class="text-xl font-bold text-white mb-4">Lịch chiếu</h2>
                            <div class="flex gap-2 mb-6 overflow-x-auto pb-2" id="date-tabs"></div>
                            <div id="shows-list" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                <p class="col-span-full text-center text-[var(--color-cinema-text-muted)] py-8">Chọn ngày để xem lịch chiếu</p>
                            </div>
                        </div>
                    ` : `
                        <div class="mt-8 glass rounded-xl p-8 text-center">
                            <p class="text-[var(--color-cinema-text-muted)]">Phim sắp chiếu — Lịch chiếu sẽ được cập nhật sớm!</p>
                        </div>
                    `}

                    {{-- Reviews Section (MoMo Style) --}}
                    <div class="mt-12" id="reviews-section">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <h2 class="text-xl md:text-2xl font-bold text-white">Đánh giá từ khán giả</h2>
                                </div>
                                <p class="text-xs md:text-sm text-[var(--color-cinema-text-muted)] mt-1">
                                    Đánh giá minh bạch & khách quan từ cộng đồng khán giả đã thưởng thức phim tại rạp
                                </p>
                            </div>
                        </div>

                        {{-- MoMo Overview Card --}}
                        <div id="momo-summary-card" class="glass rounded-3xl p-6 md:p-8 mb-8 border border-[var(--color-cinema-border)] shadow-xl relative overflow-hidden">
                            <div class="text-center py-6 text-[var(--color-cinema-text-muted)]">
                                <div class="inline-block w-6 h-6 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-xs mt-2">Đang tổng hợp đánh giá...</p>
                            </div>
                        </div>

                        {{-- Review Eligibility & Form --}}
                        ${window.IS_AUTHENTICATED ? (
                            movieHasReviewed ? `
                                <div class="glass rounded-2xl p-6 mb-8 border border-emerald-500/30 bg-emerald-500/5 text-center">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl mb-3 border border-emerald-500/30">
                                        ✓
                                    </div>
                                    <h3 class="text-base font-bold text-white mb-1">Bạn đã đánh giá bộ phim này!</h3>
                                    <p class="text-xs md:text-sm text-[var(--color-cinema-text-muted)] max-w-md mx-auto">
                                        Cảm ơn bạn đã chia sẻ nhận xét thực tế. Đánh giá của bạn giúp cộng đồng yêu phim có góc nhìn chân thực và đa chiều nhất.
                                    </p>
                                </div>
                            ` : (movieHasWatched ? `
                                <div class="glass rounded-3xl p-6 md:p-8 mb-8 border border-[var(--color-cinema-primary)]/40 shadow-2xl relative overflow-hidden" 
                                     style="background: linear-gradient(135deg, rgba(30, 20, 35, 0.95), rgba(20, 20, 28, 0.95));">
                                    <div class="flex items-center justify-between flex-wrap gap-3 mb-6 pb-4 border-b border-[var(--color-cinema-border)]/50">
                                        <div class="flex items-center gap-3">
                                            ${window.AUTH_USER?.avatar ? `
                                                <img src="${window.AUTH_USER.avatar}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border border-[var(--color-cinema-border)] shadow">
                                            ` : `
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shadow" 
                                                     style="background: linear-gradient(135deg, var(--color-cinema-primary), var(--color-cinema-accent));">
                                                    ${window.AUTH_USER?.initial || (window.AUTH_USER?.name || 'U').replace(/^Khách hàng\s+/i, '').charAt(0).toUpperCase()}
                                                </div>
                                            `}
                                            <div>
                                                <h3 class="text-base font-bold text-white flex items-center gap-2">
                                                    Viết đánh giá của bạn
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        Đã xem tại rạp
                                                    </span>
                                                </h3>
                                                <p class="text-xs text-[var(--color-cinema-text-muted)]">Bạn vừa thưởng thức phim xong, cảm nhận của bạn thế nào?</p>
                                            </div>
                                        </div>
                                    </div>

                                    <form onsubmit="submitReview(event)" class="space-y-5">
                                        {{-- Rating Selection --}}
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <label class="block text-xs md:text-sm font-semibold text-white">
                                                    Chấm điểm bộ phim: <span id="rating-sentiment" class="font-bold text-[var(--color-cinema-accent)]">Chọn số điểm từ 1 đến 10</span>
                                                </label>
                                            </div>
                                            <div class="grid grid-cols-5 sm:grid-cols-10 gap-2" id="rating-selector">
                                                ${Array.from({length: 10}, (_, i) => `
                                                    <button type="button" onclick="selectRating(${i + 1})"
                                                            class="rating-btn py-2.5 rounded-xl text-xs md:text-sm font-bold transition-all duration-200 cursor-pointer flex flex-col items-center justify-center gap-0.5 border"
                                                            style="background: var(--color-cinema-card); border-color: var(--color-cinema-border); color: var(--color-cinema-text-muted);"
                                                            data-value="${i + 1}">
                                                        <span>${i + 1}</span>
                                                        <span class="text-[10px] opacity-70">★</span>
                                                    </button>
                                                `).join('')}
                                            </div>
                                            <input type="hidden" id="review-rating" value="">
                                        </div>

                                        {{-- Fast Tag Suggestions (MoMo style) --}}
                                        <div>
                                            <span class="text-xs text-[var(--color-cinema-text-muted)] block mb-2">Gợi ý cảm xúc nhanh (nhấn để thêm vào bình luận):</span>
                                            <div class="flex flex-wrap gap-2">
                                                <button type="button" onclick="insertTag('#DiễnXuấtĐỉnh')" class="px-3 py-1 rounded-full text-xs font-medium border border-[var(--color-cinema-border)] bg-[var(--color-cinema-card)] text-[var(--color-cinema-text-muted)] hover:text-white hover:border-[var(--color-cinema-primary)] transition-colors cursor-pointer">
                                                    + Diễn xuất đỉnh
                                                </button>
                                                <button type="button" onclick="insertTag('#KịchBảnLôiCuốn')" class="px-3 py-1 rounded-full text-xs font-medium border border-[var(--color-cinema-border)] bg-[var(--color-cinema-card)] text-[var(--color-cinema-text-muted)] hover:text-white hover:border-[var(--color-cinema-primary)] transition-colors cursor-pointer">
                                                    + Kịch bản lôi cuốn
                                                </button>
                                                <button type="button" onclick="insertTag('#KỹXảoMãnNhãn')" class="px-3 py-1 rounded-full text-xs font-medium border border-[var(--color-cinema-border)] bg-[var(--color-cinema-card)] text-[var(--color-cinema-text-muted)] hover:text-white hover:border-[var(--color-cinema-primary)] transition-colors cursor-pointer">
                                                    + Kỹ xảo mãn nhãn
                                                </button>
                                                <button type="button" onclick="insertTag('#ÂmThanhSốngĐộng')" class="px-3 py-1 rounded-full text-xs font-medium border border-[var(--color-cinema-border)] bg-[var(--color-cinema-card)] text-[var(--color-cinema-text-muted)] hover:text-white hover:border-[var(--color-cinema-primary)] transition-colors cursor-pointer">
                                                    + Âm thanh sống động
                                                </button>
                                                <button type="button" onclick="insertTag('#RấtĐángTiềnVé')" class="px-3 py-1 rounded-full text-xs font-medium border border-[var(--color-cinema-border)] bg-[var(--color-cinema-card)] text-[var(--color-cinema-text-muted)] hover:text-white hover:border-[var(--color-cinema-primary)] transition-colors cursor-pointer">
                                                    + Rất đáng tiền vé
                                                </button>
                                                <button type="button" onclick="insertTag('#CáiKếtBấtNgờ')" class="px-3 py-1 rounded-full text-xs font-medium border border-[var(--color-cinema-border)] bg-[var(--color-cinema-card)] text-[var(--color-cinema-text-muted)] hover:text-white hover:border-[var(--color-cinema-primary)] transition-colors cursor-pointer">
                                                    + Cái kết bất ngờ
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Comment Textarea --}}
                                        <div>
                                            <div class="flex items-center justify-between mb-1.5">
                                                <label class="block text-xs md:text-sm font-semibold text-white">Nhận xét chi tiết</label>
                                                <span id="char-counter" class="text-[11px] text-[var(--color-cinema-text-muted)]">0 / 1000 ký tự</span>
                                            </div>
                                            <textarea id="review-comment" rows="3" maxlength="1000"
                                                      oninput="updateCharCount(this)"
                                                      class="w-full px-4 py-3 rounded-2xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)] transition-all resize-none"
                                                      style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"
                                                      placeholder="Chia sẻ chi tiết về kịch bản, diễn xuất, âm thanh, bối cảnh phim để cộng đồng cùng thảo luận nhé..."></textarea>
                                        </div>

                                        <div class="flex justify-end pt-1">
                                            <button type="submit" id="submit-review-btn" class="btn-primary px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-[var(--color-cinema-primary)]/20 cursor-pointer">
                                                Gửi đánh giá ngay
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            ` : `
                                <div class="glass rounded-3xl p-6 md:p-8 mb-8 border border-amber-500/30 bg-amber-500/5 shadow-xl">
                                    <div class="flex flex-col sm:flex-row items-center justify-between gap-5 text-center sm:text-left">
                                        <div class="flex flex-col sm:flex-row items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 shrink-0 shadow-inner">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="flex items-center justify-center sm:justify-start gap-2 mb-1">
                                                    <h3 class="text-base md:text-lg font-bold text-white">Quyền đánh giá dành cho khán giả đã xem phim</h3>
                                                </div>
                                                <p class="text-xs md:text-sm text-[var(--color-cinema-text-muted)] max-w-xl leading-relaxed">
                                                    Để đảm bảo tính khách quan và uy tín, chỉ những khán giả đã đặt vé và xem phim xong tại cụm rạp CineStar mới có quyền tham gia chấm điểm và viết bình luận.
                                                </p>
                                            </div>
                                        </div>
                                        <button onclick="scrollToShows()" class="btn-primary shrink-0 px-6 py-3 rounded-xl text-xs md:text-sm font-bold flex items-center gap-2 cursor-pointer shadow-lg shadow-[var(--color-cinema-primary)]/20 whitespace-nowrap">
                                            Đặt vé xem phim ngay
                                        </button>
                                    </div>
                                </div>
                            `)
                        ) : `
                            <div class="glass rounded-3xl p-6 md:p-8 mb-8 border border-[var(--color-cinema-border)] text-center shadow-xl">
                                <div class="w-12 h-12 mx-auto rounded-full bg-[var(--color-cinema-card)] flex items-center justify-center text-[var(--color-cinema-text-muted)] mb-3 border border-[var(--color-cinema-border)]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-base font-bold text-white mb-1">Đăng nhập để đánh giá phim</h3>
                                <p class="text-xs md:text-sm text-[var(--color-cinema-text-muted)] max-w-md mx-auto mb-4">
                                    Vui lòng đăng nhập để kiểm tra quyền đánh giá (Dành riêng cho khán giả đã mua vé và xem phim xong tại rạp).
                                </p>
                                <a href="/login" class="btn-primary inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold">
                                    Đăng nhập tài khoản
                                </a>
                            </div>
                        `}

                        {{-- MoMo Review Filter Tabs --}}
                        <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6" id="review-filter-tabs">
                            <button type="button" onclick="filterReviews('all')" id="rf-tab-all"
                                    class="review-filter-btn px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition-all cursor-pointer btn-primary shadow-md">
                                Tất cả (<span id="rf-count-all">0</span>)
                            </button>
                            <button type="button" onclick="filterReviews('9-10')" id="rf-tab-9-10"
                                    class="review-filter-btn px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all cursor-pointer border"
                                    style="background: var(--color-cinema-card); border-color: var(--color-cinema-border); color: var(--color-cinema-text-muted);">
                                Khen nhiều (9 - 10 sao)
                            </button>
                            <button type="button" onclick="filterReviews('7-8')" id="rf-tab-7-8"
                                    class="review-filter-btn px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all cursor-pointer border"
                                    style="background: var(--color-cinema-card); border-color: var(--color-cinema-border); color: var(--color-cinema-text-muted);">
                                Khá & Hay (7 - 8 sao)
                            </button>
                            <button type="button" onclick="filterReviews('under_7')" id="rf-tab-under_7"
                                    class="review-filter-btn px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all cursor-pointer border"
                                    style="background: var(--color-cinema-card); border-color: var(--color-cinema-border); color: var(--color-cinema-text-muted);">
                                Ý kiến khác (≤ 6 sao)
                            </button>
                        </div>

                        {{-- Reviews List --}}
                        <div id="reviews-list" class="space-y-4">
                            <div class="text-center py-10">
                                <div class="inline-block w-8 h-8 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-2">Đang tải danh sách nhận xét...</p>
                            </div>
                        </div>

                        {{-- Load More --}}
                        <div id="reviews-load-more" class="hidden text-center mt-6">
                            <button onclick="loadMoreReviews()" class="text-sm px-8 py-3 rounded-xl transition-all hover:border-[var(--color-cinema-primary)] font-semibold cursor-pointer glass"
                                    style="color: var(--color-cinema-text);">
                                Xem thêm nhận xét khác ↓
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // === MoMo Rating Selector Logic ===
        const RATING_SENTIMENTS = {
            10: '10/10 - Cực phẩm! Tuyệt tác điện ảnh không thể bỏ lỡ',
            9:  '9/10 - Phim rất hay! Đáng từng phút giây thưởng thức',
            8:  '8/10 - Hay! Kịch bản cuốn hút, diễn xuất rất tốt',
            7:  '7/10 - Khá ổn! Phim giải trí tốt cho ngày cuối tuần',
            6:  '6/10 - Tạm được! Xem ổn nhưng chưa có nhiều đột phá',
            5:  '5/10 - Bình thường, không có nhiều ấn tượng đọng lại',
            4:  '4/10 - Dưới kỳ vọng, kịch bản còn nhiều điểm trừ',
            3:  '3/10 - Khá chán, nhịp phim buồn ngủ và rời rạc',
            2:  '2/10 - Tệ, không khuyến khích bạn bè đi xem',
            1:  '1/10 - Rất tệ, trải nghiệm xem phim thất vọng'
        };

        let selectedRating = 0;
        function selectRating(value) {
            selectedRating = value;
            document.getElementById('review-rating').value = value;
            document.getElementById('rating-sentiment').textContent = RATING_SENTIMENTS[value] || `${value}/10 Điểm`;

            document.querySelectorAll('.rating-btn').forEach(btn => {
                const v = parseInt(btn.dataset.value);
                if (v <= value) {
                    btn.style.background = 'linear-gradient(135deg, var(--color-cinema-primary), var(--color-cinema-accent))';
                    btn.style.color = '#fff';
                    btn.style.borderColor = 'var(--color-cinema-accent)';
                    btn.classList.add('scale-105', 'shadow-md');
                } else {
                    btn.style.background = 'var(--color-cinema-card)';
                    btn.style.color = 'var(--color-cinema-text-muted)';
                    btn.style.borderColor = 'var(--color-cinema-border)';
                    btn.classList.remove('scale-105', 'shadow-md');
                }
            });
        }

        function insertTag(tag) {
            const textarea = document.getElementById('review-comment');
            if (!textarea) return;
            if (textarea.value.includes(tag)) return;
            textarea.value = textarea.value ? textarea.value.trim() + ' ' + tag : tag + ' ';
            updateCharCount(textarea);
            textarea.focus();
        }

        function updateCharCount(el) {
            const counter = document.getElementById('char-counter');
            if (counter) {
                counter.textContent = `${el.value.length} / 1000 ký tự`;
            }
        }

        function scrollToShows() {
            const el = document.getElementById('shows-list') || document.getElementById('date-tabs');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // === Submit Review ===
        function submitReview(e) {
            e.preventDefault();
            const rating = document.getElementById('review-rating').value;
            const comment = document.getElementById('review-comment').value.trim();

            if (!rating) {
                if (window.showNotificationModal) {
                    window.showNotificationModal({
                        title: 'Chưa chọn điểm',
                        message: 'Vui lòng chọn số điểm đánh giá (từ 1 đến 10)!',
                        type: 'warning'
                    });
                } else {
                    alert('Vui lòng chọn số điểm đánh giá (từ 1 đến 10)!');
                }
                return;
            }

            const btn = document.getElementById('submit-review-btn');
            btn.disabled = true;
            btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span> Đang gửi đánh giá...`;

            fetch(`/api/movies/${movieId}/reviews`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN,
                },
                body: JSON.stringify({ rating: parseInt(rating), comment: comment || null }),
            })
            .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
            .then(({ ok, status, data }) => {
                if (ok) {
                    movieHasReviewed = true;
                    if (window.showNotificationModal) {
                        window.showNotificationModal({
                            title: 'Thành công',
                            message: 'Chúc mừng bạn đã gửi đánh giá thành công! Nhận xét đã được ghi nhận vào hệ thống.',
                            type: 'success',
                            onConfirm: () => window.location.reload()
                        });
                    } else {
                        alert('Chúc mừng bạn đã gửi đánh giá thành công!');
                        window.location.reload();
                    }
                } else {
                    if (window.showNotificationModal) {
                        window.showNotificationModal({
                            title: 'Không thể gửi đánh giá',
                            message: data.message || 'Không thể gửi đánh giá. Vui lòng kiểm tra lại!',
                            type: 'error'
                        });
                    } else {
                        alert(data.message || 'Không thể gửi đánh giá. Vui lòng kiểm tra lại!');
                    }
                    btn.disabled = false;
                    btn.innerHTML = 'Gửi đánh giá ngay';
                }
            })
            .catch(() => {
                if (window.showNotificationModal) {
                    window.showNotificationModal({
                        title: 'Lỗi kết nối',
                        message: 'Có lỗi kết nối máy chủ. Vui lòng thử lại sau!',
                        type: 'error'
                    });
                } else {
                    alert('Có lỗi kết nối máy chủ. Vui lòng thử lại sau!');
                }
                btn.disabled = false;
                btn.innerHTML = 'Gửi đánh giá ngay';
            });
        }

        // === Load Reviews (MoMo Architecture) ===
        let currentRatingFilter = 'all';
        let reviewsPage = 1;
        let reviewsLastPage = 1;
        let helpfulCounts = {};

        function filterReviews(filterGroup) {
            currentRatingFilter = filterGroup;
            reviewsPage = 1;

            // Update tab styles
            document.querySelectorAll('.review-filter-btn').forEach(btn => {
                btn.className = 'review-filter-btn px-4 py-2 rounded-xl text-xs md:text-sm font-semibold transition-all cursor-pointer border';
                btn.style = 'background: var(--color-cinema-card); border-color: var(--color-cinema-border); color: var(--color-cinema-text-muted);';
            });
            const activeTab = document.getElementById(`rf-tab-${filterGroup}`);
            if (activeTab) {
                activeTab.className = 'review-filter-btn px-4 py-2 rounded-xl text-xs md:text-sm font-bold transition-all cursor-pointer btn-primary shadow-md';
                activeTab.removeAttribute('style');
            }

            loadReviews(movieId, 1, true);
        }

        function loadReviews(id, page = 1, isFilterChange = false) {
            fetch(`/api/movies/${id}/reviews?rating_filter=${currentRatingFilter}&page=${page}`)
                .then(res => res.json())
                .then(result => {
                    const reviews = result.data || [];
                    const summary = result.summary || {};
                    reviewsLastPage = result.last_page || 1;
                    reviewsPage = result.current_page || 1;

                    // Update MoMo Overview Card
                    renderMomoSummaryCard(summary);

                    // Update Tab Count
                    const allCountEl = document.getElementById('rf-count-all');
                    if (allCountEl && summary.total_reviews !== undefined) {
                        allCountEl.textContent = summary.total_reviews;
                    }

                    const container = document.getElementById('reviews-list');
                    if (reviews.length === 0) {
                        container.innerHTML = `
                            <div class="text-center py-12 glass rounded-2xl border border-[var(--color-cinema-border)]">
                                <p class="text-3xl mb-2">💬</p>
                                <p class="text-sm font-semibold text-white">Chưa có nhận xét nào trong mục này</p>
                                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-1">Hãy là người đầu tiên chia sẻ cảm nhận về bộ phim!</p>
                            </div>
                        `;
                        document.getElementById('reviews-load-more').classList.add('hidden');
                        return;
                    }

                    const html = reviews.map(r => renderMomoReviewItem(r)).join('');
                    if (page === 1) {
                        container.innerHTML = html;
                    } else {
                        container.innerHTML += html;
                    }

                    // Load more button
                    const loadMoreBtn = document.getElementById('reviews-load-more');
                    if (reviewsPage < reviewsLastPage) {
                        loadMoreBtn.classList.remove('hidden');
                    } else {
                        loadMoreBtn.classList.add('hidden');
                    }
                })
                .catch(err => {
                    console.error('Lỗi tải đánh giá:', err);
                });
        }

        function renderMomoSummaryCard(summary) {
            const card = document.getElementById('momo-summary-card');
            if (!card) return;

            const avg = summary.average_rating ? summary.average_rating.toFixed(1) : '0.0';
            const total = summary.total_reviews || 0;
            const satisfaction = summary.satisfaction_rate || 100;
            const bd = summary.breakdown || {};
            const tags = summary.top_tags || [];

            // Generate full/half stars
            const starsCount = Math.round(avg / 2);
            let starIcons = '';
            for (let i = 1; i <= 5; i++) {
                starIcons += `<span class="${i <= starsCount ? 'text-yellow-400' : 'text-gray-600'} text-lg">★</span>`;
            }

            card.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                    {{-- Column 1: Big Score --}}
                    <div class="md:col-span-4 flex flex-col items-center justify-center text-center p-4 border-b md:border-b-0 md:border-r border-[var(--color-cinema-border)]/60">
                        <span class="text-5xl md:text-6xl font-extrabold text-white tracking-tight">${avg}</span>
                        <div class="flex items-center gap-1 my-2">
                            ${starIcons}
                        </div>
                        <span class="text-xs text-[var(--color-cinema-text-muted)] mb-3">${total} lượt đánh giá & nhận xét</span>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            ${satisfaction}% Khán giả hài lòng
                        </div>
                    </div>

                    {{-- Column 2: Progress Bars (MoMo Style) --}}
                    <div class="md:col-span-5 space-y-2 px-2">
                        ${renderBreakdownRow('9 - 10 sao', bd.stars_9_10?.percentage || 0, bd.stars_9_10?.count || 0, 'bg-emerald-500')}
                        ${renderBreakdownRow('7 - 8 sao', bd.stars_7_8?.percentage || 0, bd.stars_7_8?.count || 0, 'bg-blue-500')}
                        ${renderBreakdownRow('5 - 6 sao', bd.stars_5_6?.percentage || 0, bd.stars_5_6?.count || 0, 'bg-yellow-500')}
                        ${renderBreakdownRow('3 - 4 sao', bd.stars_3_4?.percentage || 0, bd.stars_3_4?.count || 0, 'bg-orange-500')}
                        ${renderBreakdownRow('1 - 2 sao', bd.stars_1_2?.percentage || 0, bd.stars_1_2?.count || 0, 'bg-red-500')}
                    </div>

                    {{-- Column 3: Emotion Highlight Chips --}}
                    <div class="md:col-span-3 border-t md:border-t-0 md:border-l border-[var(--color-cinema-border)]/60 pt-4 md:pt-0 md:pl-5">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[var(--color-cinema-accent)] mb-3 flex items-center gap-1.5">
                            Điểm nhấn cảm xúc
                        </h4>
                        <div class="space-y-2">
                            ${tags.slice(0, 4).map(t => `
                                <div class="flex items-center justify-between text-xs bg-[var(--color-cinema-card)] px-3 py-1.5 rounded-lg border border-[var(--color-cinema-border)]/60">
                                    <span class="text-white">${t.tag}</span>
                                    <span class="font-bold text-[var(--color-cinema-primary)]">${t.percentage}%</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderBreakdownRow(label, pct, count, barColor) {
            return `
                <div class="flex items-center gap-3 text-xs">
                    <span class="w-16 text-[var(--color-cinema-text-muted)] font-medium shrink-0">${label}</span>
                    <div class="flex-1 h-2 rounded-full overflow-hidden bg-[var(--color-cinema-card)] border border-[var(--color-cinema-border)]/40">
                        <div class="h-full ${barColor} rounded-full transition-all duration-700" style="width: ${pct}%"></div>
                    </div>
                    <span class="w-9 text-right text-[var(--color-cinema-text-muted)] text-[11px] font-mono shrink-0">${pct}%</span>
                </div>
            `;
        }

        function loadMoreReviews() {
            loadReviews(movieId, reviewsPage + 1);
        }

        function formatWatchedTime(timeString) {
            if (!timeString) return 'Gần đây';
            const past = new Date(timeString);
            const now = new Date();
            const diffMs = Math.max(0, now - past);
            const diffSeconds = Math.floor(diffMs / 1000);
            const diffMinutes = Math.floor(diffSeconds / 60);
            const diffHours = Math.floor(diffMinutes / 60);
            const diffDays = Math.floor(diffHours / 24);

            if (diffDays === 0) {
                if (diffHours === 0) {
                    return 'Vừa xem hôm nay';
                }
                return `Xem hôm nay (${diffHours} giờ trước)`;
            }

            // 1..7 ngày trước
            if (diffDays <= 7) {
                return `Xem ${diffDays} ngày trước`;
            }

            // 1..4 tuần trước
            const diffWeeks = Math.floor(diffDays / 7);
            if (diffWeeks <= 4) {
                return `Xem ${diffWeeks} tuần trước`;
            }

            // 1..12 tháng trước
            const diffMonths = Math.floor(diffDays / 30);
            if (diffMonths <= 12) {
                return `Xem ${Math.max(1, diffMonths)} tháng trước`;
            }

            // 1..n năm trước
            const diffYears = Math.floor(diffDays / 365);
            return `Xem ${Math.max(1, diffYears)} năm trước`;
        }

        function renderMomoReviewItem(review) {
            const date = new Date(review.created_at);
            const dateStr = date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
            const watchedText = formatWatchedTime(review.watched_at || review.created_at);
            const user = review.user || {};
            const userName = (user.name || 'Khán giả CineStar').replace(/^Khách hàng\s+/i, '');
            const initial = userName.charAt(0).toUpperCase();
            const avatarHtml = user.avatar
                ? `<img src="${user.avatar}" alt="${userName}" class="w-11 h-11 rounded-full object-cover border border-[var(--color-cinema-border)] shadow">`
                : `<div class="w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold text-white shadow" style="background: linear-gradient(135deg, var(--color-cinema-primary), var(--color-cinema-accent));">${initial}</div>`;

            const rVal = review.rating || 10;
            const rBadgeLabel = rVal >= 9 ? 'Cực phẩm' : (rVal >= 7 ? 'Hay' : (rVal >= 5 ? 'Ổn' : 'Bình thường'));
            const rBadgeColor = rVal >= 9 ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' :
                               (rVal >= 7 ? 'bg-blue-500/15 text-blue-400 border-blue-500/30' :
                               'bg-yellow-500/15 text-yellow-400 border-yellow-500/30');

            const currentLikes = helpfulCounts[review.id] || Math.floor((review.id * 7) % 23) + 2;

            return `
                <div class="glass rounded-2xl p-5 border border-[var(--color-cinema-border)] hover:border-[var(--color-cinema-primary)]/40 transition-all duration-300 shadow-md">
                    <div class="flex items-start gap-3.5">
                        <div class="shrink-0">${avatarHtml}</div>
                        <div class="flex-1 min-w-0">
                            {{-- User Header --}}
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-bold text-white">${userName}</span>
                                    {{-- MoMo Verified Badge kèm thời gian xem --}}
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 shadow-sm" title="Khán giả đã mua vé và xem phim hoàn tất tại rạp">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Đã xem tại rạp • ${watchedText}
                                    </span>
                                </div>
                                <span class="text-xs text-[var(--color-cinema-text-muted)]" title="Ngày đăng đánh giá">${dateStr}</span>
                            </div>

                            {{-- Rating Pill --}}
                            <div class="flex items-center gap-2 mb-3">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold border ${rBadgeColor}">
                                    <span class="text-yellow-400">★</span>
                                    <span>${rVal} / 10</span>
                                    <span class="opacity-80 font-normal">| ${rBadgeLabel}</span>
                                </div>
                            </div>

                            {{-- Comment Content --}}
                            ${review.comment ? `
                                <p class="text-sm text-gray-200 leading-relaxed font-normal whitespace-pre-line mb-3">
                                    ${review.comment}
                                </p>
                            ` : ''}

                            {{-- Interaction Bar (MoMo Style) --}}
                            <div class="pt-2 border-t border-[var(--color-cinema-border)]/40 flex items-center justify-between text-xs text-[var(--color-cinema-text-muted)]">
                                <button type="button" onclick="toggleHelpful(this, ${review.id})" class="flex items-center gap-1.5 hover:text-[var(--color-cinema-primary)] transition-colors cursor-pointer group">
                                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                    </svg>
                                    <span>Hữu ích (<b id="like-count-${review.id}">${currentLikes}</b>)</span>
                                </button>
                                <span class="text-[11px] text-gray-500">Trải nghiệm rạp CineStar</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function toggleHelpful(btn, reviewId) {
            const countEl = document.getElementById(`like-count-${reviewId}`);
            if (!countEl) return;
            let current = parseInt(countEl.textContent) || 0;
            if (btn.classList.contains('liked')) {
                btn.classList.remove('liked');
                btn.classList.remove('text-[var(--color-cinema-primary)]');
                countEl.textContent = current - 1;
            } else {
                btn.classList.add('liked');
                btn.classList.add('text-[var(--color-cinema-primary)]');
                countEl.textContent = current + 1;
            }
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
            showsList.innerHTML = Array(4).fill(0).map(() => `
                <div class="glass rounded-xl p-4 h-24 animate-pulse bg-[var(--color-cinema-border)]/30"></div>
            `).join('');

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
                        const durationMins = show.movie?.duration_minutes || movieData?.duration_minutes || 120;
                        const endTime = show.end_time ? new Date(show.end_time) : new Date(startTime.getTime() + durationMins * 60000);

                        const startStr = startTime.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                        const endStr = endTime.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                        const isPast = startTime < new Date();

                        return `
                            <a href="${isPast ? '#' : '/booking/seats/' + show.id}"
                               class="glass rounded-xl p-4 text-center transition-all ${isPast ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105 hover:border-[var(--color-cinema-primary)]'}"
                               ${isPast ? 'onclick="event.preventDefault()"' : ''}>
                                <div class="text-xl font-bold text-white mb-0.5">${startStr} ~ ${endStr}</div>
                                <div class="text-xs text-[var(--color-cinema-text-muted)]">${show.room?.name || 'Phòng chiếu'}</div>
                                <div class="text-sm font-semibold mt-2" style="color: var(--color-cinema-accent);">${Number(show.price).toLocaleString('vi-VN')}đ</div>
                                ${isPast ? '<span class="text-xs text-red-400 mt-1 block">Đã chiếu</span>' : ''}
                            </a>
                        `;
                    }).join('');
                });
        }
    </script>
</x-layouts.main>
