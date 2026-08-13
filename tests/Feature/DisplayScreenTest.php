<?php

namespace Tests\Feature;

use App\Livewire\Display\RaffleDisplay;
use App\Models\Coupon;
use App\Models\Prize;
use App\Models\Winner;
use App\Services\RaffleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DisplayScreenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed sample prizes and coupons
        Prize::create(['name' => 'Sepeda Gunung Polygon', 'quota' => 2]);
        Coupon::create(['coupon_number' => 'JLS-0001', 'name' => 'Budi Santoso']);
        Coupon::create(['coupon_number' => 'JLS-0002', 'name' => 'Siti Rahma']);
    }

    public function test_display_screen_is_accessible(): void
    {
        $response = $this->get('/display');
        $response->assertStatus(200);
        $response->assertSee('UNDIAN JALAN SEHAT');
        $response->assertSee('Pemenang Terkini');
    }

    public function test_api_raffle_state_returns_correct_json(): void
    {
        $prize = Prize::first();
        app(RaffleService::class)->selectPrize($prize->id);

        $response = $this->getJson('/api/raffle/state');
        $response->assertStatus(200);
        $response->assertJsonPath('state.status', 'idle');
        $response->assertJsonPath('state.prize.name', 'Sepeda Gunung Polygon');
        $response->assertJsonPath('state.prize.remaining_quota', 2);
    }

    public function test_api_reacts_to_rolling_and_winner_states(): void
    {
        $service = app(RaffleService::class);
        $prize = Prize::first();
        $service->selectPrize($prize->id);

        // 1. Test Rolling State via API
        $service->startRolling();
        $response = $this->getJson('/api/raffle/state');
        $response->assertStatus(200);
        $response->assertJsonPath('state.status', 'rolling');

        // 2. Test Winner State via API
        $winner = $service->drawWinner();
        $response = $this->getJson('/api/raffle/state');
        $response->assertStatus(200);
        $response->assertJsonPath('state.status', 'winner');
        $response->assertJsonPath('state.winner.coupon_number', $winner->coupon->coupon_number);
        $response->assertJsonPath('state.winner.participant_name', $winner->coupon->name);
    }

    public function test_api_returns_recent_winners_for_ticker(): void
    {
        $prize = Prize::first();
        $coupon = Coupon::first();

        Winner::create([
            'coupon_id' => $coupon->id,
            'prize_id' => $prize->id,
            'status' => 'valid',
        ]);

        $response = $this->getJson('/api/raffle/state');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'coupon_number' => $coupon->coupon_number,
            'name' => $coupon->name,
            'prize_name' => $prize->name,
        ]);
    }
}
