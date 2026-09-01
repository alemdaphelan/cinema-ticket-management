<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSnack extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'snack_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function snack()
    {
        return $this->belongsTo(Snack::class);
    }
}
