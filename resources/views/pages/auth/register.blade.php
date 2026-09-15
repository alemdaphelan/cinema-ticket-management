<x-layouts.app title="Đăng ký - CineStar">
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md animate-fade-in-up">
            <div class="glass rounded-2xl p-8">
                <div class="text-center mb-8">
                    <span class="text-4xl">🎬</span>
                    <h1 class="text-2xl font-bold mt-3 gradient-text">Tạo tài khoản</h1>
                    <p class="text-sm text-[var(--color-cinema-text-muted)] mt-2">Đăng ký để đặt vé và nhận ưu đãi</p>
                </div>

                <form action="/register" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Họ và tên</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                               class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"
                               placeholder="Nguyễn Văn A">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"
                               placeholder="email@example.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Mật khẩu</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"
                               placeholder="Tối thiểu 6 ký tự">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-2 text-[var(--color-cinema-text-muted)]">Xác nhận mật khẩu</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-4 py-3 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border);"
                               placeholder="Nhập lại mật khẩu">
                    </div>

                    <button type="submit" class="btn-primary w-full text-center py-3 text-base">
                        Đăng ký
                    </button>
                </form>

                <p class="text-center text-sm text-[var(--color-cinema-text-muted)] mt-6">
                    Đã có tài khoản?
                    <a href="/login" class="text-[var(--color-cinema-primary)] font-semibold hover:text-[var(--color-cinema-primary-hover)]">Đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.app>
