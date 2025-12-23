<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SedeUser extends Model
{
    /** @use HasFactory<\Database\Factories\SedeUserFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sede_id'
    ];
}
