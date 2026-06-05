<div class="cw-card">
    <div class="cw-card-header">
        <div class="cw-card-title">
            <span class="cw-dot" style="background: var(--cw-success);"></span>
            Service Controller
        </div>
        <span class="cw-badge">Whitelisted Actions</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs code-font">
            <thead>
                <tr style="color: var(--cw-muted); border-bottom: 1px solid var(--cw-border);">
                    <th class="p-2.5 font-semibold uppercase text-[10px] tracking-wider">Service / Task</th>
                    <th class="p-2.5 font-semibold uppercase text-[10px] tracking-wider">Type</th>
                    <th class="p-2.5 font-semibold uppercase text-[10px] tracking-wider text-right">Status / Control</th>
                </tr>
            </thead>
            <tbody style="color: var(--cw-text-secondary);">
                <template x-for="(status, key) in metrics.services" :key="key">
                    <tr style="border-top: 1px solid var(--cw-border);">
                        <td class="p-2.5 font-semibold" style="color: var(--cw-text);" x-text="status.name">—</td>
                        <td class="p-2.5" style="color: var(--cw-muted);" x-text="status.port ? 'Port: ' + status.port : 'Process'">—</td>
                        <td class="p-2.5 text-right">
                            <span class="cw-badge"
                                  :class="status.active ? 'cw-badge-success' : 'cw-badge-danger'"
                                  x-text="status.active ? 'ACTIVE' : 'INACTIVE'">—</span>
                        </td>
                    </tr>
                </template>

                <template x-for="service in config.services" :key="service.key">
                    <tr style="border-top: 1px solid var(--cw-border);">
                        <td class="p-2.5 font-semibold" style="color: var(--cw-text);" x-text="service.name">—</td>
                        <td class="p-2.5" style="color: var(--cw-muted);">Secure Tool</td>
                        <td class="p-2.5 text-right">
                            <button type="button"
                                    @click="triggerServiceCommand(service.key)"
                                    :disabled="runningServiceKey === service.key"
                                    class="cw-btn cw-btn-primary cw-btn-sm ml-auto">
                                <svg x-show="runningServiceKey === service.key" class="animate-spin w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.2"></path>
                                </svg>
                                <span x-text="runningServiceKey === service.key ? 'RUNNING' : 'RUN'">RUN</span>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
