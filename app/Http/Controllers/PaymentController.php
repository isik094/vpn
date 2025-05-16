<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function handleNotification(Request $request)
    {
        $merchantId = config('freekassa.merchant_id');
        $secretKey = config('freekassa.secret_key_2');

        $sign = md5($merchantId . ':' . $request->amount . ':' . $secretKey . ':' . $request->merchantOrderId);

        if ($sign !== $request->sign) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        PaymentService::markAsPaid($request->merchantOrderId);

        return response('YES', 200);
    }

    // Перенаправление после успешной оплаты (GET)
    public function success(Request $request)
    {
        $orderId = $request->input('merchantOrderId');
    }

    // Перенаправление при отказе (GET)
    public function failure(Request $request)
    {
        $orderId = $request->input('merchantOrderId');
    }
}
