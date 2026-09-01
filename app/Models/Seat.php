<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'row_label',
        'seat_number',
        'type',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function orderSeats()
    {
        return $this->hasMany(OrderSeat::class);
    }
}
