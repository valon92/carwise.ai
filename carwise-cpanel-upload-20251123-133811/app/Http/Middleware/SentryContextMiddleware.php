<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SentryService;
use Sentry\State\Scope;
use Sentry\Laravel\Facade as Sentry;

class SentryContextMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Initialize Sentry context
        Sentry::configureScope(function (Scope $scope) use ($request): void {
            // Add request context
            $scope->setContext('request', [
                'url' => $request->url(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referer' => $request->header('referer'),
                'content_type' => $request->header('content-type'),
                'accept' => $request->header('accept'),
            ]);

            // Add user context if authenticated
            if ($request->user()) {
                $scope->setUser([
                    'id' => $request->user()->id,
                    'email' => $request->user()->email,
                    'username' => $request->user()->name,
                ]);
            }

            // Add additional context
            $scope->setTag('route', $request->route()?->getName() ?? 'unknown');
            $scope->setTag('method', $request->method());
        });

        // Add breadcrumb for the request
        Sentry::addBreadcrumb(
            new \Sentry\Breadcrumb(
                \Sentry\Breadcrumb::LEVEL_INFO,
                \Sentry\Breadcrumb::TYPE_HTTP,
                'http',
                "{$request->method()} {$request->path()}",
                [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                ]
            )
        );

        $response = $next($request);

        // Update breadcrumb with response status
        Sentry::addBreadcrumb(
            new \Sentry\Breadcrumb(
                $response->getStatusCode() >= 400 ? \Sentry\Breadcrumb::LEVEL_ERROR : \Sentry\Breadcrumb::LEVEL_INFO,
                \Sentry\Breadcrumb::TYPE_HTTP,
                'http',
                "{$request->method()} {$request->path()} - {$response->getStatusCode()}",
                [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'status_code' => $response->getStatusCode(),
                ]
            )
        );

        return $response;
    }
}