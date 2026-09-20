<x-layouts.app title="Đăng nhập - CineStar">
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md animate-fade-in-up">
            <div class="glass rounded-2xl p-8">
                <div class="text-center mb-8">
                    <span class="text-4xl">🎬</span>
                    <h1 class="text-2xl font-bold mt-3 gradient-text">Đăng nhập</h1>
                    <p class="text-sm text-[var(--color-cinema-text-muted)] mt-2">Chào mừng bạn quay trở lại CineStar</p>
                </div>

                <form action="/login" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"
                               placeholder="email@example.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Mật khẩu</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"
                               placeholder="••••••••">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-[var(--color-cinema-text-muted)]">
                            <input type="checkbox" name="remember" class="rounded border-gray-600">
                            Ghi nhớ đăng nhập
                        </label>
                        <a href="/forgot-password" class="text-sm text-[var(--color-cinema-primary)] hover:text-[var(--color-cinema-primary-hover)] transition-colors">Quên mật khẩu?</a>
                    </div>

                    <button type="submit" class="btn-primary w-full text-center py-3 text-base">
                        Đăng nhập
                    </button>
                </form>

                <p class="text-center text-sm text-[var(--color-cinema-text-muted)] mt-6">
                    Chưa có tài khoản?
                    <a href="/register" class="text-[var(--color-cinema-primary)] font-semibold hover:text-[var(--color-cinema-primary-hover)]">Đăng ký ngay</a>
                </p>

                <div class="mt-6"></div>
            </div>
        </div>
    </div>
</x-layouts.app>
