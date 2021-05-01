<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'description', 'status'
    ];
}
