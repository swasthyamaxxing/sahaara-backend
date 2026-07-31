<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Models\User;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    // Display all appointments for a patient
    public function index(Request $request, User $patient)
    {
        try {
            if (! $request->user()->canAccessPatientData($patient)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You do not have permission to access these records.',
                ], 403);
            }

            $appointments = Appointment::where('patient_id', $patient->id)->paginate(20);

            return response()->json([
                'status'  => true,
                'message' => 'Successfully retrieved medical appointments',
                'data'    => $appointments,
                'count'   => $appointments->count(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Unable to retrieve appointments.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Store a new appointment
    public function store(StoreAppointmentRequest $request, User $patient)
    {
        try {
            $appointment = Appointment::create(array_merge($request->validated(), [
                'patient_id'   => $patient->id,
                'caretaker_id' => $request->user()->id,
            ]));

            return response()->json([
                'status'  => true,
                'message' => 'Appointment created successfully.',
                'data'    => $appointment,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Unable to create appointment.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Display one appointment
    public function show(Request $request, User $patient, Appointment $appointment)
    {
        try {
            if ($appointment->patient_id !== $patient->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Appointment record does not belong to the specified patient.',
                ], 404);
            }

            if (! $request->user()->canAccessPatientData($patient)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You do not have permission to access these records.',
                ], 403);
            }

            $appointment->load(['patient', 'caretaker']);

            return response()->json([
                'status'  => true,
                'message' => 'Successfully retrieved the appointment record',
                'data'    => $appointment,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Unable to retrieve appointment details.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Update an appointment
    public function update(UpdateAppointmentRequest $request, User $patient, Appointment $appointment)
    {
        try {
            if ($appointment->patient_id !== $patient->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Appointment record does not belong to the specified patient.',
                ], 404);
            }

            $appointment->update($request->validated());

            return response()->json([
                'status'  => true,
                'message' => 'Appointment updated successfully.',
                'data'    => $appointment,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Unable to update appointment.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Delete an appointment
    public function destroy(Request $request, User $patient, Appointment $appointment)
    {
        try {
            if ($appointment->patient_id !== $patient->id) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Appointment record does not belong to the specified patient.',
                ], 404);
            }

            if (! $request->user()->isCaretaker()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You must be a caretaker to perform this action.',
                ], 403);
            }

            if (! $request->user()->canAccessPatientData($patient)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You do not have permission to access these records.',
                ], 403);
            }

            $appointment->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Appointment deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Unable to delete appointment.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function institutions(Request $request, User $patient)
    {
        try {
            if (! $request->user()->canAccessPatientData($patient)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You do not have permission to access these records.',
                ], 403);
            }

            $initials = $request->input('institution');

            $institutionNames = Appointment::where('patient_id', $patient->id)
                ->when($initials, fn ($query, $initials) => $query->where('institution_name', 'like', '%' . $initials . '%'))
                ->distinct()
                ->pluck('institution_name');

            return response()->json([
                'status'  => true,
                'message' => 'Institution names returned successfully',
                'data'    => $institutionNames,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An unknown error occurred',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function doctors(Request $request, User $patient)
    {
        try {
            if (! $request->user()->canAccessPatientData($patient)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You do not have permission to access these records.',
                ], 403);
            }

            $initials = $request->input('doctor');

            $doctorNames = Appointment::where('patient_id', 1)
                ->when($initials, fn ($query, $initials) => $query->where('doctor_name', 'like', '%' . $initials . '%'))
                ->distinct()
                ->pluck('doctor_name');

            return response()->json([
                'status'  => true,
                'message' => 'Doctor names returned successfully',
                'data'    => $doctorNames,
                'count' => count($doctorNames),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An unknown error occurred',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}