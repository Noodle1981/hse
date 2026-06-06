<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMasterReportRequest extends FormRequest
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
            'sede_id' => 'required|exists:sedes,id',
            'user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:255',
            'periodo' => 'required|string',
            'frecuencia' => 'required|in:mensual,trimestral,semestral,anual',
        ];
    }

    /**
     * Get the sanitized data, excluding system-controlled columns.
     */
    public function validatedWithSafeTime(): array
    {
        return $this->safe()->except(['solicitado_por', 'estado', 'created_at', 'updated_at']);
    }
}
