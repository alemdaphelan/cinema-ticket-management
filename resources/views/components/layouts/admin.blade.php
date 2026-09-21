<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin - CineStar' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen" style="background-color: var(--color-cinema-bg);">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="admin-sidebar w-64 fixed h-full overflow-y-auto hidden lg:block">
            <div class="p-6">
                <a href="/admin/dashboard" class="flex items-center gap-3 mb-8">
                    <span class="text-2xl">🎬</span>
                    <div>
                        <span class="text-lg font-bold gradient-text">CineStar</span>
                        <span class="block text-xs text-[var(--color-cinema-text-muted)]">Admin Panel</span>
                    </div>
                </a>

                <nav class="space-y-1">
                    <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('admin/dashboard') ? 'active' : 'text-[var(--color-cinema-text-muted)]' }}">
                        Dashboard
                    </a>
                    <a href="/admin/movies" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('admin/movies*') ? 'active' : 'text-[var(--color-cinema-text-muted)]' }}">
                        Quản lý Phim
                    </a>
                    <a href="/admin/shows" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('admin/shows*') ? 'active' : 'text-[var(--color-cinema-text-muted)]' }}">
                        Suất chiếu
                    </a>
                    <a href="/admin/snacks" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('admin/snacks*') ? 'active' : 'text-[var(--color-cinema-text-muted)]' }}">
                        Bắp nước
                    </a>
                    <a href="/admin/vouchers" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('admin/vouchers*') ? 'active' : 'text-[var(--color-cinema-text-muted)]' }}">
                        Voucher
                    </a>
                    <a href="/admin/orders" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('admin/orders*') ? 'active' : 'text-[var(--color-cinema-text-muted)]' }}">
                        Đơn hàng
                    </a>

                    <div class="border-t border-[var(--color-cinema-border)] my-4"></div>

                    <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--color-cinema-text-muted)]">
                        Xem trang chủ
                    </a>
                    <a href="/staff/scan" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--color-cinema-text-muted)]">
                        Quét vé
                    </a>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 lg:ml-64">
            {{-- Top Bar --}}
            <header class="glass sticky top-0 z-40 px-6 py-4 flex items-center justify-between">
                {{-- Mobile Menu --}}
                <button class="lg:hidden p-2 text-[var(--color-cinema-text)]" onclick="document.getElementById('admin-mobile-menu').classList.toggle('hidden')">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <h1 class="text-lg font-bold">{{ $header ?? 'Dashboard' }}</h1>

                <div class="flex items-center gap-3">
                    <span class="text-sm text-[var(--color-cinema-text-muted)]">
                        {{ Auth::user()->name ?? 'Admin' }}
                    </span>
                    <form action="/logout" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm px-3 py-1.5 rounded-lg hover:bg-[var(--color-cinema-card)] text-[var(--color-cinema-text-muted)]">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </header>

            {{-- Mobile Sidebar --}}
            <div id="admin-mobile-menu" class="hidden lg:hidden fixed inset-0 z-50 bg-black/50" onclick="this.classList.add('hidden')">
                <div class="admin-sidebar w-64 h-full overflow-y-auto" onclick="event.stopPropagation()">
                    <div class="p-6">
                        <a href="/admin/dashboard" class="flex items-center gap-3 mb-8">
                            <span class="text-2xl">🎬</span>
                            <span class="text-lg font-bold gradient-text">CineStar Admin</span>
                        </a>
                        <nav class="space-y-1">
                            <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--color-cinema-text-muted)]">Dashboard</a>
                            <a href="/admin/movies" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--color-cinema-text-muted)]">Quản lý Phim</a>
                            <a href="/admin/shows" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--color-cinema-text-muted)]">Suất chiếu</a>
                            <a href="/admin/snacks" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--color-cinema-text-muted)]">Bắp nước</a>
                            <a href="/admin/vouchers" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--color-cinema-text-muted)]">Voucher</a>
                            <a href="/admin/orders" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-[var(--color-cinema-text-muted)]">Đơn hàng</a>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="px-6 pt-4">
                    <div class="p-4 rounded-lg text-sm" style="background: rgba(0, 200, 83, 0.1); color: var(--color-cinema-success); border: 1px solid rgba(0, 200, 83, 0.3);">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            {{-- Page Content --}}
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>

    <script>
        window.CSRF_TOKEN = '{{ csrf_token() }}';
    </script>
</body>
</html>
