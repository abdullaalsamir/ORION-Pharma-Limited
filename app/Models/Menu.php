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

    public function isEffectivelyActive()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->parent) {
            return $this->parent->isEffectivelyActive();
        }

        return true;
    }
}
