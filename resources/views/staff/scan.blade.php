<x-layouts.app title="Quét Mã QR - Nhân viên">
    <div class="max-w-2xl mx-auto px-4 py-16">
        <div class="glass rounded-2xl p-8 text-center animate-fade-in-up">
            <h1 class="text-2xl font-bold text-white mb-2">📱 Quét Mã QR Soát Vé</h1>
            <p class="text-[var(--color-cinema-text-muted)] mb-8">Dành cho nhân viên rạp chiếu</p>

            <div class="mb-8">
                <div id="reader" class="mx-auto overflow-hidden rounded-xl" style="max-width: 400px; border: 2px solid var(--color-cinema-border);"></div>
                <button id="btn-start-scan" class="btn-primary mt-4" style="display: none;">Bắt đầu quét Camera</button>
            </div>

            <div class="text-[var(--color-cinema-text-muted)] mb-4">HOẶC</div>

            <div class="flex max-w-md mx-auto gap-2">
                <input type="text" id="manual-qr" placeholder="Nhập dữ liệu QR bằng tay để test..." class="flex-1 px-4 py-3 rounded-lg text-white text-sm focus:outline-none" style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);">
                <button onclick="processQRData(document.getElementById('manual-qr').value)" class="btn-accent whitespace-nowrap px-6">Quét</button>
            </div>

            <div id="scan-result" class="mt-8 text-left hidden">
                <div class="p-4 rounded-xl mb-4" id="result-alert"></div>
                <div class="glass rounded-xl p-4 space-y-2 text-sm" id="result-info">
                    <!-- Ticket info will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Html5QrcodeScanner !== 'undefined') {
                const scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} });
                scanner.render(onScanSuccess, onScanFailure);
            } else {
                document.getElementById('reader').innerHTML = '<p class="p-8 text-yellow-400">Không thể tải thư viện quét Camera, vui lòng dùng ô nhập tay để test.</p>';
            }
        });

        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Scan result: ${decodedText}`);
            processQRData(decodedText);
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
        }

        function processQRData(qrString) {
            if (!qrString) {
                alert('Vui lòng nhập mã QR');
                return;
            }

            const resultContainer = document.getElementById('scan-result');
            const alertBox = document.getElementById('result-alert');
            const infoBox = document.getElementById('result-info');

            fetch('/api/staff/tickets/scan', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ qr_code: qrString })
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
            .then(({ status, ok, data }) => {
                resultContainer.classList.remove('hidden');
                
                if (ok) {
                    alertBox.className = 'p-4 rounded-xl mb-4 bg-green-500/20 border border-green-500/50 text-green-400';
                    alertBox.innerHTML = `<strong>✅ Thành công:</strong> ${data.message}`;
                    
                    const o = data.data;
                    const startTime = new Date(o.show?.start_time);
                    infoBox.innerHTML = `
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Mã ĐH</span><span class="text-white font-bold">#${o.id}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Phim</span><span class="text-white">${o.show?.movie?.title}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Khách hàng</span><span class="text-white">${o.user?.name || ''}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Giờ chiếu</span><span class="text-white">${startTime.toLocaleTimeString('vi-VN')}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Phòng</span><span class="text-white font-bold">${o.show?.room?.name}</span></div>
                        <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Ghế</span><span class="text-[var(--color-cinema-accent)] font-bold text-lg">${o.seats?.map(s => s.seat?.row_label + s.seat?.seat_number).join(', ')}</span></div>
                        ${o.snacks?.length > 0 ? `<div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Bắp nước</span><span class="text-[var(--color-cinema-accent)] font-semibold">${o.snacks.map(s => s.snack?.name + ' x' + s.quantity).join(', ')}</span></div>` : ''}
                    `;
                } else {
                    alertBox.className = 'p-4 rounded-xl mb-4 bg-red-500/20 border border-red-500/50 text-red-400';
                    alertBox.innerHTML = `<strong>❌ Lỗi (${status}):</strong> ${data.message}`;
                    infoBox.innerHTML = '';
                    
                    if (data.data) {
                        const o = data.data;
                        infoBox.innerHTML = `
                            <p class="text-white font-medium mb-2">Thông tin vé:</p>
                            <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Phim</span><span class="text-white">${o.show?.movie?.title || ''}</span></div>
                            <div class="flex justify-between"><span class="text-[var(--color-cinema-text-muted)]">Trạng thái vé</span><span class="${o.status === 'completed' ? 'text-gray-400' : 'text-yellow-400'}">${o.status}</span></div>
                        `;
                    }
                }
                
                // Cuộn xuống để xem kết quả
                resultContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            })
            .catch(err => {
                alert('Lỗi kết nối Server!');
                console.error(err);
            });
        }
    </script>
</x-layouts.app>
