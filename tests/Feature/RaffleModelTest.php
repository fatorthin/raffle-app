<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Prize;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaffleModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_prizes_and_coupons_can_be_created_and_queried(): void
    {
        $prize = Prize::create([
            'name' => 'Sepeda Gunung Polygon',
            'quota' => 2,
        ]);

        $coupon1 = Coupon::create([
            'coupon_number' => 'JLS-0001',
            'name' => 'Budi Santoso',
        ]);

        $coupon2 = Coupon::create([
            'coupon_number' => 'JLS-0002',
            'name' => 'Siti Rahma',
        ]);

        $this->assertEquals(2, $prize->remaining_quota);
        $this->assertTrue($prize->hasQuota());
        $this->assertEquals(2, Coupon::eligible()->count());
    }

    public function test_winning_coupon_is_excluded_from_eligible_coupons(): void
    {
        $prize = Prize::create([
            'name' => 'Sepeda Gunung Polygon',
            'quota' => 2,
        ]);

        $coupon1 = Coupon::create([
            'coupon_number' => 'JLS-0001',
            'name' => 'Budi Santoso',
        ]);

        $coupon2 = Coupon::create([
            'coupon_number' => 'JLS-0002',
            'name' => 'Siti Rahma',
        ]);

        $winner = Winner::create([
            'coupon_id' => $coupon1->id,
            'prize_id' => $prize->id,
            'status' => 'valid',
        ]);

        $this->assertEquals(1, $prize->fresh()->remaining_quota);
        $this->assertEquals(1, Coupon::eligible()->count());
        $this->assertEquals($coupon2->id, Coupon::eligible()->first()->id);
    }

    public function test_annulled_winner_restores_prize_quota_but_coupon_remains_burnt(): void
    {
        $prize = Prize::create([
            'name' => 'Sepeda Gunung Polygon',
            'quota' => 2,
        ]);

        $coupon1 = Coupon::create([
            'coupon_number' => 'JLS-0001',
            'name' => 'Budi Santoso',
        ]);

        $coupon2 = Coupon::create([
            'coupon_number' => 'JLS-0002',
            'name' => 'Siti Rahma',
        ]);

        $winner = Winner::create([
            'coupon_id' => $coupon1->id,
            'prize_id' => $prize->id,
            'status' => 'valid',
        ]);

        $this->assertEquals(1, $prize->fresh()->remaining_quota);

        // Admin annuls the winner
        $winner->annul();

        $this->assertTrue($winner->fresh()->isAnnulled());
        // Prize quota restored to 2
        $this->assertEquals(2, $prize->fresh()->remaining_quota);
        // Coupon 1 is still considered burnt and not eligible
        $this->assertEquals(1, Coupon::eligible()->count());
        $this->assertEquals($coupon2->id, Coupon::eligible()->first()->id);
    }
}
