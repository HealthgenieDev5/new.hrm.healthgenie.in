<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogNazrulApiRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $body = json_decode($request->getContent(), true);
        $headers = collect($request->headers->all())
            ->map(fn (array $values) => count($values) === 1 ? $values[0] : $values)
            ->all();

        $logEntry = [
            'timestamp' => now()->toIso8601String(),
            'client_ip' => $request->ip(),
            'method' => $request->method(),
            'headers' => $headers,
            'body' => $body ?? $request->getContent(),
        ];

        $message = '[' . $logEntry['timestamp'] . '] '
            . strtoupper($request->method())
            . ' to '
            . '/'
            . ltrim($request->path(), '/')
            . ' from '
            . $request->ip()
            . PHP_EOL
            . json_encode($logEntry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            . PHP_EOL
            . str_repeat('-', 40);

        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/nazrul-api.log'),
            'replace_placeholders' => true,
        ])->info($message);

        return $next($request);
    }
}
