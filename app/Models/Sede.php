<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sede extends Model
{
    /** @use HasFactory<\Database\Factories\SedeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre_sede',
        'direccion',
        'correo',
        'telefono',
        'tipo_sede',
        'localidad',
        'provincia_id',
        'latitud',
        'longitud',
        'responsable_1_nombre',
        'responsable_1_cargo',
        'responsable_1_email',
        'responsable_2_nombre',
        'responsable_2_cargo',
        'responsable_2_email',
        'responsable_3_nombre',
        'responsable_3_cargo',
        'responsable_3_email',
        'empresa_id'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sede_users');
    }

    public function generadorConstancias()
    {
        return $this->hasMany(GeneradorConstancia::class);
    }

    public function constancias()
    {
        return $this->hasMany(GeneradorConstancia::class);
    }

    public function checklists()
    {
        return $this->hasMany(ChecklistsAsign::class);
    }

    public function assignments()
    {
        return $this->hasMany(ChecklistAssignment::class);
    }

    public function checklistAssignments()
    {
        return $this->hasMany(ChecklistAssignment::class);
    }

    public function capacitacionesPresenciales()
    {
        return $this->hasMany(CapacitacionPresencial::class);
    }

    public function capacitacionesVirtuales()
    {
        return $this->hasMany(CapacitacionVirual::class);
    }
    
    public function informes()
    {
        return $this->hasMany(Informe::class);
    }

    public function inspections()
    {
        return $this->hasMany(DynamicInspection::class);
    }

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function riskMatrices()
    {
        return $this->hasMany(RiskMatrix::class);
    }

    public function annualPlans()
    {
        return $this->hasMany(AnnualPlan::class);
    }
    public function visitCertificates()
    {
        return $this->hasMany(VisitCertificate::class);
    }
}
