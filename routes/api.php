<?php

use App\Http\Controllers\MasterData\CoworkingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->group(function () {
        Route::prefix('master-data')
            ->group(function () {
                Route::resource(
                    'coworkings',
                    CoworkingController::class
                );
            });
    });
