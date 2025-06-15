<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Проверка IP адресов от кого пришел Webhook WATA
 *
 * @class CheckWebhookIp
 */
class CheckWebhookIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = ['62.84.126.140', '51.250.106.150'];

        if (!in_array($request->ip(), $allowedIps)) {
            return response(status: 403);
        }

        return $next($request);
    }
}
