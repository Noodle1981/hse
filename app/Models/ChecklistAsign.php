<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChecklistAsign extends Model
{
    /** @use HasFactory<\Database\Factories\ChecklistAsignFactory> */
    use HasFactory;
    protected $fillable = [
        'sede_id',
        'user_id',
        'tipo_checklist',
        'checklist_id',
        'num_revision',
        'area',
        'inspector',
        'establecimiento',
        'acciones',
        'fecha_propuesta',
        'firma_responsable',
        'date_firma_responsable',
        'aclaracion_responsable',
        'email_responsable',
        'token_responsable',
        'code',
        'duration'
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
