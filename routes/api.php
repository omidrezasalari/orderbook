<?php
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/place-an-order', [OrderController::class, 'placeAnOrder']);
