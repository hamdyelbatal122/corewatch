<header class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 pb-2">
    <div class="flex items-center gap-4">
        <div class="p-3 rounded-xl border code-font font-bold text-lg"
             style="background: var(--cw-accent-soft); border-color: var(--cw-accent); color: var(--cw-accent);">
            CW
        </div>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight flex items-center gap-2 flex-wrap" style="color: var(--cw-text);">
                <span>@cw('title')</span>
                <span class="cw-badge cw-badge-accent text-[10px]" x-text="'v' + (config.version || '2.1')">v2.1</span>
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--cw-muted);">@cw('subtitle')</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
        {{-- Theme toggle --}}
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

        <button type="button" @click="fetchMetrics()" class="cw-btn cw-btn-primary">
            <svg class="w-3.5 h-3.5" :class="loadingMetrics && 'animate-spin'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.2"></path>
            </svg>
            <span x-text="config.labels.refresh">@cw('refresh')</span>
        </button>
    </div>
</header>
