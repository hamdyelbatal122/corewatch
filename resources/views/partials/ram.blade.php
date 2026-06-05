<div class="cw-card" :class="metrics.ram.usage_percentage >= 90 && 'cw-card-alert'">
    <div class="cw-card-header">
        <div class="cw-card-title">
            <span class="cw-dot cw-dot-pulse" style="background: var(--cw-purple);"></span>
            Memory
        </div>
        <span class="cw-badge" x-text="metrics.ram.total_formatted">—</span>
    </div>
    <div class="cw-progress">
        <div class="cw-progress-bar"
             :class="metrics.ram.usage_percentage >= 90 ? 'danger' : (metrics.ram.usage_percentage >= 70 ? 'warn' : '')"
             :style="'width:' + Math.min(metrics.ram.usage_percentage, 100) + '%'"></div>
    </div>
    <table class="cw-table">
        <tbody>
            <tr>
                <td class="cw-label">Usage</td>
                <td class="cw-value" :class="metrics.ram.usage_percentage >= 90 ? 'text-danger' : (metrics.ram.usage_percentage >= 70 ? 'text-warning' : 'text-success')" x-text="metrics.ram.usage_percentage + '%'">0%</td>
            </tr>
            <tr><td class="cw-label">Allocated</td><td class="cw-value" x-text="metrics.ram.used_formatted">—</td></tr>
            <tr><td class="cw-label">Available</td><td class="cw-value" x-text="metrics.ram.available_formatted">—</td></tr>
            <tr><td class="cw-label">Free</td><td class="cw-value" x-text="metrics.ram.free_formatted">—</td></tr>
        </tbody>
    </table>
</div>
