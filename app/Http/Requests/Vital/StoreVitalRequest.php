<?php

namespace App\Http\Requests\Vital;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use App\Models\CaretakerPatient;

class StoreVitalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        $patient_id = $this->input('patient_id');

        // Validate that the two are related by a caretaker-patient relationship in our system
        return CaretakerPatient::where([
            'patient_id' => $patient_id,
            'caretaker_id' => $user->id,
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
            'patient_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'vitals' => 'array|required'
        ];
    }
}
