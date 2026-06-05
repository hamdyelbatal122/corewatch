# Troubleshooting Guide

## Dashboard returns 403 Forbidden

**Cause:** Environment not in `config/corewatch.php` → `environments` array.

**Fix:**
```php
'environments' => ['local', 'staging', 'production'],
```

Or restrict to specific environments in production.

---

## Dashboard returns 404 Not Found

**Cause:** `COREWATCH_ENABLED=false` or wrong path.

**Fix:** Check `.env`:
```env
COREWATCH_ENABLED=true
COREWATCH_PATH=corewatch
```

---

## Metrics show 0 or "Unknown"

**Cause:** PHP `disable_functions` blocks shell commands and `/proc` is not readable (common in shared hosting or Docker without caps).

**Fix:**
- On VPS/dedicated: ensure `/proc/loadavg`, `/proc/meminfo` are readable
- In Docker: mount host `/proc` or use `--pid=host`
- Check: `CoreWatch::metrics()` in tinker

---

## Log viewer shows "file not readable"

**Cause:** Web server user lacks read permission on log files.

**Fix:**
```bash
# Add www-data to adm group (Debian/Ubuntu)
sudo usermod -aG adm www-data

# Or set log path in .env
COREWATCH_NGINX_ACCESS_LOG=/var/log/nginx/access.log
```

---

## Shell commands fail in Services panel

**Cause:** `exec`, `proc_open` disabled in `php.ini`.

**Fix:** Use `artisan` type commands (they don't need shell). For shell commands, ask your host to enable `proc_open`.

---

## Alerts not sending to Slack/Telegram

**Checklist:**
1. `.env` credentials are set
2. `corewatch:check-health` is scheduled in `routes/console.php`
3. Thresholds are actually breached (run manually: `php artisan corewatch:check-health`)
4. For custom channels, listen to `ThresholdBreached` event

---

## Livewire component shows blank page

**Cause:** Missing `layouts.app` when rendered as full-page component.

**Fix:** Embed inside an existing layout or use Filament integration (see [FILAMENT.md](FILAMENT.md)).
