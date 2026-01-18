<?php

use App\Http\Controllers\Api\ClientController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/{client}/websites', [ClientController::class, 'websites']);
});
