<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function shows()
    {
        return $this->hasMany(Show::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
