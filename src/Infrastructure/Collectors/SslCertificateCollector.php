<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Collectors;

final class SslCertificateCollector
{
    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        if (! config('corewatch.ssl.enabled', true)) {
            return [
                'enabled' => false,
                'status' => 'Disabled',
                'active' => true,
                'detail' => 'SSL monitoring is disabled in config',
            ];
        }

        $host = config('corewatch.ssl.host') ?: parse_url((string) config('app.url'), PHP_URL_HOST);

        if (empty($host) || in_array($host, ['localhost', '127.0.0.1'], true)) {
            return [
                'enabled' => true,
                'host' => $host ?: 'unknown',
                'status' => 'Skipped 🏠',
                'active' => true,
                'detail' => 'SSL check skipped for local development hosts',
            ];
        }

        $port = (int) config('corewatch.ssl.port', 443);
        $warningDays = (int) config('corewatch.ssl.warning_days', 14);

        try {
            $stream = @stream_socket_client(
                "ssl://{$host}:{$port}",
                $errno,
                $errstr,
                5,
                STREAM_CLIENT_CONNECT,
                stream_context_create(['ssl' => ['capture_peer_cert' => true]])
            );

            if (! $stream) {
                return [
                    'enabled' => true,
                    'host' => $host,
                    'status' => 'Unreachable ❌',
                    'active' => false,
                    'detail' => $errstr ?: "Could not connect to ssl://{$host}:{$port}",
                ];
            }

            $params = stream_context_get_params($stream);
            fclose($stream);

            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate'] ?? '');
            if (! is_array($cert)) {
                return [
                    'enabled' => true,
                    'host' => $host,
                    'status' => 'Invalid ❌',
                    'active' => false,
                    'detail' => 'Could not parse SSL certificate',
                ];
            }

            $expiresAt = (int) ($cert['validTo_time_t'] ?? 0);
            $daysLeft = (int) floor(($expiresAt - time()) / 86400);
            $expiresFormatted = date('Y-m-d', $expiresAt);

            $active = $daysLeft > $warningDays;
            $status = $daysLeft <= 0 ? 'Expired ❌' : ($daysLeft <= $warningDays ? "Expiring in {$daysLeft}d ⚠️" : 'Valid ✅');

            return [
                'enabled' => true,
                'host' => $host,
                'issuer' => $cert['issuer']['O'] ?? 'Unknown',
                'expires_at' => $expiresFormatted,
                'days_left' => max(0, $daysLeft),
                'status' => $status,
                'active' => $active,
                'detail' => "Certificate expires on {$expiresFormatted} ({$daysLeft} days remaining)",
            ];
        } catch (\Throwable $e) {
            return [
                'enabled' => true,
                'host' => $host,
                'status' => 'Error ❌',
                'active' => false,
                'detail' => $e->getMessage(),
            ];
        }
    }
}
