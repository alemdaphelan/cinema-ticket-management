<x-layouts.main title="Chọn ghế - CineStar">
    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="javascript:history.back()" class="text-sm text-[var(--color-cinema-text-muted)] hover:text-white transition-colors">← Quay lại</a>
                <h1 class="text-2xl font-bold text-white mt-2" id="show-title">Chọn ghế</h1>
                <p class="text-sm text-[var(--color-cinema-text-muted)]" id="show-info"></p>
            </div>
            {{-- Countdown Timer --}}
            <div id="countdown-container" class="hidden">
                <div class="countdown-timer text-white px-4 py-2 rounded-xl text-center">
                    <span class="text-xs block">Thời gian giữ ghế</span>
                    <span class="text-2xl font-bold" id="countdown-timer">10:00</span>
                </div>
            </div>
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
        let showData = null;
        let seatsData = [];
        let selectedSeats = [];
        let basePrice = 0;
        let pollInterval = null;
        let countdownInterval = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadSeatMap();
            // Polling every 5 seconds
            pollInterval = setInterval(loadSeatMap, 5000);
        });

        function loadSeatMap() {
            fetch(`/api/shows/${showId}/seats`)
                .then(res => res.json())
                .then(result => {
                    showData = result.data.show;
                    seatsData = result.data.seats;
                    basePrice = parseFloat(showData.price);

                    // Update header
                    document.getElementById('show-title').textContent = showData.movie?.title || 'Chọn ghế';
                    const startTime = new Date(showData.start_time);
                    document.getElementById('show-info').textContent = `${startTime.toLocaleDateString('vi-VN')} • ${startTime.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})} • ${showData.room?.name || ''}`;

                    // Update summary
                    document.getElementById('summary-movie').textContent = showData.movie?.title || '—';
                    document.getElementById('summary-time').textContent = startTime.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
                    document.getElementById('summary-room').textContent = showData.room?.name || '—';

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
                    alert('Bạn chỉ được chọn tối đa 8 ghế!');
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
                alert(err.message);
                btn.disabled = false;
                btn.textContent = 'Giữ ghế & Tiếp tục';
                loadSeatMap();
            });
        }
    </script>
</x-layouts.main>
