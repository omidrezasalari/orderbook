<?php

use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/v1/place-an-order', [OrderController::class, 'placeAnOrder']);
Route::get('/v1/news', [NewsController::class, 'index']);
