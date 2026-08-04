<?php

namespace App\Http\Controllers\Caretaker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PatientResource;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\CaretakerPatient;

class CaretakerPatientController extends Controller
{
    public function get_patients (Request $request) {

        $user = $request->user();

        if (! $user->isCaretaker()) {
            return response()->json([
                'status' => false,
                'message' => 'Only caretakers can have patients',
                'count' => -1,
            ], 403); 
        }

        $patients = $user->patients()->with('patient')->get();

        return response()->json([
            'status' => true,
            'message' => 'Patients retrieved successfully',
            'count' => $patients->count(),
            'data' => PatientResource::collection($patients)
        ], 200);

    }

    /**
     * The request must be initiated by the caretaker's app
     * Create the patient's account
     * Link the patient here
     * 
     */
    public function create_patient (Request $request, User $caretaker) {
        $caretaker = $request->user();

        if ( ! $caretaker->isCaretaker()  ) {
            return response()->json([
                'status' => false,
                'message' => 'Only caretakers can add patients',
            ], 403);
        }
        /**
         * The caretaker fills a form with details of the patients
         * fullName,
         * email,
         * age,
         * gender,
         * password,
         * As these accounts do not have much capabilities, we suggest the password to be numeric
        */ 
        $validated = $request->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'age' => [ 'required', 'integer', 'between:0,120' ],
            'password' => [
                'required', 
                'string', 
                Password::min(8),
            ],
            'gender' => [ 'required', 'string', Rule::in(['male', 'female', 'other', 'unspecified']) ],
        ]);
                
        try {
            $patient = DB::transaction(function () use ($validated, $caretaker) {
                
                $patient = User::create([
                    'name' => $validated['fullName'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'age' => $validated['age'],
                    'gender' => $validated['gender'],
                    'role' => 'patient',
                ]);

                CaretakerPatient::create([
                    'patient_id' => $patient->id,
                    'caretaker_id' => $caretaker->id,
                ]);

                return $patient;
            });



            return response()->json([
                'status' => true,
                'message' => "Successfully created the patient's account",
                'patient' => $patient,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e,
            ], 500);
        }

    }

    /**
     * When you scan a qr code, but this requires a separate controller
     */
    public function link_caretaker_patient() {

    }

   /**
     * Can only be initiated by the caretaker
     */
    public function unlink_caretaker_patient(Request $request, User $patient)
    {
        try {
            $caretaker = $request->user();

            if (! $caretaker->isCaretaker()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please request your caretaker for the action.',
                ], 403);
            }

            $record = CaretakerPatient::where([
                'caretaker_id' => $caretaker->id,
                'patient_id'   => $patient->id,
            ])->first();

            if ($record) {
                $record->delete();

                return response()->json([
                    'status'  => true,
                    'message' => 'Patient unlink successful',
                ]);
            }

            return response()->json([
                'status'  => false,
                'message' => 'Record not found in the database',
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred while unlinking the patient.',
            ], 500);
        }
    }
}
