<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Medical\AppointmentController;
use App\Http\Controllers\Medical\MedicationController;
use App\Http\Controllers\Medical\VitalController;
use App\Http\Controllers\Medical\VitalLabelController;


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

Route::prefix('appointments')->group(function () {
    Route::get('/{patient}', [AppointmentController::class, 'index']);
    Route::post('/store', [AppointmentController::class, 'store']);
    Route::get('/show/{appointment}', [AppointmentController::class, 'show']);
    Route::put('/{appointment}', [AppointmentController::class, 'update']);
    Route::delete('/{appointment}', [AppointmentController::class, 'destroy']);
});

Route::prefix('medications')->group(function () {
    Route::get('/{patient}', [MedicationController::class, 'index']);
    Route::post('/store', [MedicationController::class, 'store']);
    Route::get('/show/{medication}', [MedicationController::class, 'show']);
    Route::put('/{medication}', [MedicationController::class, 'update']);
    Route::delete('/{medication}', [MedicationController::class, 'destroy']);
});

Route::post('signup', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);