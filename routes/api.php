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

use App\Http\Controllers\Caretaker\CaretakerPatientController;




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

Route::middleware('auth:api')->get('/patients', [CaretakerPatientController::class, 'get_patients']);

Route::post('signup', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    Route::resource('patients.medical-history', MedicalHistoryController::class);
});