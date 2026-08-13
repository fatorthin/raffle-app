<?php

namespace App\Livewire\Display;

use App\Models\Winner;
use App\Services\RaffleService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.display')]
class RaffleDisplay extends Component
{
    public array $raffleState = [];

    protected function raffleService(): RaffleService
    {
        return app(RaffleService::class);
    }

    public function mount(): void
    {
        $this->refreshState();
    }

    public function refreshState(): void
    {
        $this->raffleState = $this->raffleService()->getCurrentState();
    }

    public function render()
    {
        $this->refreshState();

        $recentWinners = Winner::with(['coupon', 'prize'])
            ->valid()
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view('livewire.display.raffle-display', [
            'state' => $this->raffleState,
            'recentWinners' => $recentWinners,
        ]);
    }
}
