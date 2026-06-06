<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistSection extends Model
{
    protected $fillable = [
        'dynamic_checklist_id',
        'title',
        'description',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(DynamicChecklist::class, 'dynamic_checklist_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(DynamicQuestion::class, 'section_id')->orderBy('order');
    }
}
