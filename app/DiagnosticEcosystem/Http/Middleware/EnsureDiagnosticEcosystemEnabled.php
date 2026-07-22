<?php

namespace App\DiagnosticEcosystem\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDiagnosticEcosystemEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('diagnostic-ecosystem.enabled', false)) {
            return response()->json([
                'success' => false,
                'message' => 'Diagnostic Ecosystem module is not enabled.',
            ], 503);
        }

        return $next($request);
    }
}
