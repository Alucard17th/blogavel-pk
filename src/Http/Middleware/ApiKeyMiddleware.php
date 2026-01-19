<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApiKeyMiddleware
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $headerName = (string) config('blogavel.api_key_header', 'X-API-KEY');
        $allowedKeys = (array) config('blogavel.api_keys', []);

        $key = (string) $request->header($headerName, '');

        if ($key === '' || count($allowedKeys) === 0) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        foreach ($allowedKeys as $allowed) {
            $allowed = (string) $allowed;
            if ($allowed !== '' && hash_equals($allowed, $key)) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
