<?php

namespace App\Http\Controllers\Medication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Medication;

use App\Http\Resources\MedicationResource;
use App\Http\Requests\Medication\StoreMedicationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, User $patient)
    {
        $medications = Medication::where('patient_id', $patient->id)->where('is_active', 1)->with('schedules', 'appointment', 'medicalHistory')->get();

        return response()->json([
            'status' => true,
            'message' => 'Medications retrieved successfully',
            'data' => $medications
        ], 200);
    }

    /**
     * Store a newly created medication with its schedules.
     */
    public function store(StoreMedicationRequest $request, User $patient): JsonResponse
    {
        $validated = $request->validated();

        $medication = DB::transaction(function () use ($patient, $validated) {
            // 1. Create main medication record
            $med = $patient->medications()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'dosage' => $validated['dosage'] ?? null,
                'appointment_id' => $validated['appointment_id'] ?? null,
                'medical_history_id' => $validated['medical_history_id'] ?? null,
                'is_active' => true,
            ]);

            // 2. Bulk insert schedule records
            $med->schedules()->createMany($validated['schedules']);

            return $med->load('schedules');
        });

        return response()->json([
            'status' => true,
            'message' => 'Medication and schedules created successfully',
            'data' => new MedicationResource($medication),
        ], 201);
    }

    /**
     * Display a specific medication.
     */
    public function show(User $patient, Medication $medication): JsonResponse
    {
        // Ensure medication belongs to the specified patient
        abort_if($medication->patient_id !== $patient->id, 404);

        return response()->json([
            'status' => true,
            'data' => new MedicationResource($medication->load('schedules')),
        ]);
    }

    /**
     * Update an existing medication and replace/sync its schedules.
     */
    public function update(StoreMedicationRequest $request, User $patient, Medication $medication): JsonResponse
    {
        abort_if($medication->patient_id !== $patient->id, 404);

        $validated = $request->validated();

        DB::transaction(function () use ($medication, $validated) {
            // 1. Update basic info
            $medication->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'dosage' => $validated['dosage'] ?? null,
                'appointment_id' => $validated['appointment_id'] ?? null,
                'medical_history_id' => $validated['medical_history_id'] ?? null,
            ]);

            // 2. Refresh schedules (Delete existing and recreate)
            $medication->schedules()->delete();
            $medication->schedules()->createMany($validated['schedules']);
        });

        return response()->json([
            'status' => true,
            'message' => 'Medication updated successfully',
            'data' => new MedicationResource($medication->fresh('schedules')),
        ]);
    }

    /**
     * Toggle active/inactive status (Soft disable).
     */
    public function toggleStatus(User $patient, Medication $medication): JsonResponse
    {
        abort_if($medication->patient_id !== $patient->id, 404);

        $medication->update([
            'is_active' => !$medication->is_active,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Medication status updated',
            'data' => new MedicationResource($medication)
        ]);
    }

    /**
     * Delete a medication (Schedules cascade delete automatically).
     */
    public function destroy(User $patient, Medication $medication): JsonResponse
    {
        abort_if($medication->patient_id !== $patient->id, 404);

        $medication->delete();

        return response()->json([
            'status' => true,
            'message' => 'Medication deleted successfully',
        ]);
    }
}
