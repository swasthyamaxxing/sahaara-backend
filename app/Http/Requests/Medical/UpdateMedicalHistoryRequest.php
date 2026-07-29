<?php

namespace App\Http\Requests\Medical;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\CaretakerPatient;

class UpdateMedicalHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $caretaker = $this->user();
        
        // Retrieve route parameters (returns model instance if using Implicit Binding)
        $patient = $this->route('patient');
        $medicalHistory = $this->route('medicalHistory');

        // 1. Verify caretaker manages the patient
        $isCaretaker = CaretakerPatient::where([
            'patient_id' => is_object($patient) ? $patient->id : $patient,
            'caretaker_id' => $caretaker->id,
        ])->exists();

        if (!$isCaretaker) {
            return false;
        }

        // 2. Verify the medical history record belongs to this patient
        if ($medicalHistory && is_object($medicalHistory)) {
            return $medicalHistory->patient_id == (is_object($patient) ? $patient->id : $patient);
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'condition_name'  => 'sometimes|string|max:255',
            'diagnosis_date'  => 'sometimes|date',
            'end_date'        => 'nullable|date|after_or_equal:diagnosis_date',
            'status'          => 'sometimes|in:active,resolved,under_observation',
            'severity'        => 'sometimes|in:mild,moderate,severe',
            'notes'           => 'nullable|string',
            'action_taken'    => 'nullable|string',
            'diagnosed_by'    => 'nullable|string|max:255',
            'review_date'     => 'nullable|date',
        ];
    }
}
