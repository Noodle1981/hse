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
}
