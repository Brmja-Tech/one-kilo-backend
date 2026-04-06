<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces Accept: application/json on all API requests.
 *
 * This ensures that:
 *  - $request->expectsJson() always returns true for API routes
 *  - Laravel's exception handler always renders JSON, not HTML
 *  - Auth middleware returns 401 JSON instead of redirecting to login page
 *  - Validation exceptions render as JSON, not HTML error pages
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
