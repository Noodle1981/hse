<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistHmItem extends Model
{
    /** @use HasFactory<\Database\Factories\ChecklistHmItemFactory> */
    use HasFactory;

    protected $fillable = [
        'herramienta',
        'condiciones',
        'observaciones',
        'checklist_hm_id'
    ];

    public function checklistHm(): BelongsTo
    {
        return $this->belongsTo(ChecklistHm::class);
    }
}
