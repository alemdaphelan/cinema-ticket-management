<x-layouts.app title="Lịch sử vé - CineStar">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-6">📋 Lịch sử đặt vé</h1>

        <div id="orders-list">
            <div class="text-center py-12 text-[var(--color-cinema-text-muted)]">
                <div class="inline-block w-6 h-6 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin"></div>
                <p class="mt-2">Đang tải...</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/orders/history', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                const orders = result.data || [];
                const container = document.getElementById('orders-list');

                if (orders.length === 0) {
                    container.innerHTML = '<div class="glass rounded-2xl p-12 text-center"><p class="text-[var(--color-cinema-text-muted)]">Bạn chưa có vé nào. <a href="/" class="text-[var(--color-cinema-primary)]">Đặt vé ngay!</a></p></div>';
                    return;
                }

                container.innerHTML = orders.map((o, i) => {
                    const startTime = new Date(o.show?.start_time);
                    const statusColors = { pending: 'text-yellow-400', paid: 'text-green-400', completed: 'text-gray-400', cancelled: 'text-red-400' };
                    const statusLabels = { pending: '⏳ Đang chờ', paid: '✅ Đã thanh toán', completed: '☑️ Đã sử dụng', cancelled: '❌ Đã hủy' };

                    return `
                        <div class="glass rounded-xl p-5 mb-4 animate-fade-in-up" style="animation-delay: ${i * 0.1}s;">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <h3 class="font-bold text-white">${o.show?.movie?.title || 'Phim'}</h3>
                                    <div class="text-sm text-[var(--color-cinema-text-muted)] mt-1 space-y-1">
                                        <p>📅 ${startTime.toLocaleDateString('vi-VN')} • ${startTime.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'})}</p>
                                        <p>🏢 ${o.show?.room?.name || '—'} • Ghế: <span class="text-[var(--color-cinema-accent)]">${o.seats?.map(s => s.seat?.row_label + s.seat?.seat_number).join(', ') || '—'}</span></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-lg text-[var(--color-cinema-accent)]">${Number(o.total_amount).toLocaleString('vi-VN')}đ</span>
                                    <span class="${statusColors[o.status] || 'text-gray-400'} text-sm block mt-1">${statusLabels[o.status] || o.status}</span>
                                </div>
                            </div>

                            ${o.status === 'paid' && o.qr_code ? `
                                <div class="mt-4 pt-4 border-t border-[var(--color-cinema-border)]">
                                    <button onclick="toggleQR(this, '${o.qr_code.replace(/'/g, "\\'")}')"
                                            class="text-sm text-[var(--color-cinema-primary)] hover:text-[var(--color-cinema-primary-hover)] font-medium">
                                        📱 Hiện mã QR
                                    </button>
                                    <div class="qr-container hidden mt-3 text-center">
                                        <canvas class="qr-canvas inline-block"></canvas>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                }).join('');
            });
        });

        function toggleQR(btn, qrData) {
            const container = btn.nextElementSibling;
            container.classList.toggle('hidden');

            if (!container.classList.contains('hidden')) {
                const canvas = container.querySelector('.qr-canvas');
                if (canvas && !canvas.dataset.rendered) {
                    QRCode.toCanvas(canvas, qrData, { width: 150, margin: 2 });
                    canvas.dataset.rendered = 'true';
                }
                btn.textContent = '📱 Ẩn mã QR';
            } else {
                btn.textContent = '📱 Hiện mã QR';
            }
        }
    </script>
</x-layouts.app>
