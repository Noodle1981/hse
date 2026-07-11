<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidCuil;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return Auth::check() && $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'empresa_id' => 'required|exists:empresas,id',
            'sede_id' => 'required|exists:sedes,id',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dni' => ['required', 'regex:/^\d{7,8}$/', 'unique:employees,dni'],
            'cuil' => ['nullable', new ValidCuil],
            'puesto' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'empresa_id.required' => 'La empresa es obligatoria',
            'sede_id.required' => 'La sede es obligatoria',
            'name.required' => 'El nombre es obligatorio',
            'last_name.required' => 'El apellido es obligatorio',
            'dni.required' => 'El DNI es obligatorio',
            'dni.regex' => 'El DNI debe tener 7 u 8 dígitos',
            'dni.unique' => 'Ya existe un empleado con este DNI',
        ];
    }
}
