<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMasterReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'resumen_ejecutivo' => 'nullable|string',
            'observaciones_visitas' => 'nullable|string',
            'observaciones_checklists' => 'nullable|string',
            'observaciones_capacitaciones' => 'nullable|string',
            'observaciones_hallazgos_incidentes' => 'nullable|string',
            'observaciones_matriz' => 'nullable|string',
            'datos_anexo' => 'nullable|array',
            'finalize' => 'nullable|boolean',
            'observacion_revision' => 'nullable|string',
            'action' => 'nullable|string|in:approve,observe',
        ];
    }

    /**
     * Get sanitized data, ensuring no unauthorized modification of critical fields.
     */
    public function safeData(): array
    {
        // Exclude transient action indicators 'action' and 'finalize'
        return $this->safe()->except(['action', 'finalize', 'estado', 'user_id', 'sede_id', 'solicitado_por', 'created_at', 'updated_at']);
    }
}
