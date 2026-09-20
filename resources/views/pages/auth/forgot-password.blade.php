<x-layouts.app title="Quên mật khẩu - CineStar">
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md animate-fade-in-up">
            <div class="glass rounded-2xl p-8">
                <div class="text-center mb-8">
                    <span class="text-4xl">🎬</span>
                    <h1 class="text-2xl font-bold mt-3 gradient-text">Quên mật khẩu?</h1>
                    <p class="text-sm text-[var(--color-cinema-text-muted)] mt-2">Nhập email của bạn và chúng tôi sẽ gửi liên kết để đặt lại mật khẩu</p>
                </div>

                <form onsubmit="event.preventDefault(); alert('Đây là giao diện demo, chức năng gửi email chưa được kết nối!');" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Email đã đăng ký</label>
                        <input type="email" id="email" name="email" required autofocus
                               class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"
                               placeholder="email@example.com">
                    </div>

                    <button type="submit" class="btn-primary w-full text-center py-3 text-base">
                        Gửi liên kết khôi phục
                    </button>
                </form>

                <p class="text-center text-sm text-[var(--color-cinema-text-muted)] mt-6">
                    <a href="/login" class="text-[var(--color-cinema-primary)] font-semibold hover:text-[var(--color-cinema-primary-hover)]">← Quay lại đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
