<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitEppDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_certificate_id',
        'employee_id',
        'epp_items',
        'employee_signature',
        'notes',
    ];

    protected $casts = [
        'epp_items' => 'array', // [{item, qty, size}]
    ];

    public function visit()
    {
        return $this->belongsTo(VisitCertificate::class, 'visit_certificate_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
