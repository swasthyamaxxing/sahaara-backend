<?php

namespace App\Http\Requests\Medical;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\CaretakerPatient;

class StoreMedicalHistoryRequest extends FormRequest
{
    /**
     * Only allow the caretaker to add records
     */
    public function authorize(): bool
    {
        $caretaker = $this->user();

        // 1. Ensure the authenticated user is actually a caretaker
        if (!$caretaker || (method_exists($caretaker, 'isCaretaker') && !$caretaker->isCaretaker())) {
            return false;
        }

        // 2. Resolve patient ID from either route parameter OR request body
        $patient = $this->route('patient') ?? $this->input('patient_id');

        if (!$patient) {
            return false;
        }

        // Extract ID if a Model instance was injected via Route Model Binding
        $patientId = is_object($patient) ? $patient->id : $patient;

        // 3. Verify the relationship in the database
        return CaretakerPatient::where([
            'patient_id'   => $patientId,
            'caretaker_id' => $caretaker->id,
        ])->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'condition_name'  => 'required|string|max:255',
            'diagnosis_date'  => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:diagnosis_date',
            'status'          => 'required|in:active,resolved,under_observation',
            'severity'        => 'required|in:mild,moderate,severe',
            'notes'           => 'nullable|string',
            'action_taken'    => 'nullable|string',
            'diagnosed_by'    => 'nullable|string|max:255',
            'review_date'     => 'nullable|date',
        ];
    }
}
