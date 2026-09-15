<x-layouts.app title="Thanh toán - CineStar">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <a href="javascript:history.back()" class="text-sm text-[var(--color-cinema-text-muted)] hover:text-white transition-colors">← Quay lại</a>
        <h1 class="text-2xl font-bold text-white mt-2 mb-6">💳 Thanh toán</h1>

        <div class="lg:flex gap-6">
            {{-- Order Summary --}}
            <div class="lg:flex-1">
                <div class="glass rounded-2xl p-6 mb-6" id="order-summary">
                    <div class="text-center py-12 text-[var(--color-cinema-text-muted)]">Đang tải thông tin đơn hàng...</div>
                </div>

                {{-- Voucher --}}
                <div class="glass rounded-2xl p-6 mb-6">
                    <h3 class="font-bold text-white mb-4">🎟️ Mã giảm giá</h3>
                    <div class="flex gap-3">
                        <input type="text" id="voucher-input" placeholder="Nhập mã voucher (VD: WELCOME50)"
                               class="flex-1 px-4 py-3 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-accent)]"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                        <button onclick="applyVoucher()" class="btn-accent !py-3 !px-6 text-sm">Áp dụng</button>
                    </div>
                    <p class="text-xs text-[var(--color-cinema-text-muted)] mt-2" id="voucher-status"></p>
                </div>

                {{-- Payment Method --}}
                <div class="glass rounded-2xl p-6">
                    <h3 class="font-bold text-white mb-4">💰 Phương thức thanh toán</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 rounded-xl cursor-pointer transition-all hover:bg-[var(--color-cinema-card)]" style="border: 2px solid var(--color-cinema-primary);">
                            <input type="radio" name="payment_method" value="vnpay" checked class="w-4 h-4">
                            <span class="text-2xl">🏦</span>
                            <div>
                                <span class="font-semibold text-white text-sm">VNPay</span>
                                <span class="text-xs text-[var(--color-cinema-text-muted)] block">Thanh toán qua ví VNPay (Giả lập)</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl cursor-pointer transition-all hover:bg-[var(--color-cinema-card)]" style="border: 2px solid var(--color-cinema-border);">
                            <input type="radio" name="payment_method" value="momo" class="w-4 h-4">
                            <span class="text-2xl">📱</span>
                            <div>
                                <span class="font-semibold text-white text-sm">MoMo</span>
                                <span class="text-xs text-[var(--color-cinema-text-muted)] block">Thanh toán qua ví MoMo (Giả lập)</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl cursor-pointer transition-all hover:bg-[var(--color-cinema-card)]" style="border: 2px solid var(--color-cinema-border);">
                            <input type="radio" name="payment_method" value="cash" class="w-4 h-4">
                            <span class="text-2xl">💵</span>
                            <div>
                                <span class="font-semibold text-white text-sm">Tiền mặt</span>
                                <span class="text-xs text-[var(--color-cinema-text-muted)] block">Thanh toán tại quầy</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Payment Sidebar --}}
            <div class="lg:w-80 mt-6 lg:mt-0">
                <div class="glass rounded-2xl p-6 sticky top-20">
                    {{-- Countdown --}}
                    <div class="countdown-timer rounded-xl p-3 text-center mb-4">
                        <span class="text-xs block">⏱️ Thời gian còn lại</span>
                        <span class="text-3xl font-bold" id="countdown-timer">10:00</span>
                    </div>

                    <div class="space-y-3 text-sm" id="payment-summary">
                        <p class="text-[var(--color-cinema-text-muted)] text-center">Đang tải...</p>
                    </div>

                    <button onclick="processPayment()" id="btn-pay" class="btn-primary w-full text-center py-3 mt-6 text-base font-bold">
                        💳 Thanh toán ngay
                    </button>
                    <p class="text-xs text-[var(--color-cinema-text-muted)] text-center mt-3">
                        Thanh toán giả lập — Không thu phí thật
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const orderId = {{ $orderId }};
        let orderData = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadOrderDetail();
        });

        function loadOrderDetail() {
            fetch(`/api/orders/${orderId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                orderData = result.data;
                renderOrderSummary();
                startCountdown();
            })
            .catch(err => console.error(err));
        }

        function renderOrderSummary() {
            const o = orderData;
            const startTime = new Date(o.show?.start_time);

            document.getElementById('order-summary').innerHTML = `
                <h3 class="font-bold text-white mb-4">📋 Tóm tắt đơn hàng</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Phim</span><span class="text-white font-medium">${o.show?.movie?.title || '—'}</span></div>
                    <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Suất chiếu</span><span class="text-white">${startTime.toLocaleDateString('vi-VN')} ${startTime.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'})}</span></div>
                    <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Phòng</span><span class="text-white">${o.show?.room?.name || '—'}</span></div>
                    <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Ghế</span><span class="text-[var(--color-cinema-accent)] font-semibold">${o.seats?.map(s => s.seat?.row_label + s.seat?.seat_number).join(', ') || '—'}</span></div>
                    ${o.snacks?.length > 0 ? `
                        <div class="border-t border-[var(--color-cinema-border)] my-2"></div>
                        <p class="text-[var(--color-cinema-text-muted)] font-medium">Bắp nước:</p>
                        ${o.snacks.map(s => `<div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">${s.snack?.name} x${s.quantity}</span><span class="text-white">${Number(s.price).toLocaleString('vi-VN')}đ</span></div>`).join('')}
                    ` : ''}
                </div>
            `;

            document.getElementById('payment-summary').innerHTML = `
                <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Ghế</span><span class="text-white">${o.seats?.reduce((t,s) => t + parseFloat(s.price), 0).toLocaleString('vi-VN')}đ</span></div>
                <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Bắp nước</span><span class="text-white">${o.snacks?.reduce((t,s) => t + parseFloat(s.price), 0).toLocaleString('vi-VN')}đ</span></div>
                ${o.voucher_id ? `<div class="flex justify-between text-green-400"><span>Giảm giá</span><span>-${o.discount || 0}đ</span></div>` : ''}
                <div class="border-t border-[var(--color-cinema-border)] my-2"></div>
                <div class="flex justify-between text-lg font-bold"><span class="text-white">Tổng cộng</span><span class="text-[var(--color-cinema-accent)]">${Number(o.total_amount).toLocaleString('vi-VN')}đ</span></div>
            `;
        }

        function startCountdown() {
            if (!orderData?.hold_expires_at) return;
            const expiry = new Date(orderData.hold_expires_at);

            countdownInterval = setInterval(() => {
                const now = new Date();
                const diff = expiry - now;

                if (diff <= 0) {
                    clearInterval(countdownInterval);
                    document.getElementById('countdown-timer').textContent = 'HẾT GIỜ';
                    document.getElementById('btn-pay').disabled = true;
                    alert('Thời gian giữ ghế đã hết! Bạn sẽ được chuyển về trang chủ.');
                    window.location.href = '/';
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
                    status.innerHTML = `<span class="text-green-400">✅ ${data.message} (Giảm ${Number(data.data.discount).toLocaleString('vi-VN')}đ)</span>`;
                    loadOrderDetail();
                } else {
                    status.innerHTML = `<span class="text-red-400">❌ ${data.message}</span>`;
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
                    btn.textContent = '💳 Thanh toán ngay';
                }
            });
        }

        let countdownInterval;
    </script>
</x-layouts.app>
