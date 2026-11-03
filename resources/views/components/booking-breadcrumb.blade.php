@props([
    'step' => 1,
    'orderId' => null,
    'showId' => null,
    'holdExpiresAt' => null,
])

@php
    $steps = [
        1 => ['name' => 'Chọn ghế', 'desc' => 'Sơ đồ rạp', 'icon' => 'seat'],
        2 => ['name' => 'Bắp nước', 'desc' => 'Combo & Snacks', 'icon' => 'snack'],
        3 => ['name' => 'Thanh toán', 'desc' => 'Voucher & Cổng TT', 'icon' => 'payment'],
        4 => ['name' => 'Hoàn tất', 'desc' => 'Vé xem phim & QR', 'icon' => 'ticket'],
    ];
@endphp

<div class="glass rounded-2xl p-4 sm:p-5 mb-8 border border-[var(--color-cinema-border)] shadow-xl relative overflow-hidden">
    {{-- Ambient glow bar --}}
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[var(--color-cinema-primary)] via-[var(--color-cinema-accent)] to-[var(--color-cinema-primary)] opacity-40"></div>

    <div class="flex items-center justify-between max-w-4xl mx-auto relative">
        @foreach($steps as $s => $info)
            @php
                $isCurrent = ($step === $s);
                $isCompleted = ($step > $s);
                $isUpcoming = ($step < $s);

                // Determine click handler / URL
                $canClick = false;
                $clickAction = '';
                $clickTitle = '';

                if ($s === 1 && in_array($step, [2, 3])) {
                    // Click step 1 from snacks or payment -> quay lại trang chọn ghế (giữ nguyên đơn và ghế đã chọn)
                    $canClick = true;
                    $clickAction = "window.location.href='/booking/seats/" . ($showId ?? '') . "'";
                    $clickTitle = "Quay lại kiểm tra / đổi ghế";
                } elseif ($s === 2 && $step === 3 && $orderId) {
                    // Click step 2 from payment -> go back to snacks
                    $canClick = true;
                    $clickAction = "window.location.href='/booking/snacks/{$orderId}'";
                    $clickTitle = "Nhấp để đổi bắp nước";
                } elseif ($s === 3 && $step === 2 && $orderId) {
                    // Click step 3 from snacks -> proceed to payment
                    $canClick = true;
                    $clickAction = "typeof submitSnacks === 'function' ? submitSnacks() : window.location.href='/booking/payment/{$orderId}'";
                    $clickTitle = "Tiếp tục thanh toán";
                }
            @endphp

            {{-- Step Item --}}
            <div class="flex-1 flex flex-col items-center relative z-10 group {{ $canClick ? 'cursor-pointer' : '' }}"
                 @if($canClick) onclick="{{ $clickAction }}" title="{{ $clickTitle }}" @endif>
                
                {{-- Step Badge / Icon --}}
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center font-bold text-sm sm:text-base transition-all duration-300 relative
                    @if($isCurrent)
                        bg-gradient-to-br from-[var(--color-cinema-primary)] to-[var(--color-cinema-accent)] text-white shadow-[0_0_20px_rgba(229,9,20,0.5)] ring-4 ring-red-500/20 scale-105
                    @elseif($isCompleted)
                        bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 shadow-inner group-hover:scale-105 group-hover:border-emerald-400
                    @else
                        bg-[var(--color-cinema-card)] text-[var(--color-cinema-text-muted)] border border-[var(--color-cinema-border)] opacity-60
                    @endif">
                    
                    @if($isCompleted)
                        {{-- Completed checkmark --}}
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        {{-- Step Number or Icon --}}
                        <span>{{ $s }}</span>
                    @endif

                    @if($canClick && $s === 1)
                        {{-- Indicator on step 1: Click to change seats --}}
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-amber-500 text-black rounded-full flex items-center justify-center text-[10px] font-bold shadow" title="Đổi ghế">
                            ↺
                        </span>
                    @elseif($canClick && $s === 2 && $step === 3)
                        {{-- Indicator on step 2: Click to change snacks --}}
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-amber-500 text-black rounded-full flex items-center justify-center text-[10px] font-bold shadow" title="Đổi bắp nước">
                            ↺
                        </span>
                    @endif
                </div>

                {{-- Step Label --}}
                <div class="mt-2 text-center">
                    <span class="block text-xs sm:text-sm font-semibold transition-colors
                        @if($isCurrent)
                            text-white font-bold
                        @elseif($isCompleted)
                            text-emerald-400 group-hover:text-emerald-300
                        @else
                            text-[var(--color-cinema-text-muted)]
                        @endif">
                        {{ $info['name'] }}
                    </span>
                    <span class="hidden md:block text-[11px] mt-0.5
                        @if($isCurrent)
                            text-[var(--color-cinema-accent)] font-medium
                        @elseif($isCompleted)
                            text-[var(--color-cinema-text-muted)] group-hover:text-white
                        @else
                            text-[var(--color-cinema-text-muted)] opacity-50
                        @endif">
                        @if($isCurrent)
                            Đang chọn
                        @elseif($isCompleted && $s === 1 && in_array($step, [2, 3]))
                            (Đổi ghế)
                        @elseif($isCompleted && $s === 2 && $step === 3)
                            (Đổi bắp nước)
                        @else
                            {{ $info['desc'] }}
                        @endif
                    </span>
                </div>
            </div>

            {{-- Connector Line --}}
            @if(!$loop->last)
                <div class="flex-1 h-0.5 mx-2 sm:mx-4 -mt-6 sm:-mt-7 transition-all duration-300
                    @if($step > $s)
                        bg-gradient-to-r from-emerald-500/80 to-emerald-400/80
                    @elseif($step === $s)
                        bg-gradient-to-r from-[var(--color-cinema-primary)] to-[var(--color-cinema-border)]
                    @else
                        bg-[var(--color-cinema-border)] opacity-40
                    @endif">
                </div>
            @endif
        @endforeach
    </div>

    {{-- Status info bar for Steps 1, 2, and 3 when holding seats --}}
    @if(($step === 1 && $orderId && $holdExpiresAt) || in_array($step, [2, 3]))
        <div class="mt-3 pt-3 border-t border-[var(--color-cinema-border)]/50 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-[var(--color-cinema-text-muted)]">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Ghế của bạn đang được giữ trong phiên đặt vé này. Bấm vào các bước phía trên để quay lại đổi ghế hoặc bắp nước.</span>
            </div>
            @if($holdExpiresAt)
                <div class="flex items-center gap-1.5 font-mono bg-red-500/10 border border-red-500/30 px-2.5 py-1 rounded-lg text-red-400 font-bold shrink-0">
                    <span class="text-[11px] font-sans font-normal text-[var(--color-cinema-text-muted)]">Thời gian giữ:</span>
                    <span id="breadcrumb-countdown">--:--</span>
                </div>
            @endif
        </div>
    @endif
</div>

@if($holdExpiresAt)
<script>
    (function() {
        const expiresAtStr = @json($holdExpiresAt);
        if (!expiresAtStr) return;
        const expiry = new Date(expiresAtStr);

        function updateBreadcrumbTimer() {
            const el = document.getElementById('breadcrumb-countdown');
            if (!el) return;

            const now = new Date();
            const diff = expiry - now;

            if (diff <= 0) {
                el.textContent = '00:00';
                el.classList.add('text-red-500', 'animate-pulse');
                return;
            }

            const mins = Math.floor(diff / 60000);
            const secs = Math.floor((diff % 60000) / 1000);
            el.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }

        updateBreadcrumbTimer();
        setInterval(updateBreadcrumbTimer, 1000);
    })();
</script>
@endif

{{-- Global function for confirming seat changes --}}
@once
<script>
    function confirmChangeSeats(orderId, showId) {
        if (!orderId) {
            if (showId) window.location.href = `/booking/seats/${showId}`;
            return;
        }

        if (typeof window.showConfirmModal === 'function') {
            window.showConfirmModal({
                title: 'Đổi lại ghế đã chọn?',
                message: 'Bạn có chắc chắn muốn chọn lại ghế cho suất chiếu này không? Ghế đang giữ và bắp nước đã chọn trong đơn hiện tại sẽ được giải phóng để bạn chọn lại từ đầu.',
                type: 'warning',
                confirmText: 'Xác nhận đổi ghế',
                cancelText: 'Giữ ghế hiện tại',
                onConfirm: function() {
                    executeChangeSeats(orderId, showId);
                }
            });
        } else {
            if (confirm('Bạn có chắc muốn chọn lại ghế cho suất chiếu này? Ghế đang giữ hiện tại sẽ được giải phóng.')) {
                executeChangeSeats(orderId, showId);
            }
        }
    }

    function executeChangeSeats(orderId, showId) {
        if (window.showToast) {
            window.showToast('Đang giải phóng ghế để bạn chọn lại...', 'info');
        }

        fetch(`/api/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CSRF_TOKEN || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ reason: 'change_seats' })
        })
        .then(res => res.json())
        .then(result => {
            const targetShowId = showId || (result.data && result.data.show_id) || result.show_id;
            if (targetShowId) {
                window.location.href = `/booking/seats/${targetShowId}`;
            } else {
                window.location.href = '/schedule';
            }
        })
        .catch(err => {
            console.error('Error changing seats:', err);
            if (showId) {
                window.location.href = `/booking/seats/${showId}`;
            } else {
                window.location.href = '/schedule';
            }
        });
    }
</script>
@endonce
