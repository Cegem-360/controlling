<?php

declare(strict_types=1);

use App\Http\Controllers\Api\SyncPasswordController;
use Illuminate\Support\Facades\Route;

Route::post('/sync-password', SyncPasswordController::class);
