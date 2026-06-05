<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Notifications;

use Exception;
use Hamzi\CoreWatch\Contracts\AlertNotifierInterface;
use Illuminate\Support\Facades\Http;

final class SlackNotifier implements AlertNotifierInterface
{
    public function notify(array $alerts, array $systemInfo): bool
    {
        $webhookUrl = config('corewatch.notifications.slack.webhook_url');
        if (empty($webhookUrl)) {
            return false;
        }

        $blocks = [
            [
                'type' => 'header',
                'text' => [
                    'type' => 'plain_text',
                    'text' => '🚨 CoreWatch Server Alert Breach 🚨',
                    'emoji' => true,
                ],
            ],
            [
                'type' => 'section',
                'fields' => [
                    ['type' => 'mrkdwn', 'text' => "*Host:* `{$systemInfo['hostname']}`"],
                    ['type' => 'mrkdwn', 'text' => '*Environment:* `'.app()->environment().'`'],
                    ['type' => 'mrkdwn', 'text' => "*OS:* `{$systemInfo['os']}`"],
                    ['type' => 'mrkdwn', 'text' => "*PHP:* `{$systemInfo['php_version']}`"],
                ],
            ],
            ['type' => 'divider'],
        ];

        foreach ($alerts as $alert) {
            $blocks[] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => sprintf(
                        "⚠️ *%s* has breached limits!\n*Current Usage:* `%s` (Threshold: `%s`)\n*Details:* _%s_",
                        $alert->name,
                        $alert->current,
                        $alert->threshold,
                        $alert->details
                    ),
                ],
            ];
        }

        $blocks[] = ['type' => 'divider'];
        $blocks[] = [
            'type' => 'context',
            'elements' => [
                [
                    'type' => 'mrkdwn',
                    'text' => 'CoreWatch DevOps Sentinel • '.now()->toCookieString(),
                ],
            ],
        ];

        try {
            Http::post($webhookUrl, [
                'text' => 'CoreWatch Alert Breach: Server resource thresholds exceeded!',
                'blocks' => $blocks,
            ]);

            return true;
        } catch (Exception) {
            return false;
        }
    }
}
