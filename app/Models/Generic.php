<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Generic extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    protected static function booted()
    {
        static::saving(fn($g) => $g->slug = Str::slug($g->name));
    }
}