<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'content',
        'parent_id',
        'order',
        'is_active',
        'is_multifunctional'
    ];

    protected static function booted()
    {
        static::saving(function ($menu) {

            if ($menu->isDirty('name') || empty($menu->slug)) {
                $menu->slug = self::generateUniqueSlug(
                    $menu->name,
                    $menu->parent_id,
                    $menu->id
                );
            }
        });
    }

    public static function generateUniqueSlug(string $name, $parentId = null, $ignoreId = null): string
    {
        $baseSlug = Str::slug(str_replace('&', 'and', $name));
        $slug = $baseSlug;

        $query = self::where('slug', $slug)
            ->where('parent_id', $parentId);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if (!$query->exists()) {
            return $slug;
        }

        $counter = 2;

        while (true) {
            $slug = $baseSlug . '-' . $counter;

            $query = self::where('slug', $slug)
                ->where('parent_id', $parentId);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $counter++;
        }
    }

    public function getFullSlugAttribute(): string
    {
        $segments = [];
        $menu = $this;

        while ($menu) {
            if ($menu->slug) {
                array_unshift($segments, $menu->slug);
            }
            $menu = $menu->parent;
        }

        return implode('/', $segments);
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
