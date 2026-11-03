<x-layouts.main title="Chọn ghế - CineStar">
    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Booking Breadcrumb Stepper --}}
        <x-booking-breadcrumb :step="1" :showId="$showId" :orderId="$orderId ?? null" :holdExpiresAt="$holdExpiresAt ?? null" />

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="/schedule" class="text-sm text-[var(--color-cinema-text-muted)] hover:text-white transition-colors">← Chọn suất chiếu khác</a>
                <h1 class="text-2xl font-bold text-white mt-2" id="show-title">Chọn ghế</h1>
                <p class="text-sm text-[var(--color-cinema-text-muted)]" id="show-info"></p>
            </div>
            @if(isset($holdExpiresAt) && $holdExpiresAt)
                {{-- Countdown Timer if user already held seats and returned to step 1 --}}
                <div id="countdown-container">
                    <div class="countdown-timer text-white px-4 py-2 rounded-xl text-center border border-red-500/30 bg-red-500/10">
                        <span class="text-xs block text-[var(--color-cinema-text-muted)]">Thời gian giữ ghế</span>
                        <span class="text-2xl font-bold font-mono text-red-400" id="countdown-timer">--:--</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:flex gap-6">
            {{-- Seat Map --}}
            <div class="lg:flex-1">
                <div class="glass rounded-2xl p-6">
                    {{-- Screen --}}
                    <div class="screen">MÀN HÌNH</div>

                    {{-- Seats Grid --}}
                    <div id="seats-container" class="flex flex-col items-center gap-2 overflow-x-auto pb-4">
                        <div class="text-center py-12 text-[var(--color-cinema-text-muted)]">
                            <div class="inline-block w-6 h-6 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin"></div>
                            <p class="mt-2 text-sm">Đang tải sơ đồ ghế...</p>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="flex flex-wrap justify-center gap-4 mt-6 pt-4 border-t border-[var(--color-cinema-border)]">
                        <div class="flex items-center gap-2 text-xs text-[var(--color-cinema-text-muted)]">
                            <div class="w-5 h-5 rounded seat-available"></div> Trống
                        </div>
                        <div class="flex items-center gap-2 text-xs text-[var(--color-cinema-text-muted)]">
                            <div class="w-5 h-5 rounded seat-vip" style="border: 2px solid var(--color-seat-vip);"></div> VIP
                        </div>
                        <div class="flex items-center gap-2 text-xs text-[var(--color-cinema-text-muted)]">
                            <div class="w-5 h-5 rounded seat-selected"></div> Đang chọn
                        </div>
                        <div class="flex items-center gap-2 text-xs text-[var(--color-cinema-text-muted)]">
                            <div class="w-5 h-5 rounded seat-booked"></div> Đã đặt
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:w-80 mt-6 lg:mt-0">
                <div class="glass rounded-2xl p-6 sticky top-20">
                    <h3 class="font-bold text-white mb-4">Thông tin đặt vé</h3>

                    <div id="booking-summary">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-[var(--color-cinema-text-muted)]">
                                <span>Phim</span>
                                <span class="text-white font-medium" id="summary-movie">—</span>
                            </div>
                            <div class="flex justify-between text-[var(--color-cinema-text-muted)]">
                                <span>Suất chiếu</span>
                                <span class="text-white font-medium" id="summary-time">—</span>
                            </div>
                            <div class="flex justify-between text-[var(--color-cinema-text-muted)]">
                                <span>Phòng</span>
                                <span class="text-white font-medium" id="summary-room">—</span>
                            </div>

                            <div class="border-t border-[var(--color-cinema-border)] my-3"></div>

                            <div class="flex justify-between text-[var(--color-cinema-text-muted)]">
                                <span>Ghế đã chọn</span>
                                <span class="text-[var(--color-cinema-accent)] font-semibold" id="summary-seats">Chưa chọn</span>
                            </div>
                            <div class="flex justify-between text-[var(--color-cinema-text-muted)]">
                                <span>Số lượng</span>
                                <span class="text-white font-medium" id="summary-count">0</span>
                            </div>

                            <div class="border-t border-[var(--color-cinema-border)] my-3"></div>

                            <div class="flex justify-between text-lg font-bold">
                                <span class="text-white">Tổng tiền</span>
                                <span class="text-[var(--color-cinema-accent)]" id="summary-total">0đ</span>
                            </div>
                        </div>
                    </div>

                    <button id="btn-hold-seats" onclick="holdSeats()" disabled
                            class="btn-primary w-full text-center py-3 mt-6 text-sm">
                        Giữ ghế & Tiếp tục
                    </button>
                    <p class="text-xs text-[var(--color-cinema-text-muted)] text-center mt-3">
                        Ghế sẽ được giữ trong 10 phút
                    </p>
                </div>
            </div>
        </div>
    </div>


    <script>
        const showId = {{ $showId }};
        const existingOrderId = {{ $orderId ? $orderId : 'null' }};
        const initialHoldExpiresAt = @json($holdExpiresAt ?? null);
        let showData = null;
        let seatsData = [];
        let selectedSeats = [];
        let isInitialLoad = true;
        let basePrice = 0;
        let pollInterval = null;
        let countdownInterval = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadSeatMap();
            // Polling every 5 seconds
            pollInterval = setInterval(loadSeatMap, 5000);

            if (initialHoldExpiresAt) {
                startSeatCountdown(initialHoldExpiresAt);
            }
        });

        function startSeatCountdown(expiresAtStr) {
            const expiry = new Date(expiresAtStr);
            const timerEl = document.getElementById('countdown-timer');

            function tick() {
                if (!timerEl) return;
                const now = new Date();
                const diff = expiry - now;

                if (diff <= 0) {
                    clearInterval(countdownInterval);
                    timerEl.textContent = '00:00';
                    if (window.showToast) window.showToast('Hết thời gian giữ ghế. Vui lòng chọn lại!', 'error');
                    loadSeatMap();
                    return;
                }

                const mins = Math.floor(diff / 60000);
                const secs = Math.floor((diff % 60000) / 1000);
                timerEl.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }

            tick();
            countdownInterval = setInterval(tick, 1000);
        }

        function loadSeatMap() {
            fetch(`/api/shows/${showId}/seats`)
                .then(res => res.json())
                .then(result => {
                    showData = result.data.show;
                    seatsData = result.data.seats;
                    basePrice = parseFloat(showData.price);

                    // Khi người dùng quay lại trang chọn ghế từ bước sau, tự động phục hồi các ghế họ đã chọn
                    if (isInitialLoad) {
                        isInitialLoad = false;
                        const userHeldIds = result.data.user_held_seat_ids || [];
                        if (userHeldIds.length > 0 && selectedSeats.length === 0) {
                            seatsData.forEach(seat => {
                                if (userHeldIds.includes(seat.id)) {
                                    selectedSeats.push({
                                        id: seat.id,
                                        row: seat.row_label,
                                        number: seat.seat_number,
                                        type: seat.type
                                    });
                                }
                            });
                            updateSummary();
                        }
                    }

                    // Update header
                    document.getElementById('show-title').textContent = showData.movie?.title || 'Chọn ghế';
                    const startTime = new Date(showData.start_time);
                    const durationMins = showData.movie?.duration_minutes || 120;
                    const endTime = showData.end_time ? new Date(showData.end_time) : new Date(startTime.getTime() + durationMins * 60000);
                    const startStr = startTime.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
                    const endStr = endTime.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});

                    document.getElementById('show-info').textContent = `${startTime.toLocaleDateString('vi-VN')} • ${startStr} ~ ${endStr} • ${showData.room?.name || ''}`;

                    // Update summary
                    document.getElementById('summary-movie').textContent = showData.movie?.title || '—';
                    document.getElementById('summary-time').textContent = `${startStr} ~ ${endStr}`;
                    document.getElementById('summary-room').textContent = showData.room?.name || '—';

                    // Loại bỏ khỏi selectedSeats những ghế mà trong đợt cập nhật này đã bị người khác đặt/giữ
                    if (!isInitialLoad) {
                        const previousCount = selectedSeats.length;
                        selectedSeats = selectedSeats.filter(sel => {
                            const seat = seatsData.find(s => s.id === sel.id);
                            return seat && seat.status === 'available';
                        });

                        if (selectedSeats.length < previousCount) {
                            if (window.showToast) {
                                window.showToast('Một ghế bạn đang xem vừa được người khác giữ. Sơ đồ ghế đã được cập nhật!', 'warning');
                            }
                            updateSummary();
                        }
                    }

                    renderSeats();
                })
                .catch(err => console.error('Error loading seats:', err));
        }

        function renderSeats() {
            const container = document.getElementById('seats-container');
            const rows = {};

            seatsData.forEach(seat => {
                if (!rows[seat.row_label]) rows[seat.row_label] = [];
                rows[seat.row_label].push(seat);
            });

            let html = '';
            for (const [rowLabel, seats] of Object.entries(rows)) {
                html += `<div class="flex items-center gap-1.5">`;
                html += `<span class="w-6 text-xs text-[var(--color-cinema-text-muted)] text-center font-bold">${rowLabel}</span>`;

                seats.sort((a, b) => a.seat_number - b.seat_number);
                seats.forEach(seat => {
                    const isSelected = selectedSeats.find(s => s.id === seat.id);
                    let seatClass = 'seat';

                    if (seat.status === 'booked') {
                        seatClass += ' seat-booked';
                    } else if (isSelected) {
                        seatClass += ' seat-selected';
                    } else if (seat.type === 'vip') {
                        seatClass += ' seat-vip';
                    } else {
                        seatClass += ' seat-available';
                    }

                    const clickable = seat.status === 'available';
                    html += `<div class="${seatClass}" ${clickable ? `onclick="toggleSeat(${seat.id}, '${seat.row_label}', ${seat.seat_number}, '${seat.type}')"` : ''} title="${seat.row_label}${seat.seat_number} ${seat.type === 'vip' ? '(VIP)' : ''}">
                        ${seat.seat_number}
                    </div>`;
                });

                html += `<span class="w-6 text-xs text-[var(--color-cinema-text-muted)] text-center font-bold">${rowLabel}</span>`;
                html += `</div>`;
            }

            container.innerHTML = html;
        }

        function toggleSeat(seatId, rowLabel, seatNumber, type) {
            const index = selectedSeats.findIndex(s => s.id === seatId);
            if (index > -1) {
                selectedSeats.splice(index, 1);
            } else {
                if (selectedSeats.length >= 8) {
                    showNotificationModal({
                        title: 'Giới hạn số ghế',
                        message: 'Bạn chỉ được chọn tối đa 8 ghế trong một lần đặt vé!',
                        type: 'warning'
                    });
                    return;
                }
                selectedSeats.push({ id: seatId, row: rowLabel, number: seatNumber, type: type });
            }

            renderSeats();
            updateSummary();
        }

        function updateSummary() {
            const seatsLabel = selectedSeats.length > 0
                ? selectedSeats.map(s => `${s.row}${s.number}`).join(', ')
                : 'Chưa chọn';

            let total = 0;
            selectedSeats.forEach(s => {
                total += s.type === 'vip' ? basePrice * 1.5 : basePrice;
            });

            document.getElementById('summary-seats').textContent = seatsLabel;
            document.getElementById('summary-count').textContent = selectedSeats.length;
            document.getElementById('summary-total').textContent = total.toLocaleString('vi-VN') + 'đ';
            document.getElementById('btn-hold-seats').disabled = selectedSeats.length === 0;
        }

        function holdSeats() {
            if (selectedSeats.length === 0) return;

            const btn = document.getElementById('btn-hold-seats');
            btn.disabled = true;
            btn.textContent = 'Đang xử lý...';

            fetch('/api/orders/hold', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    show_id: showId,
                    seat_ids: selectedSeats.map(s => s.id),
                }),
            })
            .then(res => {
                if (!res.ok) {
                    if (res.status === 401) {
                        window.location.href = '/login';
                        return;
                    }
                    return res.json().then(data => { throw new Error(data.message || 'Lỗi!'); });
                }
                return res.json();
            })
            .then(result => {
                if (!result) return;
                // Stop polling
                clearInterval(pollInterval);
                // Redirect to snacks page
                window.location.href = `/booking/snacks/${result.data.order_id}`;
            })
            .catch(err => {
                if (window.showNotificationModal) {
                    window.showNotificationModal({
                        title: 'Không thể giữ ghế',
                        message: err.message,
                        type: 'error'
                    });
                } else {
                    alert(err.message);
                }
                btn.disabled = false;
                btn.textContent = 'Giữ ghế & Tiếp tục';
                loadSeatMap();
            });
        }

        function releaseActiveOrder(orderId) {
            if (typeof window.showConfirmModal === 'function') {
                window.showConfirmModal({
                    title: 'Hủy ghế đang giữ để chọn lại?',
                    message: 'Các ghế bạn đang giữ ở đơn hàng này sẽ được giải phóng ngay lập tức để bạn có thể chọn lại ghế mới.',
                    type: 'warning',
                    confirmText: 'Giải phóng ghế',
                    cancelText: 'Tiếp tục giữ',
                    onConfirm: function() {
                        doReleaseActiveOrder(orderId);
                    }
                });
            } else {
                if (confirm('Bạn có chắc muốn hủy ghế đang giữ để chọn lại?')) {
                    doReleaseActiveOrder(orderId);
                }
            }
        }

        function doReleaseActiveOrder(orderId) {
            if (window.showToast) window.showToast('Đang giải phóng ghế...', 'info');

            fetch(`/api/orders/${orderId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ reason: 'change_seats' })
            })
            .then(res => res.json())
            .then(() => {
                const banner = document.getElementById('active-order-banner');
                if (banner) banner.remove();
                if (window.showToast) window.showToast('Đã giải phóng ghế. Bạn có thể chọn ghế mới!', 'success');
                loadSeatMap();
            })
            .catch(err => {
                console.error(err);
                loadSeatMap();
            });
        }
    </script>
</x-layouts.main>
