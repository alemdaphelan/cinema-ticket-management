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

                    {{-- Reviews Section --}}
                    <div class="mt-8" id="reviews-section">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-white">Đánh giá & Bình luận</h2>
                            <div class="flex items-center gap-3">
                                ${totalReviews > 0 ? `
                                    <span class="text-sm text-[var(--color-cinema-text-muted)]">${totalReviews} đánh giá</span>
                                    <span class="text-lg font-bold" style="color: var(--color-cinema-accent);">${avgRating} / 10</span>
                                ` : `
                                    <span class="text-sm text-[var(--color-cinema-text-muted)]">Chưa có đánh giá</span>
                                `}
                            </div>
                        </div>

                        {{-- Review Form --}}
                        ${window.IS_AUTHENTICATED ? (
                            movieHasReviewed ? `
                                <div class="glass rounded-xl p-4 mb-6 text-center">
                                    <p class="text-sm text-[var(--color-cinema-text-muted)]">Bạn đã đánh giá phim này</p>
                                </div>
                            ` : (movieHasWatched ? `
                                <div class="glass rounded-xl p-6 mb-6">
                                    <h3 class="text-sm font-semibold text-white mb-4">Viết đánh giá của bạn</h3>
                                    <form onsubmit="submitReview(event)" class="space-y-4">
                                        <div>
                                            <label class="block text-sm text-[var(--color-cinema-text-muted)] mb-2">Điểm đánh giá</label>
                                            <div class="flex gap-1" id="rating-selector">
                                                ${Array.from({length: 10}, (_, i) => `
                                                    <button type="button" onclick="selectRating(${i + 1})"
                                                            class="rating-btn w-9 h-9 rounded-lg text-sm font-bold transition-all"
                                                            style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border); color: var(--color-cinema-text-muted);"
                                                            data-value="${i + 1}">
                                                        ${i + 1}
                                                    </button>
                                                `).join('')}
                                            </div>
                                            <input type="hidden" id="review-rating" value="">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-[var(--color-cinema-text-muted)] mb-2">Bình luận</label>
                                            <textarea id="review-comment" rows="3" maxlength="1000"
                                                      class="w-full px-4 py-3 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]"
                                                      style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"
                                                      placeholder="Chia sẻ cảm nhận của bạn về phim..."></textarea>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="submit" id="submit-review-btn" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">Gửi đánh giá</button>
                                        </div>
                                    </form>
                                </div>
                            ` : `
                                <div class="glass rounded-xl p-4 mb-6 text-center">
                                    <p class="text-sm text-[var(--color-cinema-text-muted)]">Bạn cần đặt vé và xem phim này trước khi đánh giá</p>
                                </div>
                            `)
                        ) : `
                            <div class="glass rounded-xl p-4 mb-6 text-center">
                                <p class="text-sm text-[var(--color-cinema-text-muted)]">Vui lòng <a href="/login" class="font-semibold" style="color: var(--color-cinema-accent);">đăng nhập</a> để đánh giá phim</p>
                            </div>
                        `}

                        {{-- Reviews List --}}
                        <div id="reviews-list">
                            <div class="text-center py-8">
                                <div class="inline-block w-6 h-6 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>
                        <div id="reviews-load-more" class="hidden text-center mt-4">
                            <button onclick="loadMoreReviews()" class="text-sm px-6 py-2 rounded-lg transition-all hover:opacity-80"
                                    style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border); color: var(--color-cinema-text-muted);">
                                Xem thêm đánh giá
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // === Rating Selector ===
        let selectedRating = 0;
        function selectRating(value) {
            selectedRating = value;
            document.getElementById('review-rating').value = value;
            document.querySelectorAll('.rating-btn').forEach(btn => {
                const v = parseInt(btn.dataset.value);
                if (v <= value) {
                    btn.style.background = 'var(--color-cinema-accent)';
                    btn.style.color = '#000';
                    btn.style.borderColor = 'var(--color-cinema-accent)';
                } else {
                    btn.style.background = 'var(--color-cinema-card)';
                    btn.style.color = 'var(--color-cinema-text-muted)';
                    btn.style.borderColor = 'var(--color-cinema-border)';
                }
            });
        }

        // === Submit Review ===
        function submitReview(e) {
            e.preventDefault();
            const rating = document.getElementById('review-rating').value;
            const comment = document.getElementById('review-comment').value;

            if (!rating) {
                alert('Vui lòng chọn điểm đánh giá');
                return;
            }

            const btn = document.getElementById('submit-review-btn');
            btn.disabled = true;
            btn.textContent = 'Đang gửi...';

            fetch(`/api/movies/${movieId}/reviews`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN,
                },
                body: JSON.stringify({ rating: parseInt(rating), comment: comment || null }),
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    movieHasReviewed = true;
                    // Reload page to reflect changes
                    window.location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra');
                    btn.disabled = false;
                    btn.textContent = 'Gửi đánh giá';
                }
            })
            .catch(() => {
                alert('Có lỗi xảy ra, vui lòng thử lại');
                btn.disabled = false;
                btn.textContent = 'Gửi đánh giá';
            });
        }

        // === Load Reviews ===
        let reviewsPage = 1;
        let reviewsLastPage = 1;

        function loadReviews(id, page = 1) {
            fetch(`/api/movies/${id}/reviews?page=${page}`)
                .then(res => res.json())
                .then(result => {
                    const reviews = result.data || [];
                    reviewsLastPage = result.last_page || 1;
                    reviewsPage = result.current_page || 1;

                    const container = document.getElementById('reviews-list');
                    if (reviews.length === 0 && page === 1) {
                        container.innerHTML = '<p class="text-center text-[var(--color-cinema-text-muted)] py-8">Chưa có đánh giá nào cho phim này</p>';
                        return;
                    }

                    const html = reviews.map(review => renderReviewItem(review)).join('');

                    if (page === 1) {
                        container.innerHTML = html;
                    } else {
                        container.innerHTML += html;
                    }

                    // Show/hide load more button
                    const loadMoreBtn = document.getElementById('reviews-load-more');
                    if (reviewsPage < reviewsLastPage) {
                        loadMoreBtn.classList.remove('hidden');
                    } else {
                        loadMoreBtn.classList.add('hidden');
                    }
                });
        }

        function loadMoreReviews() {
            loadReviews(movieId, reviewsPage + 1);
        }

        function renderReviewItem(review) {
            const date = new Date(review.created_at);
            const dateStr = date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
            const user = review.user || {};
            const initial = (user.name || '?').charAt(0).toUpperCase();
            const avatarHtml = user.avatar
                ? `<img src="${user.avatar}" alt="${user.name}" class="w-10 h-10 rounded-full object-cover">`
                : `<div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white" style="background: linear-gradient(135deg, var(--color-cinema-primary), var(--color-cinema-accent));">${initial}</div>`;

            return `
                <div class="glass rounded-xl p-4 mb-3">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0">${avatarHtml}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-semibold text-white">${user.name || 'Ẩn danh'}</span>
                                <span class="text-xs text-[var(--color-cinema-text-muted)]">${dateStr}</span>
                            </div>
                            <div class="flex items-center gap-1 mb-2">
                                <span class="text-sm font-bold" style="color: var(--color-cinema-accent);">${review.rating}</span>
                                <span class="text-xs text-[var(--color-cinema-text-muted)]">/ 10</span>
                            </div>
                            ${review.comment ? `<p class="text-sm text-[var(--color-cinema-text-muted)] leading-relaxed">${review.comment}</p>` : ''}
                        </div>
                    </div>
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
</x-layouts.main>
