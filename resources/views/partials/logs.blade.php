<div class="cw-card">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 pb-4" style="border-bottom: 1px solid var(--cw-border);">
        <div>
            <div class="cw-card-title mb-1">
                <span class="cw-dot" style="background: var(--cw-warning);"></span>
                Live Log Stream
            </div>
            <p class="text-xs" style="color: var(--cw-muted);">Chunked backwards read — safe for large log files.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <select x-model="logs.activeFile" @change="fetchLogs(true)" class="cw-select w-auto min-w-[140px]">
                <template x-for="file in config.logs" :key="file.key">
                    <option :value="file.key" x-text="file.name">Log File</option>
                </template>
            </select>
            <button type="button" @click="resetLogFilters()" class="cw-btn cw-btn-ghost cw-btn-sm">Clear Filters</button>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4 p-4 rounded-xl" style="background: var(--cw-inset); border: 1px solid var(--cw-border);">
        <div>
            <label class="block text-[10px] font-semibold uppercase mb-1.5 code-font" style="color: var(--cw-muted);">Level</label>
            <select x-model="logs.filters.level" @change="fetchLogs(true)" class="cw-select">
                <option value="">All</option>
                <option value="DEBUG">DEBUG</option>
                <option value="INFO">INFO</option>
                <option value="WARNING">WARNING</option>
                <option value="ERROR">ERROR</option>
                <option value="CRITICAL">CRITICAL</option>
            </select>
        </div>

        <div class="col-span-2 md:col-span-2">
            <label class="block text-[10px] font-semibold uppercase mb-1.5 code-font" style="color: var(--cw-muted);">Search</label>
            <input type="text" x-model="logs.filters.search" @input.debounce.500ms="fetchLogs(true)"
                   placeholder="Query string, errors, URLs…" class="cw-input">
        </div>

        <div>
            <label class="block text-[10px] font-semibold uppercase mb-1.5 code-font" style="color: var(--cw-muted);">IP</label>
            <input type="text" x-model="logs.filters.ip" @input.debounce.500ms="fetchLogs(true)"
                   placeholder="127.0.0.1" class="cw-input">
        </div>

        <div>
            <label class="block text-[10px] font-semibold uppercase mb-1.5 code-font" style="color: var(--cw-muted);">Status</label>
            <input type="number" x-model="logs.filters.status" @input.debounce.500ms="fetchLogs(true)"
                   placeholder="500" class="cw-input">
        </div>
    </div>

    <div class="cw-terminal shadow-lg">
        <div class="cw-terminal-bar">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full" style="background: var(--cw-danger);"></span>
                <span class="w-2.5 h-2.5 rounded-full" style="background: var(--cw-warning);"></span>
                <span class="w-2.5 h-2.5 rounded-full" style="background: var(--cw-success);"></span>
                <span class="ml-2 uppercase tracking-wider truncate max-w-[200px] sm:max-w-none" x-text="logs.filePath">—</span>
            </div>
            <div class="flex items-center gap-3">
                <span x-show="logs.totalScanned > 0" x-text="logs.totalScanned + ' lines'"></span>
                <span class="text-success font-bold">ONLINE</span>
            </div>
        </div>

        <div class="p-4 h-[420px] overflow-y-auto code-font text-xs space-y-2" id="terminal-screen" style="background: var(--cw-terminal); color: #cbd5e1;">

            <div x-show="logs.loading" class="flex flex-col items-center justify-center h-full gap-3">
                <svg class="animate-spin h-8 w-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.2"></path>
                </svg>
                <span style="color: var(--cw-muted);">Streaming logs…</span>
            </div>

            <div x-show="!logs.loading && logs.list.length === 0" class="flex flex-col items-center justify-center h-full text-center gap-2">
                <svg class="h-10 w-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div style="color: var(--cw-muted);">No matching log entries</div>
            </div>

            <div class="space-y-2" x-show="!logs.loading && logs.list.length > 0">
                <template x-for="(log, idx) in logs.list" :key="idx">
                    <div class="rounded-lg p-3 transition-colors"
                         style="background: rgba(255,255,255,0.03); border: 1px solid var(--cw-border);">

                        <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5 text-[11px]">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="cw-badge"
                                      :class="{
                                        'cw-badge-success': log.level === 'INFO' || log.level === 'DEBUG',
                                        'cw-badge-warning': log.level === 'WARNING' || log.level === 'WARN',
                                        'cw-badge-danger': log.level === 'ERROR' || log.level === 'CRITICAL' || log.level === 'ALERT' || log.level === 'EMERGENCY'
                                      }"
                                      x-text="log.level">INFO</span>
                                <span style="color: var(--cw-muted);" x-text="log.date">—</span>
                                <span x-show="log.ip" class="text-accent" x-text="'IP: ' + log.ip"></span>
                                <span x-show="log.status" class="cw-badge"
                                      :class="{
                                        'cw-badge-success': log.status < 400,
                                        'cw-badge-warning': log.status >= 400 && log.status < 500,
                                        'cw-badge-danger': log.status >= 500
                                      }"
                                      x-text="'HTTP ' + log.status"></span>
                            </div>
                            <button type="button" @click="toggleLogExpand(idx)" class="text-[10px] code-font hover:text-accent transition" style="color: var(--cw-muted);">
                                <span x-text="expandedLogIndexes.includes(idx) ? 'Collapse' : 'Expand'"></span>
                            </button>
                        </div>

                        <div class="break-words whitespace-pre-wrap" style="color: #e2e8f0;" x-text="log.message">—</div>

                        <div x-show="expandedLogIndexes.includes(idx)" x-transition
                             class="mt-3 p-3 rounded-lg overflow-x-auto text-[10px] leading-relaxed max-h-64 overflow-y-auto"
                             style="background: #000; border: 1px solid var(--cw-border); color: var(--cw-muted);">
                            <div class="text-[9px] uppercase tracking-wider mb-1 pb-1 font-bold" style="border-bottom: 1px solid var(--cw-border);">Raw line</div>
                            <pre x-text="log.raw"></pre>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="cw-terminal-bar" x-show="!logs.loading && logs.list.length > 0">
            <button type="button" @click="paginateLogs(-1)" :disabled="logs.page <= 1"
                    class="cw-btn cw-btn-ghost cw-btn-sm disabled:opacity-30">← Prev</button>
            <span>Page <span class="text-accent font-bold" x-text="logs.page">1</span></span>
            <button type="button" @click="paginateLogs(1)" :disabled="!logs.hasMore"
                    class="cw-btn cw-btn-ghost cw-btn-sm disabled:opacity-30">Next →</button>
        </div>
    </div>
</div>
