<x-layouts.main title="Chi tiết vé - CineStar">
    <div class="max-w-2xl mx-auto px-4 py-16">
        <div class="glass rounded-2xl p-8 text-center animate-fade-in-up">
            <h1 class="text-2xl font-bold text-white mb-2" id="status-title">Đang tải...</h1>
            <p class="text-[var(--color-cinema-text-muted)] mb-8" id="status-message"></p>

            <div id="ticket-info" class="text-left space-y-4 mb-8">
                <div class="text-center py-8 text-[var(--color-cinema-text-muted)]">Đang tải thông tin vé...</div>
            </div>

            {{-- QR Code --}}
            <div id="qr-section" class="mb-8 hidden">
                <h3 class="text-sm font-semibold text-[var(--color-cinema-accent)] mb-3">Mã QR - Xuất trình khi vào rạp</h3>
                <div id="qr-display" class="inline-block p-4 rounded-xl" style="background: white;">
                    <div class="text-center text-gray-500 text-sm py-4">Đang tạo QR...</div>
                </div>
                <p class="text-xs text-[var(--color-cinema-text-muted)] mt-3">Nhân viên sẽ quét mã QR này để soát vé</p>
            </div>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/booking/history" class="btn-primary !py-3 !px-8">Xem lịch sử vé</a>
                <a href="/" class="btn-accent !py-3 !px-8">Về trang chủ</a>
            </div>
        </div>
    </div>

    {{-- QR Code Library --}}
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <script>
        const orderId = {{ $orderId }};

        document.addEventListener('DOMContentLoaded', function() {
            fetch(`/api/orders/${orderId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN }
            })
            .then(res => res.json())
            .then(result => {
                const o = result.data;
                const startTime = new Date(o.show?.start_time);
                
                let statusTitle = '';
                let statusMessage = '';
                let statusLabel = '';
                let statusColor = '';
                let showQR = false;

                switch(o.status) {
                    case 'paid':
                    case 'completed':
                        statusTitle = 'Đặt vé thành công!';
                        statusMessage = 'Cảm ơn bạn đã sử dụng dịch vụ CineStar';
                        statusLabel = 'Đã thanh toán';
                        statusColor = 'text-green-400';
                        showQR = true;
                        break;
                    case 'cancelled':
                        statusTitle = 'Đơn hàng đã hủy';
                        statusMessage = 'Đơn hàng này đã bị hủy bỏ do hết thời gian thanh toán hoặc được người dùng hủy.';
                        statusLabel = 'Đã hủy';
                        statusColor = 'text-red-400';
                        break;
                    case 'pending':
                        statusTitle = 'Chờ thanh toán';
                        statusMessage = 'Đơn hàng của bạn đang chờ thanh toán.';
                        statusLabel = 'Chờ thanh toán';
                        statusColor = 'text-yellow-400';
                        break;
                    default:
                        statusTitle = 'Chi tiết đơn hàng';
                        statusMessage = 'Thông tin chi tiết về đơn đặt vé của bạn.';
                        statusLabel = o.status;
                        statusColor = 'text-gray-400';
                }
                
                document.getElementById('status-title').textContent = statusTitle;
                document.getElementById('status-message').textContent = statusMessage;

                document.getElementById('ticket-info').innerHTML = `
                    <div class="glass rounded-xl p-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Phim</span><span class="text-white font-semibold">${o.show?.movie?.title || '—'}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Ngày chiếu</span><span class="text-white">${startTime.toLocaleDateString('vi-VN')}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Giờ chiếu</span><span class="text-white">${startTime.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'})}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Phòng</span><span class="text-white">${o.show?.room?.name || '—'}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Ghế</span><span class="text-[var(--color-cinema-accent)] font-bold">${o.seats?.map(s => s.seat?.row_label + s.seat?.seat_number).join(', ') || '—'}</span></div>
                        ${o.snacks?.length > 0 ? `<div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Bắp nước</span><span class="text-white">${o.snacks.map(s => s.snack?.name + ' x' + s.quantity).join(', ')}</span></div>` : ''}
                        <div class="border-t border-[var(--color-cinema-border)] my-2"></div>
                        <div class="flex justify-between text-lg font-bold"><span class="text-white">Tổng tiền</span><span class="text-[var(--color-cinema-accent)]">${Number(o.total_amount).toLocaleString('vi-VN')}đ</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Thanh toán</span><span class="text-white font-medium">${o.payment_method?.toUpperCase() || '—'}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Trạng thái</span><span class="${statusColor} font-medium">${statusLabel}</span></div>
                    </div>
                `;

                // Generate QR Code
                if (showQR && o.qr_code && typeof QRCode !== 'undefined') {
                    document.getElementById('qr-section').classList.remove('hidden');
                    const qrDiv = document.getElementById('qr-display');
                    qrDiv.innerHTML = '';
                    const canvas = document.createElement('canvas');
                    qrDiv.appendChild(canvas);
                    QRCode.toCanvas(canvas, o.qr_code, { width: 200, margin: 2 }, function(error) {
                        if (error) {
                            qrDiv.innerHTML = '<p class="text-red-500 text-sm p-4">Lỗi tạo QR Code</p>';
                        }
                    });
                }
            });
        });
    </script>
</x-layouts.main>
