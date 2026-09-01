<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Snack extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function orderSnacks()
    {
        return $this->hasMany(OrderSnack::class);
    }
}
