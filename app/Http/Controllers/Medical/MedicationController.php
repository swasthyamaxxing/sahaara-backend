<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medication\StoreMedicationRequest;
use App\Http\Requests\Medication\UpdateMedicationRequest;
use App\Models\Medication;
use App\Models\User;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index(Request $request, User $patient)
    {
        $medications = Medication::where('patient_id', $patient->id)
            ->where('status', 'active')
            ->latest('start_date')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $medications,
        ], 200);
    }

    public function store(StoreMedicationRequest $request)
    {
        $medication = Medication::create([
            'patient_id' => $request->patient_id,
            'medicine_name' => $request->medicine_name,
            'purpose' => $request->purpose,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'times' => $request->times,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'instructions' => $request->instructions,
            'prescribing_doctor' => $request->prescribing_doctor,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Medication created successfully.',
            'data' => $medication,
        ], 201);
    }

    public function show(Medication $medication)
    {
        return response()->json([
            'status' => true,
            'data' => $medication,
        ], 200);
    }

    public function update(UpdateMedicationRequest $request, Medication $medication)
    {
        $medication->update([
            'medicine_name' => $request->medicine_name,
            'purpose' => $request->purpose,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'times' => $request->times,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'instructions' => $request->instructions,
            'prescribing_doctor' => $request->prescribing_doctor,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Medication updated successfully.',
            'data' => $medication,
        ], 200);
    }

    public function destroy(Medication $medication)
    {
        $medication->delete();

        return response()->json([
            'status' => true,
            'message' => 'Medication deleted successfully.',
        ], 200);
    }
}
