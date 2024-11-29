<?php

namespace App\Http\Middleware;

use App\Models\EndpointStat;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogEndpointRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $endpoint = $request->path();
        $method = $request->method();

        // Find or create the stat record
        $stat = EndpointStat::firstOrCreate(
            ['endpoint' => $endpoint, 'method' => $method],
            ['count' => 0, 'success_count' => 0, 'error_count' => 0]
        );


        $stat->increment('count');

        try {
            $response = $next($request);
        } catch (\Throwable $e) {

            $stat->increment('error_count');
            throw $e;
        }

        if ($response->status() >= 200 && $response->status() < 400) {
            $stat->increment('success_count');
        } else {
            $stat->increment('error_count');
        }

        return $response;
    }
}

