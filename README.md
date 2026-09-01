# Cinema Ticket Management API

Dự án API Quản lý vé xem phim (Cinema Ticket Management) được xây dựng trên nền tảng Laravel.

## Hướng dẫn cài đặt cho người mới (khi clone từ Github về)

Khi một người khác clone project này về máy, họ **KHÔNG THỂ** chạy lệnh `php artisan serve` ngay lập tức được. Lý do là vì các thư viện (vendor), file cấu hình môi trường (`.env`) và file database (`database.sqlite`) không được đẩy lên Github (do nằm trong `.gitignore`).

Để chạy được dự án sau khi clone, cần thực hiện đúng theo các bước sau:

**Bước 1: Cài đặt các thư viện (Dependencies)**
```bash
composer install
```

**Bước 2: Tạo file cấu hình môi trường (.env)**
Copy từ file mẫu `.env.example`:
```bash
cp .env.example .env
```
*(Trên Windows/Command Prompt có thể dùng: `copy .env.example .env`)*

**Bước 3: Tạo Application Key**
```bash
php artisan key:generate
```

**Bước 4: Tạo file Database SQLite (Dành riêng cho database SQLite)**
Tạo một file trống có tên `database.sqlite` trong thư mục `database/`:
```bash
# Trên macOS/Linux/Git Bash:
touch database/database.sqlite

# Trên Windows PowerShell:
New-Item -Path "database\database.sqlite" -ItemType File -Force
```

**Bước 5: Khởi tạo các bảng trong Database (Migrate)**
```bash
php artisan migrate
```
*(Nếu được hỏi "Would you like to create it?" cứ chọn Yes)*

**Bước 6: Khởi chạy Server**
```bash
php artisan serve
```

---

## Swagger API Documentation
Sau khi server chạy, truy cập đường dẫn sau để xem tài liệu API:
`http://localhost:8000/api/docs`
