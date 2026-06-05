<!-- Operations Insights: SSL, Failed Jobs, Scheduler -->
<div class="bg-cyber-card border border-cyber-border rounded-xl p-5 shadow-lg" x-show="config.widgets?.ops_insights !== false">
    <h3 class="text-sm font-semibold text-cyber-purple uppercase tracking-wider mb-4 flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-cyber-purple animate-pulse"></span>
        {{ __('corewatch::ops_insights') }}
    </h3>
    <div class="space-y-3 code-font text-xs">
        <div class="flex justify-between items-center p-2.5 bg-[#050b18] border border-cyber-border/60 rounded-lg">
            <span class="text-gray-400">{{ __('corewatch::ssl_certificate') }}</span>
            <span class="font-semibold" :class="metrics.ssl?.active ? 'text-cyber-green' : 'text-cyber-red'" x-text="metrics.ssl?.status || '—'"></span>
        </div>
        <div class="flex justify-between items-center p-2.5 bg-[#050b18] border border-cyber-border/60 rounded-lg">
            <span class="text-gray-400">{{ __('corewatch::failed_jobs') }}</span>
            <span class="font-semibold" :class="metrics.failed_jobs?.active ? 'text-cyber-green' : 'text-cyber-orange'" x-text="metrics.failed_jobs?.status || '—'"></span>
        </div>
        <div class="flex justify-between items-center p-2.5 bg-[#050b18] border border-cyber-border/60 rounded-lg">
            <span class="text-gray-400">{{ __('corewatch::scheduler') }}</span>
            <span class="font-semibold" :class="metrics.schedule?.active ? 'text-cyber-green' : 'text-cyber-red'" x-text="metrics.schedule?.status || '—'"></span>
        </div>
    </div>
</div>
