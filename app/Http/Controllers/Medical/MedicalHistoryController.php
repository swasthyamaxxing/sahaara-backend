<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\MedicalHistory;
use App\Models\User;
use App\Models\CaretakerPatient;

use Illuminate\Http\Request;
use App\Http\Requests\Medical\StoreMedicalHistoryRequest;
use App\Http\Requests\Medical\UpdateMedicalHistoryRequest;

class MedicalHistoryController extends Controller
{
    public function index(Request $request, User $patient)
    {

        $user = $request->user();


        // Add validation so that only the caretaker and the patient can view the records

        try {
            $records = MedicalHistory::query()
                        ->with('patient', 'caretaker')
                        ->where('patient_id', $patient->id)
                        ->search($request->query('search'))
                        ->withStatus($request->query('status'))
                        ->withSeverity($request->query('severity'))
                        ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Fetched patient medical history successfully',
                'data' => $records
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An unknown error occurred',
                'data' => [],
            ], 500);
        }
    }

    public function store(StoreMedicalHistoryRequest $request, User $patient)
    {
        $caretaker = $request->user();

        $data = $request->validated();

        try {
            $data['patient_id'] = $patient->id;
            $data['caretaker_id'] = $caretaker->id;
            $medicalHistory = MedicalHistory::create($data);

            $medicalHistory->load(['patient', 'caretaker']);

            return response()->json($medicalHistory, 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function show(Request $request, User $patient, MedicalHistory $medicalHistory)
    {
        $user = $request->user();

        if (
            ($user->isCaretaker() && ($user->id == $medicalHistory->caretaker_id))
            ||
            ($user->isPatient() && ($user->id == $medicalHistory->patient_id))
        ) {

            $medicalHistory->load(['patient', 'caretaker']);

            return response()->json([
                'status' => true,
                'message' => 'Successfully retrieved medical history',
                'data' => $medicalHistory
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'You are not authorized to view this record'
        ], 403);

    }

    public function update(UpdateMedicalHistoryRequest $request, User $patient, MedicalHistory $medicalHistory)
    {
        $data = $request->validated();

        try {
            $medicalHistory->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Record updated successfully',
                'data' => $medicalHistory
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An unkown error occured',
            ], 500);
        }
    }

    public function destroy(Request $request, User $patient, MedicalHistory $medicalHistory)
    {
        $user = $request->user();

        // 1. Verify the medical history record belongs to this patient route resource
        if ($medicalHistory->patient_id !== $patient->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Medical history record does not belong to the specified patient.',
            ], 404);
        }

        // 2. Authorization: Ensure user is a caretaker AND either created the record
        // OR is actively assigned to this patient
        $isRecordCreator = ($medicalHistory->caretaker_id === $user->id);
        $isAssignedCaretaker = $user->isCaretaker() && CaretakerPatient::where([
            'patient_id'   => $patient->id,
            'caretaker_id' => $user->id,
        ])->exists();

        if (! $isRecordCreator && ! $isAssignedCaretaker) {
            return response()->json([
                'status'  => false,
                'message' => 'You do not have permission to delete this record.',
            ], 403);
        }

        try {
            $medicalHistory->delete();

            // Option A: 200 OK with message (Recommended if frontend expects JSON)
            return response()->json([
                'status'  => true,
                'message' => 'Record deleted successfully.',
            ], 200);

            // Option B: Standard 204 No Content (uncomment if preferred)
            // return response()->noContent();

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An unknown error occurred',
            ], 500);
        }
    }
}