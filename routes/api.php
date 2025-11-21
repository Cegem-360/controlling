<?php

declare(strict_types=1);

use App\Http\Controllers\Api\SyncPasswordController;
use App\Http\Controllers\Api\UserSyncController;
use Illuminate\Support\Facades\Route;

// Route::post('/sync-password', SyncPasswordController::class);

Route::middleware('api.key')->group(function () {
    Route::post('/sync-password', SyncPasswordController::class);
    Route::post('/create-user', [UserSyncController::class, 'create']);
    Route::post('/sync-user', [UserSyncController::class, 'sync']);
});
