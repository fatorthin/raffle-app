<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Prize;
use App\Models\Winner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RaffleService
{
    public const CACHE_KEY = 'raffle_current_state';

    /**
     * Get the current raffle state.
     */
    public function getCurrentState(): array
    {
        $defaultState = [
            'status' => 'idle', // 'idle' | 'rolling' | 'winner'
            'draw_count' => 1,  // 1, 2, 3, 4, 5
            'digit_delay_ms' => 350, // 150, 350, 700, 1200
            'prize_id' => null,
            'winner_id' => null,
            'winner_ids' => [],
            'rolling_at' => null,
            'updated_at' => now()->toISOString(),
        ];

        $state = Cache::get(self::CACHE_KEY, $defaultState);

        // Ensure defaults if missing keys
        $state['draw_count'] = (int) ($state['draw_count'] ?? 1);
        $state['digit_delay_ms'] = (int) ($state['digit_delay_ms'] ?? 350);
        $state['winner_ids'] = $state['winner_ids'] ?? [];

        // Enhance state with eager loaded prize object
        if (!empty($state['prize_id'])) {
            $prize = Prize::find($state['prize_id']);
            $state['prize'] = $prize ? [
                'id' => $prize->id,
                'name' => $prize->name,
                'quota' => $prize->quota,
                'remaining_quota' => $prize->remaining_quota,
                'image_path' => $prize->image_path,
            ] : null;
        } else {
            $state['prize'] = null;
        }

        // Enhance state with list of winners drawn in current session
        $winnersList = [];
        if (!empty($state['winner_ids']) && is_array($state['winner_ids'])) {
            $winnerModels = Winner::with(['coupon', 'prize'])->whereIn('id', $state['winner_ids'])->get();
            foreach ($winnerModels as $winner) {
                $winnersList[] = [
                    'id' => $winner->id,
                    'status' => $winner->status,
                    'coupon_number' => $winner->coupon?->coupon_number,
                    'participant_name' => $winner->coupon?->name,
                    'prize_name' => $winner->prize?->name,
                    'created_at' => $winner->created_at?->toISOString(),
                ];
            }
        } elseif (!empty($state['winner_id'])) {
            $winner = Winner::with(['coupon', 'prize'])->find($state['winner_id']);
            if ($winner) {
                $winnersList[] = [
                    'id' => $winner->id,
                    'status' => $winner->status,
                    'coupon_number' => $winner->coupon?->coupon_number,
                    'participant_name' => $winner->coupon?->name,
                    'prize_name' => $winner->prize?->name,
                    'created_at' => $winner->created_at?->toISOString(),
                ];
            }
        }

        $state['winners'] = $winnersList;
        $state['winner'] = $winnersList[0] ?? null;

        return $state;
    }

    /**
     * Save the raw raffle state into cache.
     */
    protected function setState(array $state): array
    {
        $currentState = Cache::get(self::CACHE_KEY, []);
        $state['updated_at'] = now()->toISOString();
        Cache::forever(self::CACHE_KEY, [
            'status' => $state['status'] ?? 'idle',
            'draw_count' => (int) ($state['draw_count'] ?? $currentState['draw_count'] ?? 1),
            'digit_delay_ms' => (int) ($state['digit_delay_ms'] ?? $currentState['digit_delay_ms'] ?? 350),
            'prize_id' => $state['prize_id'] ?? null,
            'winner_id' => $state['winner_id'] ?? null,
            'winner_ids' => $state['winner_ids'] ?? [],
            'rolling_at' => $state['rolling_at'] ?? null,
            'updated_at' => $state['updated_at'],
        ]);

        return $this->getCurrentState();
    }

    /**
     * Set digit animation lock delay in milliseconds.
     */
    public function setDigitDelay(int $ms): array
    {
        $currentState = $this->getCurrentState();
        $currentState['digit_delay_ms'] = max(100, min(3000, $ms));

        return $this->setState($currentState);
    }

    /**
     * Set draw count quantity (1 to 5 winners).
     */
    public function setDrawCount(int $count): array
    {
        $currentState = $this->getCurrentState();
        $currentState['draw_count'] = max(1, min(5, $count));

        return $this->setState($currentState);
    }

    /**
     * Set active prize to be raffled.
     */
    public function selectPrize(int $prizeId): array
    {
        $prize = Prize::findOrFail($prizeId);

        if (!$prize->hasQuota()) {
            throw new RuntimeException("Hadiah {$prize->name} sudah habis kuotanya.");
        }

        $currentState = $this->getCurrentState();

        return $this->setState([
            'status' => 'idle',
            'draw_count' => $currentState['draw_count'] ?? 1,
            'prize_id' => $prize->id,
            'winner_id' => null,
            'winner_ids' => [],
            'rolling_at' => null,
        ]);
    }

    /**
     * Start the rolling animation on display for 1 to 5 winners.
     */
    public function startRolling(int $drawCount = 1): array
    {
        $drawCount = max(1, min(5, $drawCount));
        $state = $this->getCurrentState();

        if (empty($state['prize_id'])) {
            throw new RuntimeException("Silakan pilih hadiah yang akan diundi terlebih dahulu.");
        }

        $prize = Prize::find($state['prize_id']);
        if (!$prize || !$prize->hasQuota()) {
            throw new RuntimeException("Hadiah yang dipilih tidak tersedia atau kuotanya telah habis.");
        }

        if ($prize->remaining_quota < $drawCount) {
            throw new RuntimeException("Sisa kuota hadiah '{$prize->name}' hanya tersisa {$prize->remaining_quota} unit (tidak cukup untuk mengundi {$drawCount} sekaligus).");
        }

        $eligibleCount = Coupon::eligible()->count();
        if ($eligibleCount < $drawCount) {
            throw new RuntimeException("Kupon tersisa yang memenuhi syarat hanya ada {$eligibleCount} kupon.");
        }

        return $this->setState([
            'status' => 'rolling',
            'draw_count' => $drawCount,
            'prize_id' => $prize->id,
            'winner_id' => null,
            'winner_ids' => [],
            'rolling_at' => now()->toISOString(),
        ]);
    }

    /**
     * Draw 1 or multiple winners using RNG and stop rolling.
     *
     * @return Collection|Winner
     */
    public function drawWinners(int $drawCount = 1)
    {
        $drawCount = max(1, min(5, $drawCount));
        $state = $this->getCurrentState();

        if (empty($state['prize_id'])) {
            throw new RuntimeException("Tidak ada hadiah yang dipilih untuk pengundian.");
        }

        return DB::transaction(function () use ($state, $drawCount) {
            $prize = Prize::lockForUpdate()->find($state['prize_id']);

            if (!$prize || !$prize->hasQuota()) {
                throw new RuntimeException("Kuota hadiah '{$prize?->name}' sudah habis.");
            }

            if ($prize->remaining_quota < $drawCount) {
                throw new RuntimeException("Sisa kuota hadiah '{$prize->name}' hanya tersisa {$prize->remaining_quota} unit.");
            }

            // Pick random eligible coupons
            $winningCoupons = Coupon::eligible()
                ->inRandomOrder()
                ->lockForUpdate()
                ->limit($drawCount)
                ->get();

            if ($winningCoupons->count() < $drawCount) {
                throw new RuntimeException("Tidak ada cukup kupon tersisa untuk mengundi {$drawCount} pemenang.");
            }

            $createdWinners = collect();
            foreach ($winningCoupons as $coupon) {
                $createdWinners->push(Winner::create([
                    'coupon_id' => $coupon->id,
                    'prize_id' => $prize->id,
                    'status' => 'valid',
                ]));
            }

            $winnerIds = $createdWinners->pluck('id')->toArray();

            // Update shared raffle state
            $this->setState([
                'status' => 'winner',
                'draw_count' => $drawCount,
                'prize_id' => $prize->id,
                'winner_id' => $winnerIds[0] ?? null,
                'winner_ids' => $winnerIds,
                'rolling_at' => null,
            ]);

            return $drawCount === 1 ? $createdWinners->first() : $createdWinners;
        });
    }

    /**
     * Alias for drawing a single winner.
     */
    public function drawWinner(int $drawCount = 1)
    {
        return $this->drawWinners($drawCount);
    }

    /**
     * Annul a winner and restore prize quota.
     */
    public function annulWinner(int $winnerId): Winner
    {
        return DB::transaction(function () use ($winnerId) {
            $winner = Winner::findOrFail($winnerId);
            $winner->annul();

            $currentState = $this->getCurrentState();
            if ($currentState['winner_id'] === $winner->id || in_array($winner->id, $currentState['winner_ids'] ?? [])) {
                $this->setState([
                    'status' => 'idle',
                    'draw_count' => $currentState['draw_count'] ?? 1,
                    'prize_id' => $winner->prize_id,
                    'winner_id' => null,
                    'winner_ids' => [],
                    'rolling_at' => null,
                ]);
            }

            return $winner;
        });
    }

    /**
     * Reset current raffle state to idle.
     */
    public function resetState(bool $clearPrize = false): array
    {
        $currentState = $this->getCurrentState();

        return $this->setState([
            'status' => 'idle',
            'draw_count' => $currentState['draw_count'] ?? 1,
            'prize_id' => $clearPrize ? null : ($currentState['prize_id'] ?? null),
            'winner_id' => null,
            'winner_ids' => [],
            'rolling_at' => null,
        ]);
    }

    /**
     * Get overall statistics for dashboard.
     */
    public function getDashboardStats(): array
    {
        $totalCoupons = Coupon::count();
        $eligibleCoupons = Coupon::eligible()->count();
        $totalPrizes = Prize::count();
        $totalQuota = (int) Prize::sum('quota');
        $validWinnersCount = Winner::valid()->count();
        $annulledWinnersCount = Winner::annulled()->count();
        $remainingQuota = max(0, $totalQuota - $validWinnersCount);

        return [
            'total_coupons' => $totalCoupons,
            'eligible_coupons' => $eligibleCoupons,
            'burnt_coupons' => $totalCoupons - $eligibleCoupons,
            'total_prizes' => $totalPrizes,
            'total_quota' => $totalQuota,
            'remaining_quota' => $remainingQuota,
            'valid_winners' => $validWinnersCount,
            'annulled_winners' => $annulledWinnersCount,
        ];
    }
}
