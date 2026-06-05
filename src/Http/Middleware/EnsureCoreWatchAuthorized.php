<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureCoreWatchAuthorized
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('corewatch.enabled', true)) {
            abort(404, 'CoreWatch dashboard is disabled.');
        }

        $allowedEnvs = config('corewatch.environments', ['local']);
        if (! app()->environment($allowedEnvs)) {
            abort(403, 'CoreWatch dashboard is not permitted in this environment.');
        }

        $gate = config('corewatch.gate');
        if ($gate !== null && is_callable($gate) && ! $gate($request)) {
            abort(403, 'CoreWatch Access Denied.');
        }

        return $next($request);
    }
}
