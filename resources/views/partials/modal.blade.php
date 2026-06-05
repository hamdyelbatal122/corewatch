<div x-show="outputModal.show"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
     style="background: rgba(3, 7, 18, 0.75); backdrop-filter: blur(4px); display: none;">
    <div class="cw-card max-w-2xl w-full overflow-hidden shadow-2xl" @click.away="outputModal.show = false" style="padding: 0;">

        <div class="cw-terminal-bar">
            <div class="flex items-center gap-2">
                <span class="cw-dot cw-dot-pulse" style="background: var(--cw-accent);"></span>
                <span class="font-bold uppercase tracking-wider" style="color: var(--cw-text);" x-text="outputModal.title">Result</span>
            </div>
            <button type="button" @click="outputModal.show = false" class="cw-btn cw-btn-ghost cw-btn-sm">Close</button>
        </div>

        <div class="p-5">
            <div class="cw-terminal p-4 h-72 overflow-auto code-font text-xs select-text leading-relaxed">
                <div class="flex items-center justify-between text-[10px] uppercase font-bold mb-2 pb-2" style="color: var(--cw-muted); border-bottom: 1px solid var(--cw-border);">
                    <span>Output</span>
                    <span :class="outputModal.success ? 'text-success' : 'text-danger'"
                          x-text="outputModal.success ? 'Success' : 'Failed'"></span>
                </div>
                <pre class="whitespace-pre-wrap" style="color: #e2e8f0;" x-text="outputModal.content"></pre>
            </div>
        </div>

        <div class="px-5 py-3 flex justify-end" style="border-top: 1px solid var(--cw-border); background: var(--cw-inset);">
            <button type="button" @click="outputModal.show = false" class="cw-btn cw-btn-primary">Acknowledge</button>
        </div>
    </div>
</div>
