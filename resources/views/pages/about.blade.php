<x-layouts.main title="Giới thiệu - CineStar">
    {{-- Hero Section --}}
    <section class="relative overflow-hidden" style="min-height: 50vh;">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://image.tmdb.org/t/p/original/8Y43POKjjKDGI9MH89NW0NAzzp8.jpg'); opacity: 0.4;"></div>
        <div class="absolute inset-0" style="background: linear-gradient(0deg, rgba(15,15,35,1) 0%, rgba(15,15,35,0.7) 100%);"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 py-20 md:py-32 flex items-center justify-center h-full">
            <div class="text-center animate-fade-in-up">
                <h1 class="text-5xl md:text-7xl font-extrabold mb-6 gradient-text">
                    Về Chúng Tôi
                </h1>
                <p class="text-xl md:text-2xl text-white max-w-3xl mx-auto font-light">
                    Hệ thống rạp chiếu phim hiện đại nhất Việt Nam, mang đến trải nghiệm giải trí đẳng cấp quốc tế.
                </p>
            </div>
        </div>
    </section>

    {{-- Stats / Features --}}
    <section class="max-w-7xl mx-auto px-4 py-16 -mt-20 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="glass p-8 rounded-2xl text-center hover:scale-105 transition-transform" style="border-top: 4px solid var(--color-cinema-primary);">
                <div class="text-5xl mb-4">🎬</div>
                <h3 class="text-2xl font-bold text-white mb-2">50+ Rạp chiếu</h3>
                <p class="text-[var(--color-cinema-text-muted)]">Phủ sóng khắp các tỉnh thành trên toàn quốc</p>
            </div>
            <div class="glass p-8 rounded-2xl text-center hover:scale-105 transition-transform" style="border-top: 4px solid var(--color-cinema-accent);">
                <div class="text-5xl mb-4">✨</div>
                <h3 class="text-2xl font-bold text-white mb-2">Công nghệ IMAX</h3>
                <p class="text-[var(--color-cinema-text-muted)]">Âm thanh vòm Dolby Atmos, màn hình siêu nét</p>
            </div>
            <div class="glass p-8 rounded-2xl text-center hover:scale-105 transition-transform" style="border-top: 4px solid var(--color-cinema-success);">
                <div class="text-5xl mb-4">🍿</div>
                <h3 class="text-2xl font-bold text-white mb-2">Dịch vụ 5 sao</h3>
                <p class="text-[var(--color-cinema-text-muted)]">Thưởng thức điện ảnh với phong cách thượng lưu</p>
            </div>
        </div>
    </section>

    {{-- Story Section --}}
    <section class="max-w-7xl mx-auto px-4 py-16">
        <div class="flex flex-col md:flex-row items-center gap-12">
            <div class="w-full md:w-1/2">
                <img src="https://image.tmdb.org/t/p/original/rSPw7tgCH9c6NqICZef4kZjFOQ5.jpg" alt="Rạp chiếu phim CineStar" class="rounded-2xl shadow-2xl opacity-80" style="border: 1px solid var(--color-cinema-border);">
            </div>
            <div class="w-full md:w-1/2">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Trải nghiệm điện ảnh <span class="text-[var(--color-cinema-primary)]">Tuyệt đỉnh</span></h2>
                <p class="text-[var(--color-cinema-text-muted)] text-lg mb-4 leading-relaxed">
                    Được thành lập vào năm 2026, CineStar Cinema ra đời với sứ mệnh mang đến cho khán giả Việt Nam những tác phẩm điện ảnh xuất sắc nhất thế giới với chất lượng hình ảnh, âm thanh đỉnh cao.
                </p>
                <p class="text-[var(--color-cinema-text-muted)] text-lg mb-8 leading-relaxed">
                    Chúng tôi không chỉ là rạp chiếu phim, chúng tôi là nơi kết nối những tâm hồn yêu nghệ thuật, nơi chia sẻ cảm xúc và lan tỏa niềm đam mê điện ảnh.
                </p>
            </div>
        </div>
    </section>

    {{-- Team Section --}}
    <section class="max-w-7xl mx-auto px-4 py-20 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Đồ Án Mã Nguồn Mở</h2>
        <p class="text-[var(--color-cinema-text-muted)] text-lg max-w-2xl mx-auto mb-12">
            Hệ thống đặt vé xem phim CineStar được phát triển bởi Nhóm 7 MNM. 
            Sử dụng kiến trúc hiện đại với sự kết hợp của các công nghệ mã nguồn mở hàng đầu.
        </p>
        
        <div class="inline-block p-8 rounded-3xl glass" style="background: linear-gradient(135deg, rgba(229,9,20,0.1), rgba(245,197,24,0.1)); border: 1px solid var(--color-cinema-border);">
            <div class="text-6xl mb-4">👨‍💻</div>
            <h3 class="text-2xl font-bold text-white mb-2">Nhóm 7 MNM</h3>
            <p class="text-[var(--color-cinema-text-muted)] max-w-md">
                Chúng tôi hi vọng dự án này sẽ mang lại cái nhìn rõ nét về việc xây dựng hệ thống với các công nghệ Mã Nguồn Mở trong thực tế.
            </p>
        </div>
    </section>
</x-layouts.main>
