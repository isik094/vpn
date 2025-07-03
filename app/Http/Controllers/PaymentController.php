<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatusEnum;
use App\Http\Requests\PaymentCallbackRequest;
use App\Models\Payment;
use App\Services\OutlineVpnService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;

/**
 * Контроллер для платежей
 *
 * @class PaymentController
 */
class PaymentController extends Controller
{
    /**
     * Принять коллбэк от платежного шлюза WATA
     *
     * @param PaymentCallbackRequest $request
     * @return ResponseFactory|Application|Response|object
     */
    public function callback(PaymentCallbackRequest $request)
    {
        try {
            $data = $request->validated();
            /** @var Payment $payment */
            $payment = Payment::find($data['orderId']);

            if ($payment === null) {
                logger()->error('Payment not found');
                return response(status: 404);
            }

            if ($payment->isPaid() || $payment->isDeclined()) {
                logger()->error('Payment has been processed');
                throw new \Exception('Payment has been processed');
            }

            $payment->setCallbackData($data);
            $payment->setPaymentTime($data['paymentTime']);
            $payment->setStatus(PaymentStatusEnum::getEnumObject($data['transactionStatus']));

            if (!$payment->save()) {
                logger()->error('Payment failed to save');
                throw new \Exception("Payment failed to save");
            }

            if ($payment->isPaid()) {
                $outlineVpnService = new OutlineVpnService();
                $vpnKey = $payment->vpnKey()->exists()
                    ? $payment->vpnKey
                    : $outlineVpnService->createKey($payment->chat_id);

                if ($vpnKey === null) {
                    logger()->error('Failed to create key');
                    throw new \Exception("Failed to create key");
                }

                $vpnKey->setExpiredAt($payment->tariff, $data['paymentTime']);

                if (!$vpnKey->save()) {
                    logger()->error('Failed to save vpn');
                    throw new \Exception("Failed to save vpn");
                }

                $message = $outlineVpnService->getMessage($vpnKey->accessUrl, $payment->id, (bool) $payment->vpn_key_id);
                $payment->chat->message($message)->send();
            }

            return response(status: 200);
        } catch (\Exception $e) {
            logger()->error('Payment Callback Error: ' . $e->getMessage());
            return response(status: 500);
        } catch (GuzzleException $e) {
            logger()->error('Payment Callback Error: ' . $e->getMessage());
            return response(status: 500);
        }
    }
}
