<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapacitacionPresencial extends Model
{
    /** @use HasFactory<\Database\Factories\CapacitacionFactory> */
    use HasFactory;

    protected $fillable = [
        'sede_id',
        'user_id',
        'tema',
        'foto',
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
}
