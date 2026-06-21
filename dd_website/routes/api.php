<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IotController;

Route::post('/sensor', [IotController::class, 'store']);
Route::get('/sensor/latest', [IotController::class, 'latest']);
Route::post('/baseline/reset',   [IotController::class, 'resetBaseline']);
