<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\SyncController;

Route::post('/pair', [SyncController::class, 'pair']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/sync', [SyncController::class, 'sync']);
    Route::post('/lookup', [SyncController::class, 'lookup']);
});
