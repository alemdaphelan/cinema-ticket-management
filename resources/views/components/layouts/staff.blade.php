<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Staff - CineStar' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen" style="background-color: var(--color-cinema-bg);">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="admin-sidebar w-64 fixed h-full overflow-y-auto hidden lg:block" style="border-right: 1px solid var(--color-cinema-success);">
            <div class="p-6">
                <a href="/staff/scan" class="flex items-center gap-3 mb-8">
                    <span class="text-2xl">🎬</span>
                    <div>
                        <span class="text-lg font-bold text-[var(--color-cinema-success)]">CineStar Staff</span>
                    </div>
                </a>

                <nav class="space-y-1">
                    <a href="/staff/scan" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('staff/scan') ? 'bg-[var(--color-cinema-success)]/20 text-[var(--color-cinema-success)]' : 'text-[var(--color-cinema-text-muted)] hover:bg-[var(--color-cinema-card)]' }}">
                        Quét vé
                    </a>
                    <a href="/staff/orders" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('staff/orders*') ? 'bg-[var(--color-cinema-success)]/20 text-[var(--color-cinema-success)]' : 'text-[var(--color-cinema-text-muted)] hover:bg-[var(--color-cinema-card)]' }}">
                        Đơn hàng & Doanh thu
                    </a>
                    <a href="/staff/booking" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('staff/booking*') ? 'bg-[var(--color-cinema-success)]/20 text-[var(--color-cinema-success)]' : 'text-[var(--color-cinema-text-muted)] hover:bg-[var(--color-cinema-card)]' }}">
                        Bán vé tại quầy
                    </a>

                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <div class="border-t border-[var(--color-cinema-border)] my-4"></div>
                        <div class="px-2 mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-cinema-accent)]">Quyền Quản trị viên</span>
                        </div>
                        <a href="/admin/dashboard" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all shadow-md hover:brightness-110"
                           style="background: linear-gradient(135deg, var(--color-cinema-primary), #b20710);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Về trang Admin</span>
                        </a>
                        <a href="/" class="flex items-center gap-2.5 px-4 py-2 rounded-lg text-xs font-medium text-[var(--color-cinema-text-muted)] hover:bg-[var(--color-cinema-card)] hover:text-white mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Xem trang chủ</span>
                        </a>
                    @endif
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 lg:ml-64">
            {{-- Top Bar --}}
            <header class="glass sticky top-0 z-40 px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--color-cinema-success);">
                {{-- Mobile Menu --}}
                <button class="lg:hidden p-2 text-[var(--color-cinema-text)]" onclick="document.getElementById('admin-mobile-menu').classList.toggle('hidden')">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <h1 class="text-lg font-bold text-[var(--color-cinema-success)]">{{ $header ?? 'Staff Panel' }}</h1>

                <div class="flex items-center gap-3">
                    <x-theme-toggle />

                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <a href="/admin/dashboard" class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg transition-all shadow-md hover:scale-105"
                           style="background: linear-gradient(135deg, var(--color-cinema-primary), #b20710); color: white;" title="Quay về bảng điều khiển quản trị">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Về Admin</span>
                        </a>
                    @endif

                    <span class="text-sm text-[var(--color-cinema-text-muted)]">
                        {{ Auth::user()->role === 'admin' ? 'Admin: ' : 'Nhân viên: ' }}{{ Auth::user()->name ?? 'Staff' }}
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
                        <a href="/staff/scan" class="flex items-center gap-3 mb-8">
                            <span class="text-2xl">🎬</span>
                            <span class="text-lg font-bold text-[var(--color-cinema-success)]">CineStar Staff</span>
                        </a>
                        <nav class="space-y-1">
                            <a href="/staff/scan" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('staff/scan') ? 'bg-[var(--color-cinema-success)]/20 text-[var(--color-cinema-success)]' : 'text-[var(--color-cinema-text-muted)]' }}">Quét vé</a>
                            <a href="/staff/orders" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('staff/orders*') ? 'bg-[var(--color-cinema-success)]/20 text-[var(--color-cinema-success)]' : 'text-[var(--color-cinema-text-muted)]' }}">Đơn hàng & Doanh thu</a>
                            <a href="/staff/booking" class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->is('staff/booking*') ? 'bg-[var(--color-cinema-success)]/20 text-[var(--color-cinema-success)]' : 'text-[var(--color-cinema-text-muted)]' }}">Bán vé tại quầy</a>

                            @if(Auth::check() && Auth::user()->role === 'admin')
                                <div class="border-t border-[var(--color-cinema-border)] my-4"></div>
                                <a href="/admin/dashboard" class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm font-bold text-white shadow-md"
                                   style="background: linear-gradient(135deg, var(--color-cinema-primary), #b20710);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                    <span>Về trang Admin</span>
                                </a>
                            @endif
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
    <x-notification-modal />
</body>
</html>
