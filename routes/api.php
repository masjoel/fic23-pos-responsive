<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IpaymuController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/list-payment', [IpaymuController::class, 'listPayment']);
Route::get('/list-transaction', [IpaymuController::class, 'listTransaction']);