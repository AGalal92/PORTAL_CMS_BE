<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log; // Import Log facade

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'sanctum/csrf-cookie',
        'api/*',
        'login',
        'logout',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Log the expected CSRF token and received token
        Log::info('Expected CSRF Token:', [$request->session()->token()]);
        Log::info('Received CSRF Token:', [$request->header('X-XSRF-TOKEN')]);

        return parent::handle($request, $next);
    }
}
