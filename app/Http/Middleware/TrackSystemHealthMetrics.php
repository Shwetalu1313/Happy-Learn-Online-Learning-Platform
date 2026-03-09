<?php

namespace App\Http\Middleware;

use App\Services\SystemHealthMetricsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackSystemHealthMetrics
{
    public function __construct(private SystemHealthMetricsService $systemHealthMetricsService) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('_request_started_at', microtime(true));

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $startedAt = (float) $request->attributes->get('_request_started_at', microtime(true));
        $durationMs = (int) round(max(0, (microtime(true) - $startedAt) * 1000));

        $this->systemHealthMetricsService->recordRequest(
            $request,
            $response->getStatusCode(),
            $durationMs
        );
    }
}
