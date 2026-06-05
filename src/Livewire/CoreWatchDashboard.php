<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Livewire;

use Hamzi\CoreWatch\Application\DTOs\DashboardConfigDto;
use Hamzi\CoreWatch\Support\CoreWatchAuthorizer;
use Livewire\Component;

final class CoreWatchDashboard extends Component
{
    public function mount(): void
    {
        CoreWatchAuthorizer::authorize();
    }

    public function render()
    {
        $config = DashboardConfigDto::fromConfig();

        return view('corewatch::dashboard', compact('config'))
            ->layout('layouts.app');
    }
}
