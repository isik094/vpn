<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\CheckWebhookIp;
use App\Http\Middleware\VerifyWataSignature;

Route::post('/payment/callback', [PaymentController::class, 'callback']);
//    ->middleware(CheckWebhookIp::class)
//    ->middleware(VerifyWataSignature::class);

