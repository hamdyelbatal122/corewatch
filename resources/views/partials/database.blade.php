<div class="cw-card h-full">
    <div class="cw-card-header">
        <div class="cw-card-title">
            <span class="cw-dot" style="background: var(--cw-purple);"></span>
            Database
        </div>
        <span class="cw-badge" :class="metrics.database?.active ? 'cw-badge-success' : 'cw-badge-danger'"
              x-text="metrics.database ? metrics.database.connection : '…'">—</span>
    </div>
    <table class="cw-table">
        <tbody>
            <tr><td class="cw-label">Engine</td><td class="cw-value text-accent" x-text="metrics.database?.driver || '—'">—</td></tr>
            <tr><td class="cw-label">Size</td><td class="cw-value" x-text="metrics.database?.size_formatted || '—'">—</td></tr>
            <tr><td class="cw-label">Tables</td><td class="cw-value text-success" x-text="(metrics.database?.tables_count ?? 0) + ' tables'">0</td></tr>
        </tbody>
    </table>
</div>
