<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterReport extends Model
{
    protected $fillable = [
        'sede_id',
        'user_id',
        'solicitado_por',
        'titulo',
        'periodo',
        'frecuencia',
        'observaciones_visitas',
        'observaciones_checklists',
        'observaciones_capacitaciones',
        'observaciones_hallazgos_incidentes',
        'resumen_ejecutivo',
        'observaciones_matriz',
        'estado',
        'datos_anexo',
        'observacion_revision',
    ];

    protected $casts = [
        'datos_anexo' => 'array',
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'solicitado_por');
    }

    public function getFormattedPeriodAttribute()
    {
        $period = $this->periodo;
        $freq = $this->frecuencia ?? 'mensual';

        if ($freq === 'trimestral' || str_contains($period, '-Q')) {
            $q = substr($period, -1);
            $y = substr($period, 0, 4);
            return "TRIMESTRE $q - $y";
        }

        if ($freq === 'semestral' || str_contains($period, '-S')) {
            $s = substr($period, -1);
            $y = substr($period, 0, 4);
            return "SEMESTRE $s - $y";
        }

        if ($freq === 'anual' || str_contains($period, '-A')) {
            $y = substr($period, 0, 4);
            return "ANUAL - $y";
        }

        // Por defecto: Mensual
        try {
            return \Carbon\Carbon::parse($period . '-01')->translatedFormat('F Y');
        } catch (\Exception $e) {
            return $period;
        }
    }
}
