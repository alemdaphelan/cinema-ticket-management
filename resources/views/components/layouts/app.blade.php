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
        <div class="w-[95%] max-w-[1600px] mx-auto flex items-center justify-between">
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
                <a href="/#genres" class="text-sm font-medium hover:text-[var(--color-cinema-primary)] transition-colors text-[var(--color-cinema-text-muted)]">
                    Thể loại
                </a>
                <a href="/schedule" class="text-sm font-medium hover:text-[var(--color-cinema-primary)] transition-colors {{ request()->is('schedule') ? 'text-[var(--color-cinema-primary)]' : 'text-[var(--color-cinema-text-muted)]' }}">
                    Lịch chiếu
                </a>
                @auth
                <a href="/booking/history" class="text-sm font-medium hover:text-[var(--color-cinema-primary)] transition-colors {{ request()->is('booking/history') ? 'text-[var(--color-cinema-primary)]' : 'text-[var(--color-cinema-text-muted)]' }}">
                    Vé của tôi
                </a>
                @endauth
                <a href="/about" class="text-sm font-medium hover:text-[var(--color-cinema-primary)] transition-colors {{ request()->is('about') ? 'text-[var(--color-cinema-primary)]' : 'text-[var(--color-cinema-text-muted)]' }}">
                    Giới thiệu
                </a>

                {{-- Search Bar --}}
                <div class="hidden lg:block ml-4 relative group" id="search-container">
                    <form action="/" method="GET" class="relative z-50">
                        <input type="text" name="search" id="search-input" placeholder="Tìm kiếm phim..." value="{{ request('search') }}"
                               autocomplete="off"
                               class="w-64 px-4 py-2 pr-10 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-cinema-primary)] transition-all"
                               style="background: var(--color-cinema-card); border: 1px solid var(--color-cinema-border); color: var(--color-cinema-text);">
                        <button type="submit" class="absolute right-0 top-0 h-full px-3 flex items-center justify-center text-[var(--color-cinema-text-muted)] hover:text-white transition-colors cursor-pointer z-10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </form>
                    
                    {{-- Live Search Dropdown --}}
                    <div id="search-dropdown" class="absolute top-full left-0 mt-2 w-[350px] rounded-xl shadow-2xl hidden z-50 overflow-hidden transform opacity-0 scale-95 transition-all duration-200" 
                         style="background: var(--color-cinema-surface); border: 1px solid var(--color-cinema-border);">
                        <div id="search-results" class="max-h-[400px] overflow-y-auto">
                            <!-- Results injected by JS -->
                        </div>
                    </div>
                </div>
            </div>

            {{-- Auth Buttons --}}
            <div class="flex items-center gap-4">
                @auth
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-[var(--color-cinema-text-muted)] hidden sm:block">
                            Xin chào, <span class="text-[var(--color-cinema-accent)] font-semibold">{{ str_replace('Khách hàng ', '', Auth::user()->name) }}</span>
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

                        {{-- User Avatar Dropdown --}}
                        <div class="relative">
                            <button onclick="document.getElementById('user-dropdown').classList.toggle('hidden')" class="flex items-center justify-center w-9 h-9 rounded-full bg-[var(--color-cinema-primary)] text-white font-bold cursor-pointer hover:ring-2 hover:ring-[var(--color-cinema-primary-hover)] transition-all focus:outline-none">
                                {{ substr(str_replace('Khách hàng ', '', Auth::user()->name), 0, 1) }}
                            </button>
                            
                            {{-- Dropdown Menu --}}
                            <div id="user-dropdown" class="absolute right-0 mt-2 min-w-[200px] rounded-xl shadow-lg hidden z-50 overflow-hidden" 
                                 style="background: var(--color-cinema-surface); border: 1px solid var(--color-cinema-border);">
                                <div class="p-2 space-y-1">
                                    <a href="/profile/settings" class="block w-full text-left px-4 py-2.5 text-sm text-[var(--color-cinema-text-muted)] hover:text-white hover:bg-[var(--color-cinema-card)] rounded-lg transition-colors whitespace-nowrap">
                                        Cài đặt
                                    </a>
                                    <a href="/profile/password" class="block w-full text-left px-4 py-2.5 text-sm text-[var(--color-cinema-text-muted)] hover:text-white hover:bg-[var(--color-cinema-card)] rounded-lg transition-colors whitespace-nowrap">
                                        Đổi mật khẩu
                                    </a>
                                    <form action="/logout" method="POST" class="block w-full m-0">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors whitespace-nowrap">
                                            Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
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
        <div class="w-[95%] max-w-[1600px] mx-auto px-4 mt-4 w-full">
            <div class="p-4 rounded-lg text-sm font-medium bg-green-500/10 text-green-500 border border-green-500/30">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="w-[95%] max-w-[1600px] mx-auto px-4 mt-4 w-full">
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
        <div class="w-[95%] max-w-[1600px] mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
                <div class="pr-4 lg:col-span-1">
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
                    <h4 class="font-semibold mb-4 text-[var(--color-cinema-accent)]">Chính sách</h4>
                    <ul class="space-y-2 text-sm text-[var(--color-cinema-text-muted)]">
                        <li><a href="#" class="hover:text-white transition-colors">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4 text-[var(--color-cinema-accent)]">Kết nối với chúng tôi</h4>
                    <div class="flex gap-4">
                        <a href="#" class="text-[var(--color-cinema-text-muted)] hover:text-white transition-colors">
                            {{-- Facebook --}}
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-[var(--color-cinema-text-muted)] hover:text-white transition-colors">
                            {{-- Instagram --}}
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"/></svg>
                        </a>
                        <a href="#" class="text-[var(--color-cinema-text-muted)] hover:text-white transition-colors">
                            {{-- TikTok --}}
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.24-2.61.94-5.22 3.03-6.73 1.5-1.12 3.39-1.63 5.25-1.49.12 1.34.1 2.69.13 4.04-1.23-.28-2.58-.2-3.7.39-1.14.61-1.95 1.76-2.13 3.06-.21 1.4.37 2.87 1.44 3.73 1.2.98 2.91 1.24 4.36.78 1.45-.46 2.53-1.63 2.9-3.08.15-.59.18-1.21.19-1.83V.02z"/></svg>
                        </a>
                        <a href="#" class="text-[var(--color-cinema-text-muted)] hover:text-white transition-colors">
                            {{-- Twitter --}}
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-[var(--color-cinema-border)] text-center text-sm text-[var(--color-cinema-text-muted)]">
                © {{ date('Y') }} CineStar Cinema. Đồ án môn học - Nhóm 7 MNM.
            </div>
        </div>
    </footer>

    <script>
        // CSRF token cho fetch requests
        window.CSRF_TOKEN = '{{ csrf_token() }}';
        window.IS_AUTHENTICATED = {{ Auth::check() ? 'true' : 'false' }};
        window.AUTH_USER = {!! Auth::check() ? json_encode(Auth::user()) : 'null' !!};
        
        // Live Search Logic
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const searchDropdown = document.getElementById('search-dropdown');
            const searchResults = document.getElementById('search-results');
            let debounceTimer;

            if(!searchInput) return;

            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(debounceTimer);
                
                if (query.length < 2) {
                    hideDropdown();
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetchSearchResults(query);
                }, 300); // 300ms debounce
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!document.getElementById('search-container').contains(e.target)) {
                    hideDropdown();
                }
            });
            
            // Show dropdown again when focusing input if there's text
            searchInput.addEventListener('focus', function() {
                if (this.value.trim().length >= 2 && searchResults.innerHTML.trim() !== '') {
                    showDropdown();
                }
            });

            function fetchSearchResults(query) {
                // Show loading state
                searchResults.innerHTML = '<div class="p-4 text-center text-sm text-[var(--color-cinema-text-muted)]"><div class="inline-block w-4 h-4 border-2 border-[var(--color-cinema-primary)] border-t-transparent rounded-full animate-spin mr-2 align-middle"></div>Đang tìm kiếm...</div>';
                showDropdown();

                fetch(`/api/movies?search=${encodeURIComponent(query)}&limit=5`)
                    .then(res => res.json())
                    .then(result => {
                        const movies = result.data || [];
                        
                        if (movies.length === 0) {
                            searchResults.innerHTML = '<div class="p-4 text-center text-sm text-[var(--color-cinema-text-muted)]">Không tìm thấy phim nào phù hợp.</div>';
                            return;
                        }

                        searchResults.innerHTML = movies.map(movie => `
                            <a href="/movies/${movie.id}" class="flex items-start gap-3 p-3 hover:bg-[var(--color-cinema-card)] transition-colors border-b border-[var(--color-cinema-border)] last:border-0">
                                <img src="${movie.poster_url || '/images/placeholder.png'}" alt="${movie.title}" class="w-12 h-16 object-cover rounded" onerror="this.onerror=null; this.src='/images/placeholder.png';">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-white text-sm font-bold truncate mb-1">${movie.title}</h4>
                                    <div class="text-xs text-[var(--color-cinema-text-muted)] flex flex-wrap gap-x-2 gap-y-1">
                                        ${movie.duration_minutes ? `<span>${movie.duration_minutes} phút</span>` : ''}
                                        ${movie.genre ? `<span class="truncate max-w-[120px]">${movie.genre}</span>` : ''}
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full mt-1 inline-block ${movie.status === 'showing' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400'}">
                                        ${movie.status === 'showing' ? 'Đang chiếu' : 'Sắp chiếu'}
                                    </span>
                                </div>
                            </a>
                        `).join('');
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        searchResults.innerHTML = '<div class="p-4 text-center text-sm text-red-400">Có lỗi xảy ra, vui lòng thử lại sau.</div>';
                    });
            }

            function showDropdown() {
                searchDropdown.classList.remove('hidden');
                // Small delay to allow display:block to apply before animating opacity/transform
                setTimeout(() => {
                    searchDropdown.classList.remove('opacity-0', 'scale-95');
                }, 10);
            }

            function hideDropdown() {
                searchDropdown.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    searchDropdown.classList.add('hidden');
                }, 200); // Wait for transition
            }
        });
    </script>
</body>
</html>