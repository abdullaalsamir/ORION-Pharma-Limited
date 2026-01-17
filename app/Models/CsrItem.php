<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsrItem extends Model
{
    protected $fillable = ['title', 'description', 'csr_date', 'image_path', 'order', 'is_active'];
    protected $casts = ['csr_date' => 'date'];
}