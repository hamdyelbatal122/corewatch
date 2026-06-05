<div class="cw-card h-full">
    <div class="cw-card-header">
        <div class="cw-card-title">
            <span class="cw-dot" style="background: var(--cw-warning);"></span>
            App Integrity
        </div>
        <span class="cw-badge">Checks</span>
    </div>
    <table class="cw-table">
        <tbody>
            <template x-for="(check, key) in metrics.app_checks" :key="key">
                <tr>
                    <td class="py-2">
                        <div class="font-semibold text-[11px]" style="color: var(--cw-text);" x-text="check.name"></div>
                        <div class="text-[10px] mt-0.5" style="color: var(--cw-muted);" x-text="check.detail"></div>
                    </td>
                    <td class="text-right">
                        <span class="cw-badge" :class="check.active ? 'cw-badge-success' : 'cw-badge-warning'" x-text="check.status"></span>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>
