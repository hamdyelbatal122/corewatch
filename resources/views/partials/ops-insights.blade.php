<div class="cw-card h-full" x-show="config.widgets?.ops_insights !== false">
    <div class="cw-card-header">
        <div class="cw-card-title">
            <span class="cw-dot" style="background: var(--cw-purple);"></span>
            <span x-text="config.labels.ops_insights">@cw('ops_insights')</span>
        </div>
    </div>
    <div class="space-y-2 code-font text-xs">
        <div class="flex justify-between items-center gap-3 p-3 rounded-lg" style="background: var(--cw-inset); border: 1px solid var(--cw-border);">
            <span style="color: var(--cw-muted);" x-text="config.labels.ssl_certificate">SSL</span>
            <span class="font-semibold text-right truncate" :class="metrics.ssl?.active ? 'text-success' : 'text-danger'" x-text="metrics.ssl?.status || '—'"></span>
        </div>
        <div class="flex justify-between items-center gap-3 p-3 rounded-lg" style="background: var(--cw-inset); border: 1px solid var(--cw-border);">
            <span style="color: var(--cw-muted);" x-text="config.labels.failed_jobs">Jobs</span>
            <span class="font-semibold text-right truncate" :class="metrics.failed_jobs?.active ? 'text-success' : 'text-warning'" x-text="metrics.failed_jobs?.status || '—'"></span>
        </div>
        <div class="flex justify-between items-center gap-3 p-3 rounded-lg" style="background: var(--cw-inset); border: 1px solid var(--cw-border);">
            <span style="color: var(--cw-muted);" x-text="config.labels.scheduler">Scheduler</span>
            <span class="font-semibold text-right truncate" :class="metrics.schedule?.active ? 'text-success' : 'text-danger'" x-text="metrics.schedule?.status || '—'"></span>
        </div>
    </div>
</div>
