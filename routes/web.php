<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\CheckWebhookIp;
use App\Http\Middleware\VerifyWataSignature;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/payment/callback', [PaymentController::class, 'callback'])
    ->middleware(CheckWebhookIp::class)
    ->middleware(VerifyWataSignature::class);
