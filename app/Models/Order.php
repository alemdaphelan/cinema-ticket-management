<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'show_id',
        'voucher_id',
        'total_amount',
        'status',
        'payment_method',
        'qr_code',
        'hold_expires_at',
    ];

    protected $casts = [
        'hold_expires_at' => 'datetime',
    ];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function seats()
    {
        return $this->hasMany(OrderSeat::class);
    }

    public function snacks()
    {
        return $this->hasMany(OrderSnack::class);
    }
}
