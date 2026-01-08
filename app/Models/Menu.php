<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'slug', // add slug
        'content',
        'parent_id',
        'order',
        'is_active'
    ];

    protected static function booted()
    {
        static::saving(function ($menu) {
            // Only generate slug if the menu is a leaf (no children)
            if ($menu->children()->count() == 0) {
                $menu->slug = self::generateSlug($menu->name);
            } else {
                $menu->slug = null; // parents have no slug
            }
        });

        static::saved(function ($menu) {
            // After saving, if it has children, make sure parent's slug is null
            if ($menu->children()->count() > 0) {
                $menu->slug = null;
                $menu->saveQuietly(); // prevent recursion
            }
        });
    }

    public static function generateSlug($name)
    {
        $slug = Str::slug(str_replace('&', 'and', $name));
        $original = $slug;
        $counter = 1;

        // Ensure slug uniqueness
        while (self::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function isEffectivelyActive()
    {
        if (!$this->is_active)
            return false;
        if ($this->parent)
            return $this->parent->isEffectivelyActive();
        return true;
    }
}
