<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'patient_id' => 'required|exists:users,id',
            'institution_name' => 'required|string|max:255',
            'doctor_name' => 'required|string|max:255',
            'date_time' => 'required|date_format:Y-m-d H:i:s',
            'presenting_problem' => 'nullable|string',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }
}
