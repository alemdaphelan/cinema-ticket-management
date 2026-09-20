<x-mail::message>
# Đặt vé thành công!

Xin chào **{{ $order->user->name ?? 'Quý khách' }}**,

Cảm ơn bạn đã đặt vé tại CineStar. Dưới đây là thông tin vé của bạn:

**Phim:** {{ $order->show->movie->title ?? 'N/A' }}
**Rạp:** {{ $order->show->room->name ?? 'N/A' }}
**Giờ chiếu:** {{ \Carbon\Carbon::parse($order->show->start_time)->format('H:i - d/m/Y') }}
**Ghế:** {{ $order->seats->map(fn($s) => $s->seat->row_label . $s->seat->seat_number)->join(', ') }}

@if($order->snacks && $order->snacks->count() > 0)
**Bắp nước:** {{ $order->snacks->map(fn($s) => $s->snack->name . ' x' . $s->quantity)->join(', ') }}
@endif

**Tổng tiền:** {{ number_format($order->total_amount, 0, ',', '.') }} VNĐ

### Mã QR của bạn:
Vui lòng đưa mã QR này (hoặc mã code dưới đây) cho nhân viên soát vé khi đến rạp.

**Mã Code:** {{ json_decode($order->qr_code)->code ?? 'N/A' }}

<x-mail::button :url="url('/')">
Xem chi tiết vé trên Web
</x-mail::button>

Chúc bạn xem phim vui vẻ!<br>
{{ config('app.name') }}
</x-mail::message>
