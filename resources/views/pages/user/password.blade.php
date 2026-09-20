<x-layouts.app title="Đổi Mật Khẩu - CineStar">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto glass rounded-2xl p-8 border border-[var(--color-cinema-border)]">
            <h2 class="text-2xl font-bold text-white mb-6">Đổi Mật Khẩu</h2>

            <form action="{{ route('user.password.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-[var(--color-cinema-text-muted)] mb-2">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]" style="background: var(--color-cinema-surface); border: 1px solid var(--color-cinema-border); color: white;" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[var(--color-cinema-text-muted)] mb-2">Mật khẩu mới</label>
                    <input type="password" name="password" class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]" style="background: var(--color-cinema-surface); border: 1px solid var(--color-cinema-border); color: white;" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[var(--color-cinema-text-muted)] mb-2">Xác nhận mật khẩu mới</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]" style="background: var(--color-cinema-surface); border: 1px solid var(--color-cinema-border); color: white;" required>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full btn-primary py-3 rounded-xl font-bold text-lg">
                        Cập Nhật Mật Khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
