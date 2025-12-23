<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistE extends Model
{
    /** @use HasFactory<\Database\Factories\ChecklistEFactory> */
    use HasFactory;

    protected $fillable = [
        'lugar',
        'n1',
        'n2',
        'n3',
        'n4',
        'n5',
        'n6',
        'n7',
        'n8',
        'n9',
        'n10',
        'n11',
        'n12',
        'n13',
        'n14',
        'n15',
        'checklist_asign_id'
    ];

    public function checklistAsign(): BelongsTo
    {
        return $this->belongsTo(ChecklistAsign::class);
    }
}
