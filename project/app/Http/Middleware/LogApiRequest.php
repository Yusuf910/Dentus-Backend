<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
class LogApiRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('API Request:', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'input' => $request->all(),
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
        ]);

        return $next($request);
    }
}
