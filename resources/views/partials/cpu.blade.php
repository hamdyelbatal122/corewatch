<div class="cw-card" :class="metrics.cpu.usage_percentage >= 85 && 'cw-card-alert'">
    <div class="cw-card-header">
        <div class="cw-card-title">
            <span class="cw-dot cw-dot-pulse text-accent" style="background: var(--cw-accent);"></span>
            CPU
        </div>
        <span class="cw-badge cw-badge-accent" x-text="metrics.cpu.cores + ' cores'">0 cores</span>
    </div>
    <div class="cw-progress">
        <div class="cw-progress-bar"
             :class="metrics.cpu.usage_percentage >= 85 ? 'danger' : (metrics.cpu.usage_percentage >= 60 ? 'warn' : '')"
             :style="'width:' + Math.min(metrics.cpu.usage_percentage, 100) + '%'"></div>
    </div>
    <table class="cw-table">
        <tbody>
            <tr>
                <td class="cw-label">Usage</td>
                <td class="cw-value" :class="metrics.cpu.usage_percentage >= 85 ? 'text-danger' : (metrics.cpu.usage_percentage >= 60 ? 'text-warning' : 'text-success')" x-text="metrics.cpu.usage_percentage + '%'">0%</td>
            </tr>
            <tr><td class="cw-label">Load 1m</td><td class="cw-value" x-text="metrics.cpu.load_1">0</td></tr>
            <tr><td class="cw-label">Load 5m</td><td class="cw-value" x-text="metrics.cpu.load_5">0</td></tr>
            <tr><td class="cw-label">Load 15m</td><td class="cw-value" x-text="metrics.cpu.load_15">0</td></tr>
        </tbody>
    </table>
</div>
