<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\NewRelicService;

class NewRelicMiddleware
{
    private $newRelicService;

    public function __construct(NewRelicService $newRelicService)
    {
        $this->newRelicService = $newRelicService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        // Track page view
        if ($this->newRelicService->isEnabled()) {
            $this->newRelicService->trackPageView(
                $request->route()?->getName() ?? $request->path(),
                [
                    'url' => $request->url(),
                    'user_id' => $request->user()?->id,
                    'session_id' => $request->session()?->getId(),
                    'referrer' => $request->header('referer'),
                ]
            );
        }

        $response = $next($request);

        // Track API performance
        if ($this->newRelicService->isEnabled() && $request->is('api/*')) {
            $duration = microtime(true) - $startTime;
            
            $this->newRelicService->trackAPIPerformance(
                $request->path(),
                $request->method(),
                $response->getStatusCode(),
                $duration,
                [
                    'user_id' => $request->user()?->id,
                    'response_size' => strlen($response->getContent()),
                    'cache_hit' => false, // Could be enhanced with cache detection
                ]
            );
        }

        return $response;
    }
}
 







