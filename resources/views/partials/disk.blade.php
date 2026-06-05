<div class="cw-card" :class="metrics.disk.usage_percentage >= 90 && 'cw-card-alert'">
    <div class="cw-card-header">
        <div class="cw-card-title">
            <span class="cw-dot cw-dot-pulse" style="background: var(--cw-warning);"></span>
            Disk
        </div>
        <span class="cw-badge" x-text="metrics.disk.total_formatted">—</span>
    </div>
    <div class="cw-progress">
        <div class="cw-progress-bar"
             :class="metrics.disk.usage_percentage >= 90 ? 'danger' : (metrics.disk.usage_percentage >= 75 ? 'warn' : '')"
             :style="'width:' + Math.min(metrics.disk.usage_percentage, 100) + '%'"></div>
    </div>
    <table class="cw-table">
        <tbody>
            <tr>
                <td class="cw-label">Usage</td>
                <td class="cw-value" :class="metrics.disk.usage_percentage >= 90 ? 'text-danger' : (metrics.disk.usage_percentage >= 75 ? 'text-warning' : 'text-success')" x-text="metrics.disk.usage_percentage + '%'">0%</td>
            </tr>
            <tr><td class="cw-label">Used</td><td class="cw-value" x-text="metrics.disk.used_formatted">—</td></tr>
            <tr><td class="cw-label">Free</td><td class="cw-value" x-text="metrics.disk.free_formatted">—</td></tr>
            <tr>
                <td class="cw-label align-top pt-2">Path</td>
                <td class="cw-value text-[10px] break-all leading-relaxed max-w-[180px] ml-auto" :title="metrics.disk.path" x-text="metrics.disk.path">—</td>
            </tr>
        </tbody>
    </table>
</div>
