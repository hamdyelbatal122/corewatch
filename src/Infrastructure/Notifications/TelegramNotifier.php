<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Notifications;

use Exception;
use Hamzi\CoreWatch\Contracts\AlertNotifierInterface;
use Illuminate\Support\Facades\Http;

final class TelegramNotifier implements AlertNotifierInterface
{
    public function notify(array $alerts, array $systemInfo): bool
    {
        $botToken = config('corewatch.notifications.telegram.bot_token');
        $chatId = config('corewatch.notifications.telegram.chat_id');

        if (empty($botToken) || empty($chatId)) {
            return false;
        }

        $message = "<b>⚠️ CoreWatch Alert Breach ⚠️</b>\n\n";
        $message .= "<b>Host:</b> <code>{$systemInfo['hostname']}</code>\n";
        $message .= '<b>Env:</b> <code>'.app()->environment()."</code>\n";
        $message .= "<b>OS:</b> <code>{$systemInfo['os']}</code>\n\n";
        $message .= "<b>Resource Violations:</b>\n";

        foreach ($alerts as $alert) {
            $message .= "• <b>{$alert->name}</b>\n";
            $message .= "  Current: <code>{$alert->current}</code> (Limit: {$alert->threshold})\n";
            $message .= "  Info: <i>{$alert->details}</i>\n\n";
        }

        $message .= '📅 <i>'.now()->toCookieString().'</i>';

        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            Http::post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            return true;
        } catch (Exception) {
            return false;
        }
    }
}
