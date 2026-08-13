<?php

namespace Tests\Feature;

use App\Livewire\Admin\RaffleAdmin;
use App\Models\Coupon;
use App\Models\Prize;
use App\Models\Winner;
use App\Services\RaffleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed some initial data
        Prize::create(['name' => 'Sepeda Gunung', 'quota' => 2]);
        Prize::create(['name' => 'Kipas Angin', 'quota' => 1]);

        Coupon::create(['coupon_number' => 'JLS-0001', 'name' => 'Peserta Satu']);
        Coupon::create(['coupon_number' => 'JLS-0002', 'name' => 'Peserta Dua']);
        Coupon::create(['coupon_number' => 'JLS-0003', 'name' => 'Peserta Tiga']);
    }

    public function test_admin_route_is_accessible(): void
    {
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Undian Jalan Sehat');
        $response->assertSee('Raffle Controller');
    }

    public function test_root_redirects_to_admin(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/admin');
    }

    public function test_raffle_service_flow(): void
    {
        $service = app(RaffleService::class);
        $prize = Prize::first();

        // Select prize
        $state = $service->selectPrize($prize->id);
        $this->assertEquals($prize->id, $state['prize_id']);
        $this->assertEquals('idle', $state['status']);

        // Start rolling
        $state = $service->startRolling();
        $this->assertEquals('rolling', $state['status']);

        // Draw winner
        $winner = $service->drawWinner();
        $this->assertInstanceOf(Winner::class, $winner);
        $this->assertEquals('valid', $winner->status);

        $state = $service->getCurrentState();
        $this->assertEquals('winner', $state['status']);
        $this->assertEquals($winner->id, $state['winner_id']);
        $this->assertEquals(1, $prize->fresh()->remaining_quota);

        // Annul winner
        $service->annulWinner($winner->id);
        $this->assertTrue($winner->fresh()->isAnnulled());
        $this->assertEquals(2, $prize->fresh()->remaining_quota);
    }

    public function test_livewire_raffle_admin_component_actions(): void
    {
        $prize = Prize::first();

        Livewire::test(RaffleAdmin::class)
            ->assertSee('Raffle Controller')
            ->set('selectedPrizeId', $prize->id)
            ->call('startRolling')
            ->assertSet('raffleState.status', 'rolling')
            ->call('drawWinner')
            ->assertSet('raffleState.status', 'winner')
            ->call('setTab', 'prizes')
            ->assertSet('activeTab', 'prizes')
            ->set('prizeName', 'Smart TV')
            ->set('prizeQuota', 3)
            ->call('savePrize')
            ->assertSee('Smart TV');

        $this->assertDatabaseHas('prizes', [
            'name' => 'Smart TV',
            'quota' => 3,
        ]);
    }

    public function test_multi_winner_draw_flow(): void
    {
        $service = app(RaffleService::class);
        $prize = Prize::create(['name' => 'Payung Eksklusif', 'quota' => 5]);

        $service->selectPrize($prize->id);
        $service->startRolling(3); // Roll for 3 winners at once

        $state = $service->getCurrentState();
        $this->assertEquals(3, $state['draw_count']);
        $this->assertEquals('rolling', $state['status']);

        $winners = $service->drawWinners(3);
        $this->assertCount(3, $winners);
        $this->assertEquals(2, $prize->fresh()->remaining_quota);

        $state = $service->getCurrentState();
        $this->assertEquals('winner', $state['status']);
        $this->assertCount(3, $state['winners']);
    }

    public function test_set_digit_delay_livewire(): void
    {
        Livewire::test(RaffleAdmin::class)
            ->call('setDigitDelay', 700)
            ->assertSet('digitDelayMs', 700);

        $service = app(RaffleService::class);
        $this->assertEquals(700, $service->getCurrentState()['digit_delay_ms']);
    }
}
