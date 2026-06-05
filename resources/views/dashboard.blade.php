<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoreWatch — Server Health Dashboard</title>
    <script>
        (function () {
            var t = localStorage.getItem('corewatch-theme');
            var d = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.add(t === 'light' || t === 'dark' ? t : (d ? 'dark' : 'light'));
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @include('corewatch::partials.styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full antialiased min-h-screen" x-data="corewatchDashboard()" x-cloak>

    <div class="cw-topbar"></div>

    <div class="cw-page space-y-6">

        @include('corewatch::partials.header')

        {{-- ① System Metrics --}}
        <section class="cw-section">
            <h2 class="cw-section-title">System Metrics</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-5">
                @include('corewatch::partials.cpu')
                @include('corewatch::partials.ram')
                @include('corewatch::partials.disk')
            </div>
        </section>

        {{-- ② Health & Operations --}}
        <section class="cw-section">
            <h2 class="cw-section-title">Health & Operations</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-5">
                @include('corewatch::partials.ops-insights')
                @include('corewatch::partials.app-checks')
                @include('corewatch::partials.database')
            </div>
        </section>

        {{-- ③ Infrastructure --}}
        <section class="cw-section">
            <h2 class="cw-section-title">Infrastructure</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5">
                @include('corewatch::partials.specifications')
                @include('corewatch::partials.processes')
            </div>
        </section>

        {{-- ④ Service Controls --}}
        <section class="cw-section">
            <h2 class="cw-section-title">Service Controls</h2>
            @include('corewatch::partials.services')
        </section>

        {{-- ⑤ Logs --}}
        <section class="cw-section">
            <h2 class="cw-section-title">Log Stream</h2>
            @include('corewatch::partials.logs')
        </section>

        <footer class="text-center text-[10px] code-font pt-2 pb-4" style="color: var(--cw-muted);">
            CoreWatch v<span x-text="config.version">—</span> · Laravel Server Health Sentinel
        </footer>
    </div>

    @include('corewatch::partials.modal')
    @include('corewatch::partials.script')

    <style>[x-cloak] { display: none !important; }</style>
</body>
</html>
