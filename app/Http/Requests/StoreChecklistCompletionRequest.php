<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreChecklistCompletionRequest extends FormRequest
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
            'supervisor_name' => 'required|string|max:255',
            'supervisor_signature' => 'required|string',
            'technician_signature' => 'required|string',
            'answers' => 'nullable|array',
            'notes' => 'nullable|string',
            'visit_id' => 'nullable|integer',
        ];
    }

    /**
     * Get the sanitized data, excluding any client-side time-tampering variables.
     */
    public function validatedWithSafeTime(): array
    {
        // Explicitly exclude date/time fields from the input to prevent client-side overrides.
        return $this->safe()->except(['completed_at', 'date', 'time', 'timestamp']);
    }
}
