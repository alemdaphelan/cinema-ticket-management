<x-layouts.app title="Cài đặt Tài khoản - CineStar">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto glass rounded-2xl p-8 border border-[var(--color-cinema-border)]">
            <h2 class="text-2xl font-bold text-white mb-6">Cài đặt Tài khoản</h2>

            <form action="{{ route('user.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Avatar --}}
                <div class="flex flex-col items-center gap-4">
                    <div class="relative">
                        @if($user->avatar)
                            <img id="avatar-preview" src="{{ $user->avatar }}" alt="Avatar"
                                 class="w-24 h-24 rounded-full object-cover border-2 border-[var(--color-cinema-primary)]">
                        @else
                            <div id="avatar-placeholder" class="w-24 h-24 rounded-full flex items-center justify-center text-3xl font-bold text-white border-2 border-[var(--color-cinema-primary)]"
                                 style="background: linear-gradient(135deg, var(--color-cinema-primary), var(--color-cinema-accent));">
                                {{ substr(str_replace('Khách hàng ', '', $user->name), 0, 1) }}
                            </div>
                            <img id="avatar-preview" src="" alt="Avatar"
                                 class="w-24 h-24 rounded-full object-cover border-2 border-[var(--color-cinema-primary)] hidden">
                        @endif
                    </div>
                    <div>
                        <label for="avatar-input" class="cursor-pointer text-sm font-medium px-4 py-2 rounded-lg transition-all hover:opacity-80"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border); color: var(--color-cinema-accent);">
                            Thay đổi ảnh đại diện
                        </label>
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden"
                               onchange="previewAvatar(this)">
                    </div>
                </div>

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

    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    const placeholder = document.getElementById('avatar-placeholder');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layouts.app>
