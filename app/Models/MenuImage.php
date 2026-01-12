<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuImage extends Model
{
    protected $fillable = ['menu_id', 'file_path', 'file_name', 'is_active'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}