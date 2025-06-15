<?php

namespace App\Http\Middleware;

use App\helpers\StrHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

/**
 * Проверка подписи
 *
 * @class VerifyWataSignature
 */
class VerifyWataSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('VerifyWataSignature running');
        $rawPayload = $request->getContent();
        $signature = $request->header('X-Signature');

        if (StrHelper::isEmpty($signature) || StrHelper::isEmpty($rawPayload)) {
            return response(status: 400);
        }

        $publicKey = Cache::remember('wata_public_key', now()->addHour(), function () {
            $response = Http::get('https://api.wata.pro/api/h2h/public-key');

            if ($response->successful()) {
                $data = $response->json();
                return $data['value'] ?? null;
            }

            return null;
        });

        if (StrHelper::isEmpty($publicKey)) {
            return response(status: 500);
        }

        if (!$this->verifySignature($rawPayload, $signature, $publicKey)) {
            logger()->warning('Invalid WATA webhook signature', [
                'ip' => $request->ip(),
                'signature' => $signature,
            ]);

            return response(status: 403);
        }

        return $next($request);
    }

    /**
     * Верификация подписи
     *
     * @param string $rawWebhookJson
     * @param string $signature
     * @param string $publicKey
     * @return bool
     */
    private function verifySignature(string $rawWebhookJson, string $signature, string $publicKey): bool
    {
        $publicKeyResource = openssl_pkey_get_public($publicKey);
        if ($publicKeyResource === false) {
            logger()->error('Failed to get public key resource');
            return false;
        }

        $signatureBytes = base64_decode($signature);
        $result = openssl_verify($rawWebhookJson, $signatureBytes, $publicKeyResource, OPENSSL_ALGO_SHA512);

        return $result === 1;
    }
}
