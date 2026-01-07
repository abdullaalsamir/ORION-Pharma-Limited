<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'order',
        'is_active'
    ];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')
            ->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('parent_id');
        });
    }

    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }

    // Add this method inside the Menu class
    public function isEffectivelyActive()
    {
        // If the current menu is inactive, it's inactive
        if (!$this->is_active) {
            return false;
        }

        // If it has a parent, check if the parent is effectively active
        if ($this->parent) {
            return $this->parent->isEffectivelyActive();
        }

        // If it's a root menu and is_active is true
        return true;
    }
}
