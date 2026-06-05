<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Audit;

use Illuminate\Support\Facades\Log;

final class ServiceAuditLogger
{
    public function log(string $serviceKey, string $serviceName, bool $success, ?int $userId = null, ?string $ip = null): void
    {
        if (! config('corewatch.audit_log.enabled', true)) {
            return;
        }

        $channel = config('corewatch.audit_log.channel', 'single');

        Log::channel($channel)->info('[CoreWatch Audit] Service command executed', [
            'service_key' => $serviceKey,
            'service_name' => $serviceName,
            'success' => $success,
            'user_id' => $userId,
            'ip' => $ip,
            'environment' => app()->environment(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
