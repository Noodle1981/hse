<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'sede_id',
        'category',
        'activity_name',
        'activity_code',
        'checklist_id',
        'year',
        'january', 'february', 'march', 'april', 'may', 'june',
        'july', 'august', 'september', 'october', 'november', 'december',
    ];

    protected $casts = [
        'january' => 'boolean',
        'february' => 'boolean',
        'march' => 'boolean',
        'april' => 'boolean',
        'may' => 'boolean',
        'june' => 'boolean',
        'july' => 'boolean',
        'august' => 'boolean',
        'september' => 'boolean',
        'october' => 'boolean',
        'november' => 'boolean',
        'december' => 'boolean',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function checklist()
    {
        return $this->belongsTo(DynamicChecklist::class, 'checklist_id');
    }
}
