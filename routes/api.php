<?php

use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\Medical\MedicalHistoryController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;


use App\Http\Controllers\Medical\AppointmentController;
use App\Http\Controllers\Medical\VitalController;
use App\Http\Controllers\Medical\VitalLabelController;

use App\Http\Controllers\Profile\ProfileController;

use App\Http\Controllers\Caretaker\CaretakerPatientController;

use App\Http\Controllers\Medication\MedicationController;
use App\Http\Controllers\Medication\MedicationScheduleController;




Route::get('/users', function () {
    return response()->json(['message' => 'Hello Next.js front-end!']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::get('vitals/labels', [VitalLabelController::class, 'index']);

Route::prefix('vitals')->middleware('auth:api')->group(function () {
    
    Route::get('/{patient}', [VitalController::class, 'index']);

    Route::post('/store', [VitalController::class, 'store']);
});


Route::post('signup', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);


Route::middleware('auth:api')->group(function () {

    Route::get('user/{user}', [ProfileController::class, 'show']);

    // Get unique doctor, institution names
    Route::get('patients/{patient}/appointments/doctors', [AppointmentController::class, 'doctors']);
    Route::get('patients/{patient}/appointments/institutions', [AppointmentController::class, 'institutions']);

    /**
     * Appointments
     */
    Route::resource('patients.appointments', AppointmentController::class);

    /**
     * Medical History
     */
    Route::resource('patients.medical-history', MedicalHistoryController::class);

    /**
     * Medications
     */
    Route::patch('patients/{patient}/medications/{medication}/toggle', [MedicationController::class, 'toggleStatus']);
    Route::resource('patients.medications', MedicationController::class);
    Route::resource('patients.medication-schedules', MedicationScheduleController::class);

    /**
     * Link/unlink Caretaker and Patient
     */
    Route::middleware('auth:api')->get('caretaker/{caretaker}/patients', [CaretakerPatientController::class, 'get_patients']);
    Route::middleware('auth:api')->post('caretaker/{caretaker}/patients', [CaretakerPatientController::class, 'create_patient']);
});
