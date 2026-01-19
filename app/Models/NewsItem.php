<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsItem extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'news_date', 'image_path', 'order', 'is_active'];
    protected $casts = ['news_date' => 'date'];

    protected static function booted()
    {
        static::saving(function ($item) {
            if (empty($item->slug) || $item->isDirty('title')) {
                $item->slug = Str::slug($item->title);
            }
        });
    }
}