<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/freekassa/notify', [PaymentController::class, 'handleNotification'])
    ->name('freekassa.notify');

Route::get('/freekassa/success', [PaymentController::class, 'success'])
    ->name('freekassa.success');

Route::get('/freekassa/failure', [PaymentController::class, 'failure'])
    ->name('freekassa.failure');
