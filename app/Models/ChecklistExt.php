<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistExt extends Model
{
    /** @use HasFactory<\Database\Factories\ChecklistExtFactory> */
    use HasFactory;

    protected $fillable = [
        'inspector',
        'establecimiento',
        'area',
        'responsable',
        'acciones',
        'checklist_asign_id'
    ];

    public function checklistAsign(): BelongsTo
    {
        return $this->belongsTo(ChecklistAsign::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistExtItem::class);
    }
}
