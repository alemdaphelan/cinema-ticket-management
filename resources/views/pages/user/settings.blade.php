<x-layouts.app title="Cài đặt Tài khoản - CineStar">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto glass rounded-2xl p-8 border border-[var(--color-cinema-border)]">
            <h2 class="text-2xl font-bold text-white mb-6">Cài đặt Tài khoản</h2>

            <form action="{{ route('user.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-[var(--color-cinema-text-muted)] mb-2">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]" style="background: var(--color-cinema-surface); border: 1px solid var(--color-cinema-border); color: white;" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[var(--color-cinema-text-muted)] mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)]" style="background: var(--color-cinema-surface); border: 1px solid var(--color-cinema-border); color: white;" required>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full btn-primary py-3 rounded-xl font-bold text-lg">
                        Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
