<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registro extends Model
{
    /** @use HasFactory<\Database\Factories\RegistroFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'tipo_registro',
        'descripcion',
        'nombre_usuario',
        'link'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
