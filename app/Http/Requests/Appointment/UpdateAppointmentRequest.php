<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use App\Models\CaretakerPatient;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $caretaker = $this->user();

        // 1. Ensure logged in user is a caretaker
        if (! $caretaker || (method_exists($caretaker, 'isCaretaker') && ! $caretaker->isCaretaker())) {
            return false;
        }

        // 2. Resolve patient from route parameter
        $patient = $this->route('patient');

        if (! $patient) {
            return false;
        }

        $patientId = is_object($patient) ? $patient->id : $patient;

        // 3. Ensure caretaker is assigned to this patient
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
            'institution_name' => 'required|string|max:255',
            'doctor_name' => 'required|string|max:255',
            'date_time' => 'required|date_format:Y-m-d H:i:s',
            'presenting_problem' => 'nullable|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}