<?php

namespace App\Http\Controllers\Caretaker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Appointment;

class CaretakerController extends Controller
{
    public function index (Request $request) {
        try {
            $user = $request->user();

            $patients = $user->patients;

            // 1. Patients without vitals updated today
            $patientsNeedingVitals = $patients->filter(function ($patient) {
                return !$patient->patient->vitals()
                    ->where('updated_at', '>=', today())
                    ->exists();
            })->count();

            
            // 2. Today's scheduled appointments
            $todayAppointments = Appointment::whereHas('caretaker', function ($query) use ($user) {
                    $query->where('caretaker_id', $user->id);
                })
                ->whereDate('date_time', today())
                ->count();

            return response()->json([
                'status' => true,
                'data' => [
                    'patients' => $patients->count(),
                    'appointments' => $todayAppointments,
                    'vitals' => $patientsNeedingVitals,
                    'today' => today()
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

    }
}
