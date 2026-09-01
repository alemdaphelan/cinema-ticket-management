<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'title',
        'director',
        'poster_url',
        'teaser_url',
        'duration_minutes',
        'status',
    ];

    public function shows()
    {
        return $this->hasMany(Show::class);
    }
}
