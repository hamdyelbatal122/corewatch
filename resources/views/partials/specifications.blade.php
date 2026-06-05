<div class="cw-card h-full">
    <div class="cw-card-header">
        <div class="cw-card-title">
            <span class="cw-dot" style="background: var(--cw-accent);"></span>
            Host Specs
        </div>
        <span class="cw-badge">Static</span>
    </div>
    <table class="cw-table">
        <tbody>
            <tr><td class="cw-label">Hostname</td><td class="cw-value" x-text="metrics.system_info.hostname">—</td></tr>
            <tr><td class="cw-label">OS</td><td class="cw-value" x-text="metrics.system_info.os">—</td></tr>
            <tr><td class="cw-label">Kernel</td><td class="cw-value text-[10px] break-all max-w-[160px] ml-auto" :title="metrics.system_info.kernel" x-text="metrics.system_info.kernel">—</td></tr>
            <tr><td class="cw-label">PHP</td><td class="cw-value" x-text="metrics.system_info.php_version">—</td></tr>
            <tr><td class="cw-label">Laravel</td><td class="cw-value text-success" x-text="'v' + metrics.system_info.laravel_version">—</td></tr>
            <tr><td class="cw-label">Server</td><td class="cw-value text-[10px] truncate max-w-[160px] ml-auto" :title="metrics.system_info.server_software" x-text="metrics.system_info.server_software">—</td></tr>
        </tbody>
    </table>
</div>
