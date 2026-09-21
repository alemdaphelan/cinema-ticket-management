<x-layouts.main title="Thanh toán - CineStar">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <a href="javascript:history.back()" class="text-sm text-[var(--color-cinema-text-muted)] hover:text-white transition-colors">← Quay lại</a>
        <h1 class="text-2xl font-bold text-white mt-2 mb-6">Thanh toán</h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            {{-- Left Column: Order Summary --}}
            <div class="glass rounded-2xl p-6 h-fit" id="order-summary-container">
                <h3 class="font-bold text-white mb-4 text-lg border-b border-[var(--color-cinema-border)] pb-3">Thông tin đặt vé</h3>
                <div id="order-summary-loading" class="animate-pulse space-y-5 py-4">
                    <div class="flex justify-between items-center"><div class="h-4 bg-[var(--color-cinema-border)] rounded w-16 opacity-30"></div><div class="h-5 bg-[var(--color-cinema-border)] rounded w-48 opacity-30"></div></div>
                    <div class="flex justify-between items-center"><div class="h-4 bg-[var(--color-cinema-border)] rounded w-24 opacity-30"></div><div class="h-4 bg-[var(--color-cinema-border)] rounded w-32 opacity-30"></div></div>
                    <div class="flex justify-between items-center"><div class="h-4 bg-[var(--color-cinema-border)] rounded w-24 opacity-30"></div><div class="h-4 bg-[var(--color-cinema-border)] rounded w-32 opacity-30"></div></div>
                    <div class="flex justify-between items-center"><div class="h-4 bg-[var(--color-cinema-border)] rounded w-16 opacity-30"></div><div class="h-4 bg-[var(--color-cinema-border)] rounded w-24 opacity-30"></div></div>
                    <div class="flex justify-between items-center"><div class="h-4 bg-[var(--color-cinema-border)] rounded w-28 opacity-30"></div><div class="h-4 bg-[var(--color-cinema-border)] rounded w-40 opacity-30"></div></div>
                </div>
                <div id="order-summary-content" class="hidden space-y-4 text-sm mt-4">
                    <div class="flex justify-between items-center"><span class="text-[var(--color-cinema-text-muted)]">Phim</span><span class="text-white font-bold text-base text-right" id="os-movie">—</span></div>
                    <div class="flex justify-between items-center"><span class="text-[var(--color-cinema-text-muted)]">Suất chiếu</span><span class="text-white text-right" id="os-time">—</span></div>
                    <div class="flex justify-between items-center"><span class="text-[var(--color-cinema-text-muted)]">Ngày chiếu</span><span class="text-white text-right" id="os-date">—</span></div>
                    <div class="flex justify-between items-center"><span class="text-[var(--color-cinema-text-muted)]">Phòng</span><span class="text-white text-right" id="os-room">—</span></div>
                    <div class="flex justify-between items-start"><span class="text-[var(--color-cinema-text-muted)]">Ghế đã chọn</span><span class="text-[var(--color-cinema-accent)] font-semibold text-right max-w-[60%]" id="os-seats">—</span></div>
                    
                    <div id="os-snacks-container" class="hidden">
                        <div class="border-t border-[var(--color-cinema-border)] my-3"></div>
                        <p class="text-[var(--color-cinema-text-muted)] mb-2">Bắp nước:</p>
                        <div id="os-snacks-list" class="space-y-2"></div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Voucher & Payment Methods --}}
            <div class="space-y-6">
                {{-- Voucher --}}
                <div class="glass rounded-2xl p-6">
                    <h3 class="font-bold text-white mb-4 text-lg border-b border-[var(--color-cinema-border)] pb-3">Mã giảm giá</h3>
                    <div class="flex gap-3 mt-4">
                        <input type="text" id="voucher-input" placeholder="Nhập mã voucher (VD: WELCOME50)"
                               class="flex-1 px-4 py-3 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-accent)]"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                        <button onclick="applyVoucher()" class="btn-accent !py-3 !px-6 text-sm">Áp dụng</button>
                    </div>
                    <p class="text-xs text-[var(--color-cinema-text-muted)] mt-2" id="voucher-status"></p>
                </div>

                {{-- Payment Method --}}
                <div class="glass rounded-2xl p-6">
                    <h3 class="font-bold text-white mb-4 text-lg border-b border-[var(--color-cinema-border)] pb-3">Phương thức thanh toán</h3>
                    <div class="space-y-3 mt-4">
                        <label class="payment-method-label flex items-center gap-3 p-4 rounded-xl cursor-pointer transition-all hover:bg-[var(--color-cinema-card)] border-2 border-[var(--color-cinema-primary)]">
                            <input type="radio" name="payment_method" value="vnpay" checked class="w-4 h-4 hidden" onchange="updatePaymentUI()">
                            <div class="w-5 h-5 rounded-full border-2 border-[var(--color-cinema-primary)] flex items-center justify-center mr-2 radio-indicator">
                                <div class="w-2.5 h-2.5 rounded-full bg-[var(--color-cinema-primary)]"></div>
                            </div>
                            <div>
                                <span class="font-semibold text-white text-sm">VNPay</span>
                                <span class="text-xs text-[var(--color-cinema-text-muted)] block">Thanh toán qua ví VNPay</span>
                            </div>
                        </label>
                        <label class="payment-method-label flex items-center gap-3 p-4 rounded-xl cursor-pointer transition-all hover:bg-[var(--color-cinema-card)] border-2 border-[var(--color-cinema-border)]">
                            <input type="radio" name="payment_method" value="momo" class="w-4 h-4 hidden" onchange="updatePaymentUI()">
                            <div class="w-5 h-5 rounded-full border-2 border-[var(--color-cinema-border)] flex items-center justify-center mr-2 radio-indicator">
                                <div class="w-2.5 h-2.5 rounded-full bg-transparent"></div>
                            </div>
                            <div>
                                <span class="font-semibold text-white text-sm">MoMo</span>
                                <span class="text-xs text-[var(--color-cinema-text-muted)] block">Thanh toán qua ví MoMo</span>
                            </div>
                        </label>
                        <label class="payment-method-label flex items-center gap-3 p-4 rounded-xl cursor-pointer transition-all hover:bg-[var(--color-cinema-card)] border-2 border-[var(--color-cinema-border)]">
                            <input type="radio" name="payment_method" value="cash" class="w-4 h-4 hidden" onchange="updatePaymentUI()">
                            <div class="w-5 h-5 rounded-full border-2 border-[var(--color-cinema-border)] flex items-center justify-center mr-2 radio-indicator">
                                <div class="w-2.5 h-2.5 rounded-full bg-transparent"></div>
                            </div>
                            <div>
                                <span class="font-semibold text-white text-sm">Tiền mặt</span>
                                <span class="text-xs text-[var(--color-cinema-text-muted)] block">Thanh toán tại quầy</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Bar: Total and Checkout --}}
        <div class="glass rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-6 sticky bottom-4 z-40 border border-[var(--color-cinema-primary)] shadow-[0_0_20px_rgba(229,9,20,0.2)]">
            <div class="flex items-center gap-6">
                <div class="text-center md:text-left">
                    <span class="text-xs text-[var(--color-cinema-text-muted)] block uppercase tracking-wider">Thời gian còn lại</span>
                    <span class="text-3xl font-bold text-white font-mono" id="countdown-timer">10:00</span>
                </div>
                <div class="h-12 w-px bg-[var(--color-cinema-border)] hidden md:block"></div>
                <div id="payment-summary-bottom" class="text-sm space-y-1 hidden md:block">
                    <!-- Loaded via JS -->
                </div>
            </div>
            
            <div class="flex items-center gap-6 w-full md:w-auto">
                <div class="text-center md:text-right flex-1 md:flex-none">
                    <span class="text-xs text-[var(--color-cinema-text-muted)] block uppercase tracking-wider">Tổng cộng</span>
                    <span class="text-2xl md:text-3xl font-bold text-[var(--color-cinema-accent)]" id="bottom-total-price">0đ</span>
                </div>
                <button onclick="processPayment()" id="btn-pay" class="btn-primary py-4 px-8 text-lg font-bold min-w-[200px] whitespace-nowrap shadow-lg shadow-red-500/30">
                    Thanh toán ngay
                </button>
            </div>
        </div>
    </div>

    {{-- Timeout Modal --}}
    <div id="timeout-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/80 backdrop-blur-sm opacity-0 transition-opacity duration-300">
        <div class="glass p-8 rounded-2xl max-w-sm w-full text-center transform scale-95 transition-transform duration-300" style="background: linear-gradient(135deg, rgba(15,15,35,0.95), rgba(229,9,20,0.1)); border: 1px solid rgba(229,9,20,0.3);">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center bg-red-500/20 text-red-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Hết thời gian giữ ghế</h3>
            <p class="text-[var(--color-cinema-text-muted)] mb-6">Đơn hàng của bạn đã bị hủy do quá thời gian thanh toán.</p>
            <button onclick="window.location.href='/'" class="btn-primary w-full py-3">Quay về trang chủ</button>
        </div>
    </div>

    <script>
        const orderId = {{ $orderId }};
        let orderData = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadOrderDetail();
            updatePaymentUI(); // Fix viền đỏ không hiện lúc đầu
        });

        function updatePaymentUI() {
            const labels = document.querySelectorAll('.payment-method-label');
            const radios = document.querySelectorAll('input[name="payment_method"]');
            
            labels.forEach((label, index) => {
                const radio = radios[index];
                const indicator = label.querySelector('.radio-indicator');
                const innerDot = indicator.querySelector('div');
                
                if (radio.checked) {
                    label.classList.remove('border-[var(--color-cinema-border)]');
                    label.classList.add('border-[var(--color-cinema-primary)]');
                    indicator.classList.remove('border-[var(--color-cinema-border)]');
                    indicator.classList.add('border-[var(--color-cinema-primary)]');
                    innerDot.classList.remove('bg-transparent');
                    innerDot.classList.add('bg-[var(--color-cinema-primary)]');
                } else {
                    label.classList.remove('border-[var(--color-cinema-primary)]');
                    label.classList.add('border-[var(--color-cinema-border)]');
                    indicator.classList.remove('border-[var(--color-cinema-primary)]');
                    indicator.classList.add('border-[var(--color-cinema-border)]');
                    innerDot.classList.remove('bg-[var(--color-cinema-primary)]');
                    innerDot.classList.add('bg-transparent');
                }
            });
        }

        function loadOrderDetail() {
            fetch(`/api/orders/${orderId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                orderData = result.data;
                renderOrderSummary();
                if(!countdownInterval) startCountdown();
            })
            .catch(err => console.error(err));
        }

        function renderOrderSummary() {
            const o = orderData;
            document.getElementById('order-summary-loading').classList.add('hidden');
            document.getElementById('order-summary-content').classList.remove('hidden');

            const startTime = new Date(o.show?.start_time);
            // Assuming default duration of 120 mins if not provided
            const durationMins = o.show?.movie?.duration_minutes || 120;
            const endTime = new Date(startTime.getTime() + durationMins * 60000);

            const startTimeStr = startTime.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'});
            const endTimeStr = endTime.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'});
            const dateStr = startTime.toLocaleDateString('vi-VN', {weekday: 'long', year: 'numeric', month: '2-digit', day: '2-digit'});

            document.getElementById('os-movie').textContent = o.show?.movie?.title || 'Đang cập nhật';
            document.getElementById('os-time').textContent = `${startTimeStr} - ${endTimeStr}`;
            document.getElementById('os-date').textContent = dateStr;
            document.getElementById('os-room').textContent = o.show?.room?.name || '—';
            document.getElementById('os-seats').textContent = o.seats?.map(s => s.seat?.row_label + s.seat?.seat_number).join(', ') || '—';

            if (o.snacks?.length > 0) {
                document.getElementById('os-snacks-container').classList.remove('hidden');
                document.getElementById('os-snacks-list').innerHTML = o.snacks.map(s => `
                    <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">${s.snack?.name} x${s.quantity}</span><span class="text-white">${Number(s.price).toLocaleString('vi-VN')}đ</span></div>
                `).join('');
            } else {
                document.getElementById('os-snacks-container').classList.add('hidden');
            }

            const seatsTotal = o.seats?.reduce((t,s) => t + parseFloat(s.price), 0) || 0;
            const snacksTotal = o.snacks?.reduce((t,s) => t + parseFloat(s.price), 0) || 0;
            const discount = o.voucher_id ? (seatsTotal + snacksTotal - parseFloat(o.total_amount)) : 0;

            document.getElementById('payment-summary-bottom').innerHTML = `
                <div class="flex justify-between gap-8"><span class="text-[var(--color-cinema-text-muted)]">Tiền vé</span><span class="text-white">${seatsTotal.toLocaleString('vi-VN')}đ</span></div>
                <div class="flex justify-between gap-8"><span class="text-[var(--color-cinema-text-muted)]">Bắp nước</span><span class="text-white">${snacksTotal.toLocaleString('vi-VN')}đ</span></div>
                ${o.voucher_id ? `<div class="flex justify-between gap-8 text-green-400"><span>Giảm giá</span><span>-${discount.toLocaleString('vi-VN')}đ</span></div>` : ''}
            `;

            document.getElementById('bottom-total-price').textContent = `${Number(o.total_amount).toLocaleString('vi-VN')}đ`;
        }

        let countdownInterval;
        function startCountdown() {
            if (!orderData?.hold_expires_at) return;
            const expiry = new Date(orderData.hold_expires_at);

            countdownInterval = setInterval(() => {
                const now = new Date();
                const diff = expiry - now;

                if (diff <= 0) {
                    clearInterval(countdownInterval);
                    document.getElementById('countdown-timer').textContent = '00:00';
                    document.getElementById('btn-pay').disabled = true;
                    
                    // Show modal
                    const modal = document.getElementById('timeout-modal');
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        modal.firstElementChild.classList.remove('scale-95');
                    }, 10);
                    return;
                }

                const mins = Math.floor(diff / 60000);
                const secs = Math.floor((diff % 60000) / 1000);
                document.getElementById('countdown-timer').textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }, 1000);
        }

        function applyVoucher() {
            const code = document.getElementById('voucher-input').value.trim();
            if (!code) return;

            fetch(`/api/orders/${orderId}/voucher`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ voucher_code: code }),
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                const status = document.getElementById('voucher-status');
                if (ok) {
                    status.innerHTML = `<span class="text-green-400">Thành công: ${data.message} (Giảm ${Number(data.data.discount).toLocaleString('vi-VN')}đ)</span>`;
                    loadOrderDetail();
                } else {
                    status.innerHTML = `<span class="text-red-400">Lỗi: ${data.message}</span>`;
                }
            });
        }

        function processPayment() {
            const method = document.querySelector('input[name="payment_method"]:checked').value;
            const btn = document.getElementById('btn-pay');
            btn.disabled = true;
            btn.textContent = 'Đang xử lý...';

            fetch(`/api/orders/${orderId}/pay`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ payment_method: method }),
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    clearInterval(countdownInterval);
                    window.location.href = `/booking/success/${orderId}`;
                } else {
                    alert(data.message || 'Thanh toán thất bại!');
                    btn.disabled = false;
                    btn.textContent = 'Thanh toán ngay';
                }
            });
        }
    </script>
</x-layouts.main>
