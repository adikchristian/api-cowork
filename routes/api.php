<?php

use App\Http\Controllers\Booking\Admin\BookingApprovalController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Booking\BookingDetailController;
use App\Http\Controllers\MasterData\CoworkingController;
use App\Http\Controllers\MasterData\CoworkPlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

if (app()->environment(['local'])) {
    Route::post('/run-artisan', function (Request $request) {
        $command = $request->input('command');

        // Jalankan perintah Artisan
        try {
            Artisan::call($command);
            $output = Artisan::output();
            return response()->json(['success' => true, 'output' => $output]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    });
}

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
                )->except(['create', 'edit']);
                Route::resource(
                    'cowork-plans',
                    CoworkPlanController::class
                )->except(['create', 'edit']);

                Route::get(
                    'cowork-plans/{coworkingId}/coworking',
                    [CoworkPlanController::class, 'showCoworking']
                );
            });
        Route::prefix('booking')
            ->middleware(['auth:api'])
            ->group(function () {
                Route::middleware('role:member')
                    ->group(function () {
                        Route::get(
                            '/',
                            [
                                BookingController::class,
                                'index'
                            ]
                        );
                        Route::get(
                            '/{code}',
                            [
                                BookingController::class,
                                'show'
                            ]
                        );
                        Route::put(
                            '/cancle/{code}',
                            [
                                BookingController::class,
                                'cancle'
                            ]
                        );
                        Route::post(
                            '/',
                            [
                                BookingController::class,
                                'store'
                            ]
                        );
                    });
            });

        Route::prefix('booking-detail')
            ->middleware(['auth:api'])
            ->group(function () {
                Route::middleware('role:member')
                    ->group(function () {
                        Route::post(
                            '/',
                            [
                                BookingDetailController::class,
                                'store'
                            ]
                        );
                    });
            });
        Route::prefix('booking-approval')
            ->middleware(['auth:api'])
            ->group(function () {
                Route::middleware('role:admin')
                    ->group(function () {
                        Route::post(
                            '/',
                            [
                                BookingApprovalController::class,
                                'store'
                            ]
                        );
                    });
            });
    });
