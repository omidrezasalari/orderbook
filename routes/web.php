<?php

use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/orders', [OrderController::class, 'placeOrder']);
