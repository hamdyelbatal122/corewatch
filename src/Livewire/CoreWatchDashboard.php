<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Livewire;

use Hamzi\CoreWatch\Application\DTOs\DashboardConfigDto;
use Hamzi\CoreWatch\Http\Middleware\EnsureCoreWatchAuthorized;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Middleware;
use Livewire\Component;

#[Middleware([EnsureCoreWatchAuthorized::class])]
final class CoreWatchDashboard extends Component
{
    public function render(): View
    {
        $config = DashboardConfigDto::fromConfig();

        return view('corewatch::dashboard', compact('config'))
            ->layout('layouts.app');
    }
}
