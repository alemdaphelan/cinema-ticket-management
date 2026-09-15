<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="CineStar - Hệ thống đặt vé xem phim trực tuyến hàng đầu Việt Nam">
    <title>{{ $title ?? 'CineStar Cinema' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col text-cinema-text font-sans antialiased bg-cinema-bg">

    {{-- Navbar --}}
    <nav class="glass sticky top-0 z-50 px-4 py-3">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold text-xl" style="background: linear-gradient(135deg, var(--color-cinema-primary), var(--color-cinema-accent));">
                    🎬
                </div>
                <span class="text-xl font-bold gradient-text hidden sm:block">CineStar</span>
            </a>

            {{-- Nav Links --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="/" class="text-sm font-medium hover:text-[var(--color-cinema-primary)] transition-colors {{ request()->is('/') ? 'text-[var(--color-cinema-primary)]' : 'text-[var(--color-cinema-text-muted)]' }}">
                    Trang chủ
                </a>
                <a href="/schedule" class="text-sm font-medium hover:text-[var(--color-cinema-primary)] transition-colors {{ request()->is('schedule') ? 'text-[var(--color-cinema-primary)]' : 'text-[var(--color-cinema-text-muted)]' }}">
                    Lịch chiếu
                </a>
                @auth
                <a href="/booking/history" class="text-sm font-medium hover:text-[var(--color-cinema-primary)] transition-colors {{ request()->is('booking/history') ? 'text-[var(--color-cinema-primary)]' : 'text-[var(--color-cinema-text-muted)]' }}">
                    Vé của tôi
                </a>
                @endauth
            </div>

            {{-- Auth Buttons --}}
            <div class="flex items-center gap-3">
                @auth
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-[var(--color-cinema-text-muted)] hidden sm:block">
                            Xin chào, <span class="text-[var(--color-cinema-accent)] font-semibold">{{ Auth::user()->name }}</span>
                        </span>

                        @if(Auth::user()->role === 'admin')
                            <a href="/admin/dashboard" class="text-xs px-3 py-1.5 rounded-full font-semibold" style="background: var(--color-cinema-accent); color: #000;">
                                Admin
                            </a>
                        @elseif(Auth::user()->role === 'staff')
                            <a href="/staff/scan" class="text-xs px-3 py-1.5 rounded-full font-semibold" style="background: var(--color-cinema-success); color: #000;">
                                Staff
                            </a>
                        @endif

                        <form action="/logout" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm px-4 py-2 rounded-lg font-medium transition-all hover:bg-[var(--color-cinema-card)]" style="color: var(--color-cinema-text-muted);">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                @else
                    <a href="/login" class="text-sm px-4 py-2 rounded-lg font-medium transition-all hover:bg-[var(--color-cinema-card)]" style="color: var(--color-cinema-text-muted);">
                        Đăng nhập
                    </a>
                    <a href="/register" class="btn-primary text-sm !py-2 !px-4 rounded-lg">
                        Đăng ký
                    </a>
                @endguest
            </div>

            {{-- Mobile Menu Toggle --}}
            <button class="md:hidden text-[var(--color-cinema-text)] p-2" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden md:hidden mt-3 pb-3 border-t border-[var(--color-cinema-border)]">
            <div class="flex flex-col gap-2 pt-3">
                <a href="/" class="px-3 py-2 rounded-lg text-sm hover:bg-[var(--color-cinema-card)]">Trang chủ</a>
                <a href="/schedule" class="px-3 py-2 rounded-lg text-sm hover:bg-[var(--color-cinema-card)]">Lịch chiếu</a>
                @auth
                    <a href="/booking/history" class="px-3 py-2 rounded-lg text-sm hover:bg-[var(--color-cinema-card)]">Vé của tôi</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="p-4 rounded-lg text-sm font-medium bg-green-500/10 text-green-500 border border-green-500/30">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="p-4 rounded-lg text-sm font-medium bg-red-500/10 text-red-500 border border-red-500/30">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="mt-16 border-t border-[var(--color-cinema-border)]" style="background: var(--color-cinema-surface);">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-2xl">🎬</span>
                        <span class="text-xl font-bold gradient-text">CineStar</span>
                    </div>
                    <p class="text-sm text-[var(--color-cinema-text-muted)]">
                        Hệ thống đặt vé xem phim trực tuyến hàng đầu. Trải nghiệm điện ảnh tuyệt vời nhất.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4 text-[var(--color-cinema-accent)]">Liên kết</h4>
                    <ul class="space-y-2 text-sm text-[var(--color-cinema-text-muted)]">
                        <li><a href="/" class="hover:text-white transition-colors">Trang chủ</a></li>
                        <li><a href="/schedule" class="hover:text-white transition-colors">Lịch chiếu</a></li>
                        <li><a href="/api/docs" class="hover:text-white transition-colors">API Docs</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4 text-[var(--color-cinema-accent)]">Hỗ trợ</h4>
                    <ul class="space-y-2 text-sm text-[var(--color-cinema-text-muted)]">
                        <li>Hotline: 1900 1234</li>
                        <li>Email: support@cinestar.vn</li>
                        <li>Giờ làm việc: 8:00 - 22:00</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4 text-[var(--color-cinema-accent)]">Tài khoản test</h4>
                    <ul class="space-y-2 text-sm text-[var(--color-cinema-text-muted)]">
                        <li>Admin: admin@cinema.com</li>
                        <li>Staff: staff@cinema.com</li>
                        <li>User: user@cinema.com</li>
                        <li>Password: password</li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-[var(--color-cinema-border)] text-center text-sm text-[var(--color-cinema-text-muted)]">
                © {{ date('Y') }} CineStar Cinema. Dự án môn học - Nhóm MNM.
            </div>
        </div>
    </footer>

    <script>
        // CSRF token cho fetch requests
        window.CSRF_TOKEN = '{{ csrf_token() }}';
        window.IS_AUTHENTICATED = {{ Auth::check() ? 'true' : 'false' }};
        window.AUTH_USER = {!! Auth::check() ? json_encode(Auth::user()) : 'null' !!};
    </script>
</body>
</html>