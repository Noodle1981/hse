<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CapacitacionVirual extends Model
{
    /** @use HasFactory<\Database\Factories\CapacitacionVirualFactory> */
    use HasFactory;

    protected $fillable = [
        'sede_id',
        'user_id',
        'tema',
        'foto',
        'link',
        'lugar',
        'instructor',
        'duracion',
        'email_responsable',
    ];

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function asistentes(): HasMany
    {
        return $this->hasMany(CapacitacionVirualAsistente::class);
    }
}
