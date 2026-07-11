<?php

namespace App\Models;

use Database\Factories\SedeUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SedeUser extends Model
{
    /** @use HasFactory<SedeUserFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sede_id',
    ];
}
