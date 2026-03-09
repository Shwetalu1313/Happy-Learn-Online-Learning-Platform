<?php

namespace App\Services;

use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Throwable;

class SystemHealthMetricsService
{
    public function recordRequest(Request $request, int $statusCode, int $durationMs): void
    {
        if ($this->shouldSkip($request)) {
            return;
        }

        $scope = $this->resolveScope($request);
        $minuteBucket = now()->format('YmdHi');
        $hourBucket = now()->format('YmdH');
        $expiresMinute = now()->addHours(4);
        $expiresHour = now()->addDays(2);
        $isServerError = $statusCode >= 500 ? 1 : 0;

        foreach (['all', $scope] as $bucketScope) {
            $this->incrementCounter($this->key('minute', $minuteBucket, $bucketScope, 'requests'), 1, $expiresMinute);
            $this->incrementCounter($this->key('minute', $minuteBucket, $bucketScope, 'duration_ms'), $durationMs, $expiresMinute);
            $this->incrementCounter($this->key('minute', $minuteBucket, $bucketScope, 'errors'), $isServerError, $expiresMinute);

            $this->incrementCounter($this->key('hour', $hourBucket, $bucketScope, 'requests'), 1, $expiresHour);
            $this->incrementCounter($this->key('hour', $hourBucket, $bucketScope, 'duration_ms'), $durationMs, $expiresHour);
            $this->incrementCounter($this->key('hour', $hourBucket, $bucketScope, 'errors'), $isServerError, $expiresHour);
        }

        if (! Cache::has('system_health:started_at')) {
            Cache::put('system_health:started_at', now()->toDateTimeString(), now()->addDays(30));
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function snapshot(): array
    {
        $minuteAll = $this->collectMinuteWindow(60, 'all');
        $minuteApi = $this->collectMinuteWindow(60, 'api');
        $minuteWeb = $this->collectMinuteWindow(60, 'web');
        $hourAll = $this->collectHourWindow(24, 'all');

        $minuteCurrentRequests = (int) ($minuteAll['requests'][array_key_last($minuteAll['requests'])] ?? 0);
        $minuteCurrentDuration = (int) ($minuteAll['duration_ms'][array_key_last($minuteAll['duration_ms'])] ?? 0);

        $requestsHour = array_sum($minuteAll['requests']);
        $durationHour = array_sum($minuteAll['duration_ms']);
        $errorsHour = array_sum($minuteAll['errors']);

        $apiMinuteRequests = (int) ($minuteApi['requests'][array_key_last($minuteApi['requests'])] ?? 0);
        $webMinuteRequests = (int) ($minuteWeb['requests'][array_key_last($minuteWeb['requests'])] ?? 0);
        $apiHourRequests = array_sum($minuteApi['requests']);
        $webHourRequests = array_sum($minuteWeb['requests']);

        return [
            'generated_at' => now()->toDateTimeString(),
            'started_at' => Cache::get('system_health:started_at'),
            'traffic' => [
                'responses_per_minute' => $minuteCurrentRequests,
                'responses_per_hour' => $requestsHour,
                'api_responses_per_minute' => $apiMinuteRequests,
                'api_responses_per_hour' => $apiHourRequests,
                'web_responses_per_minute' => $webMinuteRequests,
                'web_responses_per_hour' => $webHourRequests,
                'avg_response_ms_1m' => $minuteCurrentRequests > 0 ? round($minuteCurrentDuration / $minuteCurrentRequests, 2) : 0.0,
                'avg_response_ms_1h' => $requestsHour > 0 ? round($durationHour / $requestsHour, 2) : 0.0,
                'server_errors_1h' => $errorsHour,
                'error_rate_1h' => $requestsHour > 0 ? round(($errorsHour / $requestsHour) * 100, 2) : 0.0,
            ],
            'database' => $this->databaseHealth(),
            'server' => $this->serverHealth(),
            'container' => $this->containerHealth(),
            'routes' => $this->routeOverview(),
            'charts' => [
                'minute_labels' => $minuteAll['labels'],
                'minute_requests' => $minuteAll['requests'],
                'minute_api_requests' => $minuteApi['requests'],
                'minute_avg_response_ms' => $this->computeAverageSeries($minuteAll['requests'], $minuteAll['duration_ms']),
                'hour_labels' => $hourAll['labels'],
                'hour_requests' => $hourAll['requests'],
            ],
        ];
    }

    /**
     * @return array{labels: array<int, string>, requests: array<int, int>, duration_ms: array<int, int>, errors: array<int, int>}
     */
    private function collectMinuteWindow(int $minutes, string $scope): array
    {
        $labels = [];
        $requests = [];
        $durations = [];
        $errors = [];

        $cursor = now()->copy()->subMinutes($minutes - 1);
        for ($i = 0; $i < $minutes; $i++) {
            $bucket = $cursor->format('YmdHi');
            $labels[] = $cursor->format('H:i');
            $requests[] = (int) Cache::get($this->key('minute', $bucket, $scope, 'requests'), 0);
            $durations[] = (int) Cache::get($this->key('minute', $bucket, $scope, 'duration_ms'), 0);
            $errors[] = (int) Cache::get($this->key('minute', $bucket, $scope, 'errors'), 0);
            $cursor->addMinute();
        }

        return [
            'labels' => $labels,
            'requests' => $requests,
            'duration_ms' => $durations,
            'errors' => $errors,
        ];
    }

    /**
     * @return array{labels: array<int, string>, requests: array<int, int>, duration_ms: array<int, int>, errors: array<int, int>}
     */
    private function collectHourWindow(int $hours, string $scope): array
    {
        $labels = [];
        $requests = [];
        $durations = [];
        $errors = [];

        $cursor = now()->copy()->subHours($hours - 1)->startOfHour();
        for ($i = 0; $i < $hours; $i++) {
            $bucket = $cursor->format('YmdH');
            $labels[] = $cursor->format('d M H:00');
            $requests[] = (int) Cache::get($this->key('hour', $bucket, $scope, 'requests'), 0);
            $durations[] = (int) Cache::get($this->key('hour', $bucket, $scope, 'duration_ms'), 0);
            $errors[] = (int) Cache::get($this->key('hour', $bucket, $scope, 'errors'), 0);
            $cursor->addHour();
        }

        return [
            'labels' => $labels,
            'requests' => $requests,
            'duration_ms' => $durations,
            'errors' => $errors,
        ];
    }

    /**
     * @param  array<int, int>  $requests
     * @param  array<int, int>  $durations
     * @return array<int, float>
     */
    private function computeAverageSeries(array $requests, array $durations): array
    {
        $averages = [];

        foreach ($requests as $index => $requestCount) {
            $duration = $durations[$index] ?? 0;
            $averages[] = $requestCount > 0 ? round($duration / $requestCount, 2) : 0.0;
        }

        return $averages;
    }

    /**
     * @return array<string, mixed>
     */
    private function databaseHealth(): array
    {
        $started = microtime(true);

        try {
            DB::select('SELECT 1');

            return [
                'status' => 'UP',
                'latency_ms' => round((microtime(true) - $started) * 1000, 2),
                'connection' => config('database.default'),
                'error' => null,
            ];
        } catch (Throwable $throwable) {
            return [
                'status' => 'DOWN',
                'latency_ms' => null,
                'connection' => config('database.default'),
                'error' => $throwable->getMessage(),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function serverHealth(): array
    {
        $loadAverages = function_exists('sys_getloadavg') ? sys_getloadavg() : false;
        $diskFree = @disk_free_space(base_path());
        $diskTotal = @disk_total_space(base_path());

        return [
            'hostname' => php_uname('n'),
            'os' => PHP_OS_FAMILY,
            'php_version' => PHP_VERSION,
            'app_env' => config('app.env'),
            'memory_usage_mb' => round(memory_get_usage(true) / 1048576, 2),
            'memory_peak_mb' => round(memory_get_peak_usage(true) / 1048576, 2),
            'memory_limit' => ini_get('memory_limit'),
            'load_1m' => is_array($loadAverages) ? round((float) ($loadAverages[0] ?? 0), 2) : null,
            'load_5m' => is_array($loadAverages) ? round((float) ($loadAverages[1] ?? 0), 2) : null,
            'load_15m' => is_array($loadAverages) ? round((float) ($loadAverages[2] ?? 0), 2) : null,
            'disk_free_gb' => is_numeric($diskFree) ? round($diskFree / 1073741824, 2) : null,
            'disk_total_gb' => is_numeric($diskTotal) ? round($diskTotal / 1073741824, 2) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function containerHealth(): array
    {
        $inDocker = file_exists('/.dockerenv');
        $sailFlag = filter_var($_SERVER['LARAVEL_SAIL'] ?? $_ENV['LARAVEL_SAIL'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $memoryLimitRaw = $this->readFileFirstLine('/sys/fs/cgroup/memory.max')
            ?? $this->readFileFirstLine('/sys/fs/cgroup/memory/memory.limit_in_bytes');

        $memoryLimitMb = null;
        if (is_string($memoryLimitRaw) && is_numeric($memoryLimitRaw)) {
            $memoryLimit = (int) $memoryLimitRaw;
            if ($memoryLimit > 0 && $memoryLimit < 9223372036854775807) {
                $memoryLimitMb = round($memoryLimit / 1048576, 2);
            }
        }

        return [
            'in_container' => $inDocker || $sailFlag,
            'runtime' => $inDocker ? 'Docker' : 'Host',
            'memory_limit_mb' => $memoryLimitMb,
            'sail_env' => (bool) $sailFlag,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function routeOverview(): array
    {
        $allRoutes = collect(Route::getRoutes()->getRoutes());
        $apiRoutes = $allRoutes->filter(function ($route): bool {
            return Str::startsWith($route->uri(), 'api/')
                || Str::startsWith((string) $route->getName(), 'api.');
        })->count();

        return [
            'total' => $allRoutes->count(),
            'api' => $apiRoutes,
            'web' => max(0, $allRoutes->count() - $apiRoutes),
        ];
    }

    private function incrementCounter(string $key, int $by, DateTimeInterface $expiresAt): void
    {
        if (! Cache::has($key)) {
            Cache::put($key, 0, $expiresAt);
        }

        Cache::increment($key, $by);
    }

    private function key(string $period, string $bucket, string $scope, string $metric): string
    {
        return 'system_health:'.$period.':'.$bucket.':'.$scope.':'.$metric;
    }

    private function resolveScope(Request $request): string
    {
        $routeName = (string) optional($request->route())->getName();
        if ($request->is('api/*') || Str::startsWith($routeName, 'api.') || $request->expectsJson()) {
            return 'api';
        }

        return 'web';
    }

    private function shouldSkip(Request $request): bool
    {
        $routeName = (string) optional($request->route())->getName();
        if ($request->isMethod('OPTIONS')) {
            return true;
        }

        if (Str::startsWith($routeName, 'admin.system-health')) {
            return true;
        }

        return $request->is(
            'build/*',
            'assets/*',
            'storage/*',
            'vendor/*',
            'favicon.ico',
            '_ignition/*'
        );
    }

    private function readFileFirstLine(string $path): ?string
    {
        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            return null;
        }

        $line = trim(Str::before($contents, PHP_EOL));

        return $line === '' ? null : $line;
    }
}
