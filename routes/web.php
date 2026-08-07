<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service'       => 'SaharaSewa API',
        'status'        => 'operational',
        'version'       => '1.0.0',
        // 'documentation' => 'https://xyz.com/docs',
    ]);
});