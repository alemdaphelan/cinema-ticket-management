<x-layouts.main title="Chọn bắp nước - CineStar">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <a href="javascript:history.back()" class="text-sm text-[var(--color-cinema-text-muted)] hover:text-white transition-colors">← Quay lại</a>
        <h1 class="text-2xl font-bold text-white mt-2 mb-6">Chọn bắp nước & combo</h1>

        <div class="lg:flex gap-6">
            <div class="lg:flex-1">
                <div id="snacks-list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="col-span-full text-center py-12 text-[var(--color-cinema-text-muted)]">Đang tải...</div>
                </div>
            </div>

            <div class="lg:w-80 mt-6 lg:mt-0">
                <div class="glass rounded-2xl p-6 sticky top-20">
                    <h3 class="font-bold text-white mb-4">Giỏ hàng</h3>
                    <div id="cart-items" class="space-y-2 text-sm"></div>
                    <div class="border-t border-[var(--color-cinema-border)] my-4"></div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Tổng bắp nước</span>
                        <span class="text-[var(--color-cinema-accent)]" id="snack-total">0đ</span>
                    </div>

                    <button onclick="submitSnacks()" class="btn-primary w-full text-center py-3 mt-4 text-sm">
                        Tiếp tục thanh toán
                    </button>
                    <a href="/booking/payment/{{ $orderId }}" class="block text-center text-sm text-[var(--color-cinema-text-muted)] mt-3 hover:text-white transition-colors">
                        Bỏ qua →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const orderId = {{ $orderId }};
        let snacks = [];
        let cart = {};

        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/snacks')
                .then(res => res.json())
                .then(result => {
                    snacks = result.data || [];
                    renderSnacks();
                });
        });

        function renderSnacks() {
            const container = document.getElementById('snacks-list');
            container.innerHTML = snacks.map(snack => `
                <div class="glass rounded-xl p-4 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-lg flex items-center justify-center text-3xl" style="background: var(--color-cinema-card);">
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-sm text-white">${snack.name}</h4>
                        <p class="text-[var(--color-cinema-accent)] font-bold text-sm mt-1">${Number(snack.price).toLocaleString('vi-VN')}đ</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="changeQty(${snack.id}, -1)" class="w-8 h-8 rounded-lg flex items-center justify-center text-lg font-bold transition-all hover:bg-[var(--color-cinema-primary)]"
                                style="background: var(--color-cinema-card);">−</button>
                        <span class="w-8 text-center font-bold" id="qty-${snack.id}">${cart[snack.id] || 0}</span>
                        <button onclick="changeQty(${snack.id}, 1)" class="w-8 h-8 rounded-lg flex items-center justify-center text-lg font-bold transition-all hover:bg-[var(--color-cinema-primary)]"
                                style="background: var(--color-cinema-card);">+</button>
                    </div>
                </div>
            `).join('');
        }

        function changeQty(snackId, delta) {
            cart[snackId] = Math.max(0, (cart[snackId] || 0) + delta);
            document.getElementById(`qty-${snackId}`).textContent = cart[snackId];
            updateCart();
        }

        function updateCart() {
            const cartDiv = document.getElementById('cart-items');
            let total = 0;
            let items = [];

            for (const [id, qty] of Object.entries(cart)) {
                if (qty > 0) {
                    const snack = snacks.find(s => s.id == id);
                    if (snack) {
                        const subtotal = snack.price * qty;
                        total += subtotal;
                        items.push(`<div class="flex justify-between text-[var(--color-cinema-text-muted)]"><span>${snack.name} x${qty}</span><span class="text-white">${subtotal.toLocaleString('vi-VN')}đ</span></div>`);
                    }
                }
            }

            cartDiv.innerHTML = items.length > 0 ? items.join('') : '<p class="text-[var(--color-cinema-text-muted)] text-center">Chưa chọn gì</p>';
            document.getElementById('snack-total').textContent = total.toLocaleString('vi-VN') + 'đ';
        }

        let isSubmitted = false;

        window.addEventListener('pagehide', function() {
            if (!isSubmitted && orderId) {
                fetch(`/api/orders/${orderId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    keepalive: true
                });
            }
        });

        function submitSnacks() {
            const snackItems = [];
            for (const [id, qty] of Object.entries(cart)) {
                if (qty > 0) {
                    snackItems.push({ snack_id: parseInt(id), quantity: qty });
                }
            }

            if (snackItems.length === 0) {
                isSubmitted = true;
                window.location.href = `/booking/payment/${orderId}`;
                return;
            }

            fetch(`/api/orders/${orderId}/snacks`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ snacks: snackItems }),
            })
            .then(res => res.json())
            .then(() => {
                isSubmitted = true;
                window.location.href = `/booking/payment/${orderId}`;
            })
            .catch(err => alert('Lỗi: ' + err.message));
        }

        // Bỏ qua button
        document.querySelector('a[href^="/booking/payment"]').addEventListener('click', function() {
            isSubmitted = true;
        });
    </script>
</x-layouts.main>
