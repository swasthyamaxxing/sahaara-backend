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

            $appointments = Appointment::where('patient_id', $patient->id)->get();

            return response()->json([
                'status' => true,
                'data' => $appointments,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Unable to retrieve appointments.',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    public function create()
    {
        //
    }

    // Store a new appointment
    public function store(StoreAppointmentRequest $request)
    {
        $request->validated();

        try {

            $appointment = Appointment::create([
                'patient_id' => $request->patient_id,
                'institution_name' => $request->institution_name,
                'doctor_name' => $request->doctor_name,
                'date_time' => $request->date_time,
                'presenting_problem' => $request->presenting_problem,
                'prescription' => $request->prescription,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Appointment created successfully.',
                'data' => $appointment,
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Unable to create appointment.',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    // Display one appointment
    public function show(Appointment $appointment)
    {
        return response()->json([
            'status' => true,
            'data' => $appointment,
        ], 200);
    }

    public function edit(Appointment $appointment)
    {
        //
    }

    // Update an appointment
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $request->validated();

        try {

            $appointment->update([
                'institution_name' => $request->institution_name,
                'doctor_name' => $request->doctor_name,
                'date_time' => $request->date_time,
                'presenting_problem' => $request->presenting_problem,
                'prescription' => $request->prescription,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Appointment updated successfully.',
                'data' => $appointment,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Unable to update appointment.',
                'error' => $e->getMessage(),
            ], 500);

        }
    }

    // Delete an appointment
    public function destroy(Appointment $appointment)
    {
        try {

            $appointment->delete();

            return response()->json([
                'status' => true,
                'message' => 'Appointment deleted successfully.',
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Unable to delete appointment.',
                'error' => $e->getMessage(),
            ], 500);

        }
    }
}