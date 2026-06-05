<header class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 pb-2">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight flex items-center gap-2 flex-wrap" style="color: var(--cw-text);">
            <span>@cw('title')</span>
            <span class="cw-badge cw-badge-accent text-[10px]" x-text="config.version">—</span>
        </h1>
        <p class="text-sm mt-0.5" style="color: var(--cw-muted);">@cw('subtitle')</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
        <div class="cw-theme-toggle">
            <button type="button" @click="setTheme('light')" class="cw-theme-btn" :class="theme === 'light' && 'active'">
                ☀ Light
            </button>
            <button type="button" @click="setTheme('dark')" class="cw-theme-btn" :class="theme === 'dark' && 'active'">
                🌙 Dark
            </button>
        </div>

        <div class="cw-status-pill">
            <span class="cw-dot cw-dot-pulse" :style="polling ? 'background:var(--cw-success)' : 'background:var(--cw-danger)'"></span>
            <span x-text="polling ? config.labels.polling_active : config.labels.polling_suspended"></span>
        </div>

        <div class="cw-status-pill code-font">
            <span style="color: var(--cw-muted);" x-text="config.labels.uptime + ':'"></span>
            <span class="text-accent font-semibold" x-text="metrics.uptime">—</span>
        </div>

        <button type="button" @click="fetchMetrics()" :disabled="loadingMetrics" class="cw-btn cw-btn-primary">
            <span x-text="config.labels.refresh">@cw('refresh')</span>
        </button>
    </div>
</header>
