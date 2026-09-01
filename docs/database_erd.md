# Sơ đồ quan hệ thực thể (ERD) & Cấu trúc Database

Dưới đây là sơ đồ ERD dựa trên yêu cầu của hệ thống đặt vé xem phim, hỗ trợ phân quyền, lưu trữ ghế, suất chiếu và luồng đặt vé an toàn.

## 1. Sơ đồ ERD (Mermaid)

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email
        string password
        string role "admin, staff, user"
        datetime created_at
    }
    
    MOVIES {
        bigint id PK
        string tmdb_id "ID lấy từ TMDB"
        string title
        string director
        string poster_url
        string teaser_url
        int duration_minutes "Thời lượng phim"
        string status "coming_soon, showing, stopped"
        datetime created_at
    }

    ROOMS {
        bigint id PK
        string name "Tên phòng chiếu (Phòng 1, Phòng 2)"
    }

    SEATS {
        bigint id PK
        bigint room_id FK
        string row_label "Hàng ghế (A, B, C...)"
        int seat_number "Số ghế (1, 2, 3...)"
        string type "Loại ghế (normal, vip)"
    }

    SHOWS {
        bigint id PK
        bigint movie_id FK
        bigint room_id FK
        datetime start_time "Giờ bắt đầu chiếu"
        datetime end_time "Giờ kết thúc (đã cộng 15-20p dọn rạp)"
        decimal price "Giá vé cơ bản của suất chiếu"
    }

    SNACKS {
        bigint id PK
        string name
        decimal price
        string image_url
        boolean is_active
    }

    VOUCHERS {
        bigint id PK
        string code "Mã nhập (VD: GIAM50K)"
        decimal discount_value "Giá trị giảm"
        string discount_type "percent, fixed"
        decimal min_order_value "Đơn tối thiểu để áp dụng"
        int max_uses_per_user "Số lần tối đa 1 user đc dùng"
        datetime valid_from
        datetime valid_to
        boolean is_active
    }

    ORDERS {
        bigint id PK
        bigint user_id FK "Có thể null nếu khách vãng lai mua tại quầy"
        bigint show_id FK
        bigint voucher_id FK "Có thể null"
        decimal total_amount "Tổng tiền thanh toán"
        string status "pending, paid, completed, cancelled"
        string payment_method "vnpay, momo, cash"
        string qr_code "Mã QR để quét soát vé"
        datetime hold_expires_at "Thời hạn hết 10 phút giữ ghế"
        datetime created_at
    }

    ORDER_SEATS {
        bigint id PK
        bigint order_id FK
        bigint seat_id FK
        decimal price "Giá vé lúc mua (có thể khác giá gốc)"
    }

    ORDER_SNACKS {
        bigint id PK
        bigint order_id FK
        bigint snack_id FK
        int quantity "Số lượng combo"
        decimal price "Giá tiền tại thời điểm mua"
    }

    USERS ||--o{ ORDERS : "places"
    MOVIES ||--o{ SHOWS : "has"
    ROOMS ||--o{ SHOWS : "hosts"
    ROOMS ||--o{ SEATS : "contains"
    SHOWS ||--o{ ORDERS : "booked_in"
    ORDERS ||--o{ ORDER_SEATS : "includes"
    SEATS ||--o{ ORDER_SEATS : "booked_as"
    ORDERS ||--o{ ORDER_SNACKS : "includes"
    SNACKS ||--o{ ORDER_SNACKS : "bought_as"
    VOUCHERS ||--o{ ORDERS : "applied_to"
```

## 2. Giải pháp Kỹ thuật cho các luồng nghiệp vụ khó

### 2.1. Logic Chọn ghế & Giữ ghế (10 phút)
Yêu cầu: Khách chọn ghế thì ghế bị khóa trong 10 phút, không cho người khác chọn.

**Giải pháp Database:**
- Khi user gửi request chọn ghế, hệ thống sẽ tạo một dòng trong bảng `ORDERS` với trạng thái `status = 'pending'` và cột `hold_expires_at = now() + 10 minutes`. Đồng thời insert các ghế vào `ORDER_SEATS`.
- Để biết một ghế đã bị đặt hay chưa, khi render Sơ đồ ghế, ta query như sau:
  ```sql
  SELECT os.seat_id FROM order_seats os
  JOIN orders o ON os.order_id = o.id
  WHERE o.show_id = {show_id}
  AND (
      o.status IN ('paid', 'completed') -- Vé đã mua
      OR (o.status = 'pending' AND o.hold_expires_at > NOW()) -- Đang bị giữ 10 phút
  )
  ```
- **Chống Concurrency (Bán trùng ghế):** Trong hàm API Chọn ghế, sử dụng **Database Lock (Pessimistic Locking)** của Laravel:
  ```php
  DB::transaction(function () use ($showId, $seatIds) {
      // Dùng lockForUpdate() để khóa record, 2 request gọi cùng lúc thì 1 request phải đợi request kia xong
      $lockedSeats = OrderSeat::whereIn('seat_id', $seatIds)
          ->whereHas('order', function($q) use ($showId) {
              $q->where('show_id', $showId)
                ->where(function($query) {
                    $query->whereIn('status', ['paid', 'completed'])
                          ->orWhere(function($sub) {
                              $sub->where('status', 'pending')
                                  ->where('hold_expires_at', '>', now());
                          });
                });
          })
          ->lockForUpdate() 
          ->get();
      
      if ($lockedSeats->isNotEmpty()) {
          throw new Exception('Ghế đã bị người khác chọn nhanh hơn!');
      }
      // Khởi tạo Order và OrderSeats
  });
  ```

### 2.2. Background Job: Tự động nhả ghế (Timeout)
Thay vì cronjob chạy mỗi phút quét DB (gây nặng server), hãy dùng **Laravel Queue (Delayed Jobs)**:
- Ngay khi tạo Order (giữ ghế) thành công, push một Job vào Queue với delay đúng 10 phút:
  ```php
  ReleaseSeatJob::dispatch($order->id)->delay(now()->addMinutes(10));
  ```
- Code trong `ReleaseSeatJob`:
  ```php
  public function handle() {
      $order = Order::find($this->orderId);
      if ($order && $order->status === 'pending') {
          $order->update(['status' => 'cancelled']);
          // Gắn sự kiện (Event/Websocket) để báo cho Frontend cập nhật UI hiển thị lại ghế trống
      }
  }
  ```

### 2.3. Xử lý thuật toán Timing (Chống trùng lịch chiếu)
Khi Admin thêm suất chiếu, bạn cần tính `end_time = start_time + movie_duration + buffer_time (15-20p)`.
Check trùng lặp thời gian trong cùng một `room_id`:
```php
$isConflict = Show::where('room_id', $roomId)
    ->where(function ($query) use ($newStartTime, $newEndTime) {
        $query->whereBetween('start_time', [$newStartTime, $newEndTime])
              ->orWhereBetween('end_time', [$newStartTime, $newEndTime])
              ->orWhere(function ($q) use ($newStartTime, $newEndTime) {
                  $q->where('start_time', '<=', $newStartTime)
                    ->where('end_time', '>=', $newEndTime);
              });
    })->exists();
```
