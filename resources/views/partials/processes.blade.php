<div class="cw-card h-full">
    <div class="cw-card-header">
        <div class="cw-card-title">
            <span class="cw-dot cw-dot-pulse" style="background: var(--cw-accent);"></span>
            Top Processes
        </div>
        <span class="cw-badge">Live</span>
    </div>
    <div class="cw-terminal">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs code-font">
                <thead>
                    <tr style="color: var(--cw-muted); border-bottom: 1px solid var(--cw-border);">
                        <th class="p-2.5 w-16">PID</th>
                        <th class="p-2.5">User</th>
                        <th class="p-2.5 w-14 text-right">CPU</th>
                        <th class="p-2.5 w-14 text-right">MEM</th>
                        <th class="p-2.5">Command</th>
                    </tr>
                </thead>
                <tbody style="color: var(--cw-text-secondary);">
                    <tr x-show="!metrics.processes || metrics.processes.length === 0">
                        <td colspan="5" class="p-8 text-center" style="color: var(--cw-muted);">No processes detected</td>
                    </tr>
                    <template x-for="proc in metrics.processes" :key="proc.pid">
                        <tr style="border-top: 1px solid var(--cw-border);">
                            <td class="p-2.5 tabular-nums" x-text="proc.pid"></td>
                            <td class="p-2.5" x-text="proc.user"></td>
                            <td class="p-2.5 tabular-nums text-right text-success font-semibold" x-text="proc.cpu"></td>
                            <td class="p-2.5 tabular-nums text-right text-accent" x-text="proc.mem"></td>
                            <td class="p-2.5 truncate max-w-[200px]" :title="proc.command" x-text="proc.command"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
