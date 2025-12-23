<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistVeh extends Model
{
    /** @use HasFactory<\Database\Factories\ChecklistVehFactory> */
    use HasFactory;

    protected $fillable = [
        'marca',
        'modelo',
        'dominio',
        'kilometraje',
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
        'n16',
        'n17',
        'n18',
        'n19',
        'n20',
        'n21',
        'n22',
        'n23',
        'n24',
        'n25',
        'n26',
        'n27',
        'n28',
        'n29',
        'n30',
        'n31',
        'n32',
        'n33',
        'n34',
        'n35',
        'n36',
        'n37',
        'n38',
        'n39',
        'n40',
        'n41',
        'checklist_asign_id'
    ];

    public function checklistAsign(): BelongsTo
    {
        return $this->belongsTo(checklistAsign::class);
    }
}
