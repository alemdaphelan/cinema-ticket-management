{{-- Global Modal & Toast Notification Component --}}
<div id="cinema-global-modal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/80 backdrop-blur-sm opacity-0 transition-opacity duration-300 p-4">
    <div id="cinema-modal-box" class="glass p-6 md:p-8 rounded-3xl max-w-md w-full text-center transform scale-95 transition-transform duration-300 border border-[var(--color-cinema-border)] shadow-2xl relative overflow-hidden"
         style="background: linear-gradient(135deg, rgba(25, 20, 28, 0.98), rgba(15, 15, 22, 0.98));">
        
        {{-- Icon Container --}}
        <div id="cinema-modal-icon-container" class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center shadow-inner border">
            <!-- Icon rendered via JS -->
        </div>
        
        <h3 class="text-xl font-bold text-white mb-2" id="cinema-modal-title">Thông báo</h3>
        <p class="text-sm text-[var(--color-cinema-text-muted)] mb-6 leading-relaxed" id="cinema-modal-message">
            Nội dung thông báo
        </p>
        
        <div class="flex flex-col gap-2.5" id="cinema-modal-actions">
            <button type="button" id="cinema-modal-confirm-btn" onclick="closeCinemaModal()" class="btn-primary w-full py-3 rounded-xl font-bold cursor-pointer shadow-lg shadow-red-500/20">
                Đã hiểu
            </button>
        </div>
    </div>
</div>

{{-- Toast Notification Container (Top-right) --}}
<div id="cinema-toast-container" class="fixed top-20 right-4 md:right-8 z-[9999] flex flex-col gap-3 pointer-events-none max-w-sm w-full">
</div>

<script>
    // === Global Cinema Modal & Toast System ===
    (function() {
        let modalCallback = null;

        window.showNotificationModal = function(options) {
            // Support either string or object: showNotificationModal('Title', 'Message', 'error')
            let title = 'Thông báo';
            let message = '';
            let type = 'info'; // 'success' | 'error' | 'warning' | 'info'
            let onConfirm = null;
            let confirmText = 'Đã hiểu';

            if (typeof options === 'string') {
                if (arguments.length >= 2) {
                    title = arguments[0];
                    message = arguments[1];
                    type = arguments[2] || 'info';
                    onConfirm = arguments[3] || null;
                } else {
                    message = options;
                }
            } else if (typeof options === 'object') {
                title = options.title || title;
                message = options.message || '';
                type = options.type || type;
                onConfirm = options.onConfirm || null;
                confirmText = options.confirmText || confirmText;
            }

            modalCallback = onConfirm;

            const modal = document.getElementById('cinema-global-modal');
            const box = document.getElementById('cinema-modal-box');
            const iconContainer = document.getElementById('cinema-modal-icon-container');
            const titleEl = document.getElementById('cinema-modal-title');
            const msgEl = document.getElementById('cinema-modal-message');
            const confirmBtn = document.getElementById('cinema-modal-confirm-btn');

            if (!modal) return;

            titleEl.textContent = title;
            msgEl.textContent = message;
            confirmBtn.textContent = confirmText;

            // Configure Icon and Colors based on type
            let iconSvg = '';
            let iconBg = '';
            let borderColor = '';

            switch (type) {
                case 'success':
                    iconBg = 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30';
                    borderColor = 'border-emerald-500/40';
                    confirmBtn.className = 'w-full py-3 rounded-xl font-bold cursor-pointer text-white shadow-lg shadow-emerald-500/20';
                    confirmBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                    iconSvg = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
                    break;
                case 'error':
                    iconBg = 'bg-red-500/15 text-red-400 border-red-500/30';
                    borderColor = 'border-red-500/40';
                    confirmBtn.className = 'btn-primary w-full py-3 rounded-xl font-bold cursor-pointer shadow-lg shadow-red-500/20';
                    confirmBtn.style.background = '';
                    iconSvg = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>`;
                    break;
                case 'warning':
                    iconBg = 'bg-amber-500/15 text-amber-400 border-amber-500/30';
                    borderColor = 'border-amber-500/40';
                    confirmBtn.className = 'w-full py-3 rounded-xl font-bold cursor-pointer text-white shadow-lg shadow-amber-500/20';
                    confirmBtn.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
                    iconSvg = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
                    break;
                default: // info
                    iconBg = 'bg-blue-500/15 text-blue-400 border-blue-500/30';
                    borderColor = 'border-blue-500/40';
                    confirmBtn.className = 'btn-primary w-full py-3 rounded-xl font-bold cursor-pointer';
                    confirmBtn.style.background = '';
                    iconSvg = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
                    break;
            }

            iconContainer.className = `w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center shadow-inner border ${iconBg}`;
            iconContainer.innerHTML = iconSvg;
            box.className = `glass p-6 md:p-8 rounded-3xl max-w-md w-full text-center transform scale-95 transition-transform duration-300 border shadow-2xl relative overflow-hidden ${borderColor}`;

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            }, 10);
        };

        window.closeCinemaModal = function() {
            const modal = document.getElementById('cinema-global-modal');
            const box = document.getElementById('cinema-modal-box');
            if (!modal) return;

            modal.classList.add('opacity-0');
            if (box) {
                box.classList.remove('scale-100');
                box.classList.add('scale-95');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
                if (typeof modalCallback === 'function') {
                    const cb = modalCallback;
                    modalCallback = null;
                    cb();
                }
            }, 300);
        };

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCinemaModal();
            }
        });

        // Close when clicking backdrop
        const modalEl = document.getElementById('cinema-global-modal');
        if (modalEl) {
            modalEl.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeCinemaModal();
                }
            });
        }

        // === Override native window.alert to automatically use custom modal ===
        window.alert = function(msg) {
            window.showNotificationModal({
                title: 'Thông báo',
                message: String(msg),
                type: 'info'
            });
        };

        // === Toast Notification Helper ===
        window.showToast = function(message, type = 'info') {
            const container = document.getElementById('cinema-toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            const colors = {
                success: 'border-emerald-500/50 text-emerald-300 bg-emerald-950/90 shadow-emerald-500/20',
                error: 'border-red-500/50 text-red-300 bg-red-950/90 shadow-red-500/20',
                warning: 'border-amber-500/50 text-amber-300 bg-amber-950/90 shadow-amber-500/20',
                info: 'border-blue-500/50 text-blue-300 bg-blue-950/90 shadow-blue-500/20'
            };

            toast.className = `pointer-events-auto flex items-center justify-between gap-3 px-5 py-3.5 rounded-2xl border glass shadow-xl transform transition-all duration-300 translate-y-2 opacity-0 text-sm ${colors[type] || colors.info}`;
            toast.innerHTML = `
                <div class="flex items-center gap-2.5">
                    <span class="text-base font-bold">${type === 'success' ? '✓' : (type === 'error' ? '✕' : 'ℹ')}</span>
                    <span class="text-slate-100 font-medium">${message}</span>
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };
    })();
</script>
