<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\MedicalHistory;
use App\Models\User;
use Illuminate\Http\Request;

class MedicalHistoryController extends Controller
{
    // GET /api/patients/{patient}/medical-histories
    public function index(Request $request, User $patient)
    {
        $query = MedicalHistory::where('patient_id', $patient->id);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('condition_name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('action_taken', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($severity = $request->query('severity')) {
            $query->where('severity', $severity);
        }

        return response()->json(
            $query->orderByDesc('diagnosis_date')->get()
        );
    }

    // POST /api/patients/{patient}/medical-histories
    public function store(Request $request, User $patient)
    {
        $data = $request->validate([
            'condition_name'  => 'required|string|max:255',
            'diagnosis_date'  => 'required|date',
            'end_date'        => 'nullable|date|after_or_equal:diagnosis_date',
            'status'          => 'required|in:active,resolved,under_observation',
            'severity'        => 'required|in:mild,moderate,severe',
            'notes'           => 'nullable|string',
            'action_taken'    => 'nullable|string',
            'diagnosed_by'    => 'nullable|string|max:255',
            'review_date'     => 'nullable|date',
        ]);

        $data['patient_id'] = $patient->id;
        $data['caregiver_id'] = $request->user()->id; // logged-in caregiver, never trust frontend for this

        $medicalHistory = MedicalHistory::create($data);

        return response()->json($medicalHistory, 201);
    }

    // GET /api/medical-histories/{medicalHistory}
    public function show(MedicalHistory $medicalHistory)
    {
        return response()->json($medicalHistory);
    }

    // PUT/PATCH /api/medical-histories/{medicalHistory}
    public function update(Request $request, MedicalHistory $medicalHistory)
    {
        $data = $request->validate([
            'condition_name'  => 'sometimes|string|max:255',
            'diagnosis_date'  => 'sometimes|date',
            'end_date'        => 'nullable|date|after_or_equal:diagnosis_date',
            'status'          => 'sometimes|in:active,resolved,under_observation',
            'severity'        => 'sometimes|in:mild,moderate,severe',
            'notes'           => 'nullable|string',
            'action_taken'    => 'nullable|string',
            'diagnosed_by'    => 'nullable|string|max:255',
            'review_date'     => 'nullable|date',
        ]);

        $medicalHistory->update($data);

        return response()->json($medicalHistory);
    }

    // DELETE /api/medical-histories/{medicalHistory}
    public function destroy(MedicalHistory $medicalHistory)
    {
        $medicalHistory->delete();

        return response()->json(['message' => 'Record deleted']);
    }
}