<?php

namespace App\Models\News;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'images',
        'description',
        'is_published',
    ];

    protected $casts = [
        'images' => 'array'
    ];
}
