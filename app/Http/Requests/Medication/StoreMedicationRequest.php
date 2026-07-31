<?php

namespace App\Http\Requests\Medication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'dosage' => ['nullable', 'string', 'max:100'],
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'medical_history_id' => ['nullable', 'exists:medical_histories,id'],
            
            // Nested schedule array validation
            'schedules' => ['required', 'array', 'min:1'],
            'schedules.*.taken_at' => [
                'required',
                Rule::in([
                    'before_breakfast', 'after_breakfast',
                    'before_lunch',     'after_lunch',
                    'before_snacks',    'after_snacks',
                    'before_dinner',    'after_dinner',
                ]),
            ],
            'schedules.*.time_for_reminder' => ['required', 'date_format:H:i:s,H:i'],
        ];
    }
}