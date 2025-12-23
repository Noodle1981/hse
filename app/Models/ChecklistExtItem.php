<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistExtItem extends Model
{
    /** @use HasFactory<\Database\Factories\ChecklistExtItemFactory> */
    use HasFactory;

    protected $fillable = [
        'ubicacion',
        'clase',
        'capacidad',
        'prueba',
        'vencimiento',
        'manometro',
        'recipiente',
        'manija',
        'manguera',
        'tobera',
        'precinto',
        'pintura',
        'senaletica',
        'checklist_ext_id'
    ];

    public function checklistExt(): BelongsTo
    {
        return $this->belongsTo(ChecklistExt::class);
    }
}
