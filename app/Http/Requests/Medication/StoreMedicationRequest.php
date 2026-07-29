<?php

namespace App\Http\Requests\Medication;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:users,id',
            'medicine_name' => 'required|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'times' => 'nullable|array',
            'times.*' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'instructions' => 'nullable|string',
            'prescribing_doctor' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }
}
