<?php

use App\Http\Controllers\MasterData\CoworkingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->group(function () {
        Route::prefix('auth')
            ->group(function () {
                Route::post(
                    'login',
                    [App\Http\Controllers\Auth\AuthController::class, 'login']
                );
                Route::middleware('auth:api')
                    ->group(function () {
                        Route::get(
                            'profile',
                            [App\Http\Controllers\Auth\AuthController::class, 'profile']
                        );
                        Route::post(
                            'logout',
                            [App\Http\Controllers\Auth\AuthController::class, 'logout']
                        );
                    });
            });
        Route::prefix('master-data')
            ->middleware(['auth:api', 'role:admin'])
            ->group(function () {
                Route::resource(
                    'coworkings',
                    CoworkingController::class
                );
            });
    });
