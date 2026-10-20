<x-layouts.main title="Lịch sử vé - CineStar">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-6">Lịch sử đặt vé</h1>

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
                    const durationMins = o.show?.movie?.duration_minutes || 120;
                    const endTime = o.show?.end_time ? new Date(o.show.end_time) : new Date(startTime.getTime() + durationMins * 60000);
                    const statusStyles = { 
                        pending: 'text-amber-400 border-amber-500/40 bg-amber-500/20 font-semibold', 
                        paid: 'text-green-400 border-green-500/50 bg-green-500/20 font-bold', 
                        completed: 'text-slate-300 border-slate-500/40 bg-slate-500/20', 
                        cancelled: 'text-red-400 border-red-500/50 bg-red-500/20 font-bold' 
                    };
                    const statusLabels = { 
                        pending: 'Chờ thanh toán', 
                        paid: 'Đã thanh toán', 
                        completed: 'Đã sử dụng', 
                        cancelled: 'Đã hủy' 
                    };
                    const isPending = o.status === 'pending';
                    const targetLink = isPending ? `/booking/payment/${o.id}` : `/booking/success/${o.id}`;

                    const dateFormatted = startTime.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    const startStr = startTime.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                    const endStr = endTime.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                    const timeFormatted = `${startStr} ~ ${endStr}`;
                    const seatNumbers = o.seats?.map(s => (s.seat?.row_label || '') + (s.seat?.seat_number || '')).join(', ') || 'Chưa chọn';

                    return `
                        <a href="${targetLink}" 
                           class="group relative block glass rounded-2xl p-6 mb-5 border border-[var(--color-cinema-border)] hover:border-[var(--color-cinema-primary)]/60 transition-all duration-300 ease-out transform hover:-translate-y-1 hover:scale-[1.015] hover:shadow-2xl hover:shadow-[var(--color-cinema-primary)]/10 cursor-pointer overflow-hidden animate-fade-in-up" 
                           style="animation-delay: ${i * 0.05}s;">
                            
                            {{-- Subtle Glow Effect on Hover --}}
                            <div class="absolute inset-0 bg-gradient-to-r from-[var(--color-cinema-primary)]/0 via-[var(--color-cinema-primary)]/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>

                            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
                                {{-- Left info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                                        <h3 class="font-bold text-white text-lg md:text-xl group-hover:text-[var(--color-cinema-accent)] transition-colors truncate">
                                            ${o.show?.movie?.title || 'Phim'}
                                        </h3>
                                        <span class="text-xs px-3 py-1 rounded-full border shadow-sm ${statusStyles[o.status] || 'text-gray-400 border-gray-500/30'}">
                                            ${statusLabels[o.status] || o.status}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1.5 gap-x-6 text-sm text-[var(--color-cinema-text-muted)] mt-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium uppercase tracking-wider text-slate-400">Suất chiếu:</span>
                                            <span class="text-slate-200 font-medium">${timeFormatted} — ${dateFormatted}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium uppercase tracking-wider text-slate-400">Phòng:</span>
                                            <span class="text-slate-200 font-medium">${o.show?.room?.name || '—'}</span>
                                        </div>
                                        <div class="sm:col-span-2 flex items-center gap-2">
                                            <span class="text-xs font-medium uppercase tracking-wider text-slate-400">Ghế:</span>
                                            <span class="text-[var(--color-cinema-accent)] font-semibold">${seatNumbers}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Right info / CTA --}}
                                <div class="flex md:flex-col items-center md:items-end justify-between gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-[var(--color-cinema-border)]/50 shrink-0">
                                    <div>
                                        <span class="text-xs text-[var(--color-cinema-text-muted)] block md:text-right">Tổng tiền</span>
                                        <span class="font-bold text-xl md:text-2xl text-[var(--color-cinema-accent)]">${Number(o.total_amount).toLocaleString('vi-VN')}đ</span>
                                    </div>

                                    ${isPending ? `
                                        <span class="btn-primary text-xs py-2 px-5 rounded-xl font-bold shadow-lg shadow-red-500/25 flex items-center gap-1.5 group-hover:brightness-110 transition-all">
                                            <span>Thanh toán ngay</span>
                                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </span>
                                    ` : `
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center bg-white/5 group-hover:bg-[var(--color-cinema-primary)]/20 text-slate-400 group-hover:text-white transition-all transform group-hover:translate-x-1">
                                            <svg class="w-4 h-4 text-[var(--color-cinema-accent)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                    `}
                                </div>
                            </div>
                        </a>
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
</x-layouts.main>
