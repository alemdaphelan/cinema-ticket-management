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

Sau đó, mở file `.env` và cấu hình Database. Bạn có 2 cách chạy dự án:

**Cách 1: Chạy toàn bộ bằng Docker (Khuyên dùng)**
Nếu máy bạn đã cài Docker, bạn không cần sửa gì thêm trong `.env`. Chạy lệnh sau để khởi động cả Database (MySQL) và App (Laravel):
```bash
docker-compose up -d
```
App sẽ tự động chạy tại `http://localhost:8000`.

**Cách 2: Chạy thủ công bằng XAMPP / MySQL cục bộ**
Nếu không dùng Docker, bạn phải tự tạo một Database có tên `cinema_ticket_management` trong MySQL của bạn. Sau đó trong file `.env`, điều chỉnh thông tin nếu cần:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cinema_ticket_management
DB_USERNAME=root
DB_PASSWORD=mật_khẩu_của_bạn
```

**Bước 3: Tạo Application Key**
```bash
php artisan key:generate
```
*(Lưu ý: Nếu dùng Cách 1 - Docker, bạn cần vào container để chạy: `docker exec -it cinema_app php artisan key:generate`)*

**Bước 4: Khởi tạo các bảng trong Database (Migrate)**
```bash
php artisan migrate
```
*(Lưu ý: Nếu dùng Cách 1 - Docker, lệnh sẽ là: `docker exec -it cinema_app php artisan migrate`)*

**Bước 5: Khởi chạy Server (Chỉ dành cho Cách 2)**
```bash
php artisan serve
```

---

## Swagger API Documentation
Sau khi server chạy, truy cập đường dẫn sau để xem tài liệu API:
`http://localhost:8000/api/docs`
