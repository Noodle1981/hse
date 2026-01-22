<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EppDeliveryRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'sede_id',
        'employee_id',
        'requested_by_user_id',
        'status', // pending, completed
        'items', // json: {item_id: {cantidad, tipo}}
        'notes',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
}
