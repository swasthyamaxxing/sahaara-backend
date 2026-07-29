<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends FormRequest
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
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'age' => [ 'required', 'integer', 'between:0,120' ],
            'password' => [
                'required', 
                'string', 
                Password::min(8)->letters()->mixedCase()->numbers(),
            ],
            'confirmPassword' => [
                'required',
                'same:password'
            ],
            'gender' => [ 'required', 'string', Rule::in(['male', 'female', 'other', 'unspecified']) ],
            'role' => ['required', Rule::in(['patient', 'caretaker'])],
        ];
    }
}
