<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;


Route::get('/users', function () {
    return response()->json(['message' => 'Hello Next.js front-end!']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::post('signup', [RegisterController::class, 'register']);
