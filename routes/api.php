<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function (): void {
    Route::post('/login', [AuthTokenController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthTokenController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            abort_unless(config('starter.modules.api'), 404);
            abort_unless($request->user()->tokenCan('user:read'), 403);

            return $request->user();
        });

        Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);
    });
});
