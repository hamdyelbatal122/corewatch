# Filament Integration Guide

CoreWatch integrates seamlessly with [Filament](https://filamentphp.com/) admin panels.

## Option 1: Livewire Component (Recommended)

```php
// app/Filament/Pages/ServerHealth.php
namespace App\Filament\Pages;

use Filament\Pages\Page;

class ServerHealth extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static ?string $navigationLabel = 'Server Health';
    protected static ?string $title = 'CoreWatch';
    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.server-health';
}
```

```blade
{{-- resources/views/filament/pages/server-health.blade.php --}}
<x-filament-panels::page>
    <livewire:corewatch-dashboard />
</x-filament-panels::page>
```

## Option 2: Individual Widget Partials

Publish views and embed specific metrics inside Filament widgets:

```bash
php artisan vendor:publish --tag=corewatch-views
```

```blade
<div x-data="corewatchDashboard()">
    @include('corewatch::partials.cpu')
    @include('corewatch::partials.ram')
</div>
```

> Wrap partials in `x-data="corewatchDashboard()"` so AlpineJS polling works.

## Option 3: Custom Filament Widget with Facade

```php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Hamzi\CoreWatch\Facades\CoreWatch;

class ServerStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $cpu  = CoreWatch::cpu();
        $ram  = CoreWatch::ram();
        $disk = CoreWatch::disk();

        return [
            Stat::make('CPU Load', $cpu['usage_percentage'].'%')
                ->color($cpu['usage_percentage'] > 85 ? 'danger' : 'success'),
            Stat::make('RAM Usage', $ram['usage_percentage'].'%')
                ->color($ram['usage_percentage'] > 90 ? 'danger' : 'success'),
            Stat::make('Disk Usage', $disk['usage_percentage'].'%')
                ->color($disk['usage_percentage'] > 90 ? 'warning' : 'success'),
        ];
    }
}
```

## Security in Filament

Filament already requires authentication. Add CoreWatch gate in `AppServiceProvider`:

```php
config(['corewatch.gate' => fn ($request) => auth()->check()]);
```
