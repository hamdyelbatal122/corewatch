<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Support;

use Illuminate\Http\Request;

final class CoreWatchAuthorizer
{
    public static function authorize(?Request $request = null): void
    {
        $request ??= request();

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
    }
}
