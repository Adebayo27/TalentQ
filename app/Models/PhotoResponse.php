<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'photo_request_id', 'user_id', 'thumbnail', 'high_resolution', 'status', 'comment'
    ];
}
