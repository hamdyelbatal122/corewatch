<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Http\Middleware;

use Closure;
use Hamzi\CoreWatch\Support\CoreWatchAuthorizer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureCoreWatchAuthorized
{
    public function handle(Request $request, Closure $next): Response
    {
        CoreWatchAuthorizer::authorize($request);

        return $next($request);
    }
}
