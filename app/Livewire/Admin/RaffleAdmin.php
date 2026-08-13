<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use App\Models\Prize;
use App\Models\Winner;
use App\Services\RaffleService;
use Exception;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class RaffleAdmin extends Component
{
    use WithPagination, WithFileUploads;

    #[Url(as: 'tab')]
    public string $activeTab = 'raffle'; // 'raffle' | 'winners' | 'prizes' | 'coupons'

    // Raffle Controller State
    public ?int $selectedPrizeId = null;
    public int $drawCount = 1;
    public int $digitDelayMs = 350; // 150, 350, 700, 1200
    public array $raffleState = [];

    // Winners Filter & Search
    public string $winnerSearch = '';
    public string $winnerStatusFilter = '';
    public string $winnerPrizeFilter = '';

    // Prize Management Form
    public bool $showPrizeModal = false;
    public ?int $editingPrizeId = null;
    public string $prizeName = '';
    public int $prizeQuota = 1;

    // Coupon Management Form
    public string $couponSearch = '';
    public string $couponFilter = ''; // '' | 'eligible' | 'won'
    public bool $showGenerateModal = false;
    public string $genPrefix = 'JLS-';
    public int $genStart = 1;
    public int $genEnd = 1000;
    public int $genPadding = 4;
    public bool $genIncludeNames = true;

    // CSV Upload
    public $csvFile = null;
    public bool $showImportModal = false;

    // Confirmation Modals
    public ?int $annulTargetId = null;
    public ?int $deletePrizeId = null;
    public bool $showAnnulModal = false;
    public bool $showDeletePrizeModal = false;
    public bool $showResetCouponsModal = false;

    // Alert Messages
    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    protected function raffleService(): RaffleService
    {
        return app(RaffleService::class);
    }

    public function mount(): void
    {
        $this->refreshRaffleState();

        if (!empty($this->raffleState['prize_id'])) {
            $this->selectedPrizeId = $this->raffleState['prize_id'];
        } else {
            // Auto-select first prize with available quota
            $firstPrize = Prize::all()->first(fn (Prize $p) => $p->hasQuota());
            if ($firstPrize) {
                $this->selectedPrizeId = $firstPrize->id;
            }
        }
    }

    public function refreshRaffleState(): void
    {
        $this->raffleState = $this->raffleService()->getCurrentState();
        $this->drawCount = $this->raffleState['draw_count'] ?? 1;
        $this->digitDelayMs = $this->raffleState['digit_delay_ms'] ?? 350;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage('winnersPage');
        $this->resetPage('couponsPage');
        $this->clearAlerts();
    }

    public function clearAlerts(): void
    {
        $this->successMessage = null;
        $this->errorMessage = null;
    }

    // ==========================================
    // RAFFLE CONTROLLER ACTIONS
    // ==========================================

    public function setDigitDelay(int $ms): void
    {
        $this->digitDelayMs = max(100, min(3000, $ms));
        $this->raffleService()->setDigitDelay($this->digitDelayMs);
        $this->refreshRaffleState();
        $this->dispatch('raffle-action', [
            'action' => 'delay_change',
            'digit_delay_ms' => $this->digitDelayMs,
        ]);
        $this->successMessage = "Kecepatan animasi per digit diubah ke {$this->digitDelayMs}ms.";
    }

    public function updatedSelectedPrizeId($prizeId): void
    {
        if (!$prizeId) {
            return;
        }

        try {
            $this->raffleService()->selectPrize((int) $prizeId);
            $this->refreshRaffleState();
            $this->successMessage = "Hadiah aktif berhasil diubah.";
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function setDrawCount(int $count): void
    {
        $this->drawCount = max(1, min(5, $count));
        $this->raffleService()->setDrawCount($this->drawCount);
        $this->refreshRaffleState();
        $this->dispatch('raffle-action', [
            'action' => 'count_change',
            'draw_count' => $this->drawCount,
        ]);
        $this->successMessage = "Jumlah undian sekaligus diset ke {$this->drawCount} pemenang.";
    }

    public function startRolling(): void
    {
        $this->clearAlerts();

        try {
            if (!$this->selectedPrizeId) {
                throw new Exception("Silakan pilih hadiah terlebih dahulu.");
            }

            $this->raffleService()->selectPrize((int) $this->selectedPrizeId);
            $this->raffleService()->startRolling($this->drawCount);
            $this->refreshRaffleState();
            $this->dispatch('raffle-action', [
                'action' => 'rolling',
                'draw_count' => $this->drawCount,
                'prize_id' => $this->selectedPrizeId,
            ]);
            $this->successMessage = "Pengundian {$this->drawCount} pemenang dimulai! Layar display sekarang menampilkan animasi rolling.";
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function drawWinner(): void
    {
        $this->clearAlerts();

        try {
            $winners = $this->raffleService()->drawWinners($this->drawCount);
            $this->refreshRaffleState();

            $winnersArray = [];
            if ($winners instanceof \Illuminate\Support\Collection) {
                foreach ($winners as $w) {
                    $winnersArray[] = [
                        'id' => $w->id,
                        'coupon_number' => $w->coupon?->coupon_number,
                        'participant_name' => $w->coupon?->name,
                        'prize_name' => $w->prize?->name,
                    ];
                }
            } else {
                $winnersArray[] = [
                    'id' => $winners->id,
                    'coupon_number' => $winners->coupon?->coupon_number,
                    'participant_name' => $winners->coupon?->name,
                    'prize_name' => $winners->prize?->name,
                ];
            }

            $this->dispatch('raffle-action', [
                'action' => 'winner',
                'draw_count' => $this->drawCount,
                'winners' => $winnersArray,
                'coupon_number' => $winnersArray[0]['coupon_number'] ?? '',
                'winner_id' => $winnersArray[0]['id'] ?? null,
                'winner_name' => $winnersArray[0]['participant_name'] ?? '',
                'prize_name' => $winnersArray[0]['prize_name'] ?? '',
            ]);

            $countStr = count($winnersArray);
            $this->successMessage = "🎉 Berhasil mengundi {$countStr} pemenang sah!";
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function resetRaffle(): void
    {
        $this->clearAlerts();
        $this->raffleService()->resetState(false);
        $this->refreshRaffleState();
        $this->dispatch('raffle-action', ['action' => 'idle']);
        $this->successMessage = "Status pengundian di-reset ke Siap (Idle).";
    }

    // ==========================================
    // WINNER ACTIONS
    // ==========================================

    public function confirmAnnul(int $winnerId): void
    {
        $this->annulTargetId = $winnerId;
        $this->showAnnulModal = true;
    }

    public function cancelAnnul(): void
    {
        $this->annulTargetId = null;
        $this->showAnnulModal = false;
    }

    public function executeAnnul(): void
    {
        if (!$this->annulTargetId) {
            return;
        }

        try {
            $winner = $this->raffleService()->annulWinner($this->annulTargetId);
            $this->showAnnulModal = false;
            $this->annulTargetId = null;
            $this->refreshRaffleState();
            $this->successMessage = "Kupon {$winner->coupon?->coupon_number} berhasil dianulir. Kuota hadiah telah dikembalikan.";
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function exportWinnersCsv()
    {
        $winners = Winner::with(['coupon', 'prize'])->orderBy('id', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data-pemenang-undian-' . date('Y-m-d-His') . '.csv"',
        ];

        $callback = function () use ($winners) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nomor Kupon', 'Nama Peserta', 'Hadiah', 'Status', 'Waktu Menang']);

            foreach ($winners as $w) {
                fputcsv($file, [
                    $w->id,
                    $w->coupon?->coupon_number ?? '-',
                    $w->coupon?->name ?? '-',
                    $w->prize?->name ?? '-',
                    strtoupper($w->status),
                    $w->created_at?->format('Y-m-d H:i:s') ?? '-',
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // ==========================================
    // PRIZE MANAGEMENT ACTIONS
    // ==========================================

    public function openAddPrizeModal(): void
    {
        $this->editingPrizeId = null;
        $this->prizeName = '';
        $this->prizeQuota = 1;
        $this->showPrizeModal = true;
    }

    public function openEditPrizeModal(int $prizeId): void
    {
        $prize = Prize::findOrFail($prizeId);
        $this->editingPrizeId = $prize->id;
        $this->prizeName = $prize->name;
        $this->prizeQuota = $prize->quota;
        $this->showPrizeModal = true;
    }

    public function closePrizeModal(): void
    {
        $this->showPrizeModal = false;
        $this->editingPrizeId = null;
        $this->prizeName = '';
        $this->prizeQuota = 1;
    }

    public function savePrize(): void
    {
        $this->validate([
            'prizeName' => 'required|string|max:255',
            'prizeQuota' => 'required|integer|min:1|max:10000',
        ]);

        if ($this->editingPrizeId) {
            $prize = Prize::findOrFail($this->editingPrizeId);
            $validWinnersCount = $prize->validWinners()->count();

            if ($this->prizeQuota < $validWinnersCount) {
                $this->errorMessage = "Kuota tidak boleh lebih kecil dari jumlah pemenang valid yang sudah ada ({$validWinnersCount}).";
                return;
            }

            $prize->update([
                'name' => $this->prizeName,
                'quota' => $this->prizeQuota,
            ]);
            $this->successMessage = "Hadiah '{$prize->name}' berhasil diperbarui.";
        } else {
            $prize = Prize::create([
                'name' => $this->prizeName,
                'quota' => $this->prizeQuota,
            ]);
            $this->successMessage = "Hadiah baru '{$prize->name}' berhasil ditambahkan.";
        }

        $this->closePrizeModal();
        $this->refreshRaffleState();
    }

    public function confirmDeletePrize(int $prizeId): void
    {
        $this->deletePrizeId = $prizeId;
        $this->showDeletePrizeModal = true;
    }

    public function cancelDeletePrize(): void
    {
        $this->deletePrizeId = null;
        $this->showDeletePrizeModal = false;
    }

    public function executeDeletePrize(): void
    {
        if (!$this->deletePrizeId) {
            return;
        }

        $prize = Prize::findOrFail($this->deletePrizeId);
        $prizeName = $prize->name;

        // If this prize was active, clear state
        if ($this->raffleState['prize_id'] === $prize->id) {
            $this->raffleService()->resetState(true);
        }

        $prize->delete();
        $this->deletePrizeId = null;
        $this->showDeletePrizeModal = false;
        $this->refreshRaffleState();
        $this->successMessage = "Hadiah '{$prizeName}' berhasil dihapus.";
    }

    // ==========================================
    // COUPON MANAGEMENT ACTIONS
    // ==========================================

    public function openGenerateModal(): void
    {
        $this->showGenerateModal = true;
    }

    public function closeGenerateModal(): void
    {
        $this->showGenerateModal = false;
    }

    public function generateCoupons(): void
    {
        $this->validate([
            'genPrefix' => 'nullable|string|max:10',
            'genStart' => 'required|integer|min:1|max:50000',
            'genEnd' => 'required|integer|gte:genStart|max:50000',
            'genPadding' => 'required|integer|min:1|max:8',
        ]);

        $totalCount = ($this->genEnd - $this->genStart) + 1;
        if ($totalCount > 10000) {
            $this->errorMessage = "Maksimal generate 10.000 kupon dalam satu kali proses.";
            return;
        }

        $faker = Faker::create('id_ID');
        $now = now();
        $chunk = [];
        $createdCount = 0;

        DB::beginTransaction();
        try {
            for ($i = $this->genStart; $i <= $this->genEnd; $i++) {
                $numberStr = ($this->genPrefix ?? '') . str_pad((string) $i, $this->genPadding, '0', STR_PAD_LEFT);

                $chunk[] = [
                    'coupon_number' => $numberStr,
                    'name' => $this->genIncludeNames ? $faker->name() : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($chunk) >= 250) {
                    Coupon::upsert($chunk, ['coupon_number'], ['name', 'updated_at']);
                    $createdCount += count($chunk);
                    $chunk = [];
                }
            }

            if (!empty($chunk)) {
                Coupon::upsert($chunk, ['coupon_number'], ['name', 'updated_at']);
                $createdCount += count($chunk);
            }

            DB::commit();
            $this->closeGenerateModal();
            $this->successMessage = "Berhasil membuat/memperbarui {$createdCount} kupon.";
        } catch (Exception $e) {
            DB::rollBack();
            $this->errorMessage = "Gagal membuat kupon: " . $e->getMessage();
        }
    }

    public function openImportModal(): void
    {
        $this->csvFile = null;
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->csvFile = null;
        $this->showImportModal = false;
    }

    public function importCsv(): void
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path = $this->csvFile->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        if (!$header) {
            $this->errorMessage = "File CSV kosong.";
            return;
        }

        // Map column indices
        $couponIdx = 0;
        $nameIdx = 1;

        foreach ($header as $idx => $col) {
            $colClean = strtolower(trim($col));
            if (in_array($colClean, ['coupon', 'coupon_number', 'nomor', 'nomor_kupon', 'no_kupon', 'no'])) {
                $couponIdx = $idx;
            } elseif (in_array($colClean, ['name', 'nama', 'peserta', 'nama_peserta'])) {
                $nameIdx = $idx;
            }
        }

        $now = now();
        $chunk = [];
        $importedCount = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($file)) !== false) {
                if (empty($row[$couponIdx])) {
                    continue;
                }

                $chunk[] = [
                    'coupon_number' => trim($row[$couponIdx]),
                    'name' => isset($row[$nameIdx]) && trim($row[$nameIdx]) !== '' ? trim($row[$nameIdx]) : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($chunk) >= 250) {
                    Coupon::upsert($chunk, ['coupon_number'], ['name', 'updated_at']);
                    $importedCount += count($chunk);
                    $chunk = [];
                }
            }
            fclose($file);

            if (!empty($chunk)) {
                Coupon::upsert($chunk, ['coupon_number'], ['name', 'updated_at']);
                $importedCount += count($chunk);
            }

            DB::commit();
            $this->closeImportModal();
            $this->successMessage = "Berhasil mengimpor {$importedCount} kupon dari CSV.";
        } catch (Exception $e) {
            DB::rollBack();
            $this->errorMessage = "Gagal memproses file CSV: " . $e->getMessage();
        }
    }

    public function confirmResetCoupons(): void
    {
        $this->showResetCouponsModal = true;
    }

    public function cancelResetCoupons(): void
    {
        $this->showResetCouponsModal = false;
    }

    public function executeResetCoupons(): void
    {
        Winner::truncate();
        Coupon::truncate();
        $this->raffleService()->resetState(true);
        $this->showResetCouponsModal = false;
        $this->refreshRaffleState();
        $this->successMessage = "Semua data kupon dan pemenang berhasil dikosongkan.";
    }

    // ==========================================
    // RENDER VIEW
    // ==========================================

    public function render()
    {
        $this->refreshRaffleState();
        $stats = $this->raffleService()->getDashboardStats();
        $allPrizes = Prize::withCount('validWinners')->get();

        // Query Winners
        $winnersQuery = Winner::with(['coupon', 'prize'])->orderBy('id', 'desc');
        if ($this->winnerSearch !== '') {
            $term = '%' . $this->winnerSearch . '%';
            $winnersQuery->where(function ($q) use ($term) {
                $q->whereHas('coupon', function ($cq) use ($term) {
                    $cq->where('coupon_number', 'like', $term)
                       ->orWhere('name', 'like', $term);
                })->orWhereHas('prize', function ($pq) use ($term) {
                    $pq->where('name', 'like', $term);
                });
            });
        }
        if ($this->winnerStatusFilter !== '') {
            $winnersQuery->where('status', $this->winnerStatusFilter);
        }
        if ($this->winnerPrizeFilter !== '') {
            $winnersQuery->where('prize_id', $this->winnerPrizeFilter);
        }
        $winners = $winnersQuery->paginate(10, ['*'], 'winnersPage');

        // Query Coupons
        $couponsQuery = Coupon::with('winner')->orderBy('id', 'asc');
        if ($this->couponSearch !== '') {
            $term = '%' . $this->couponSearch . '%';
            $couponsQuery->where(function ($q) use ($term) {
                $q->where('coupon_number', 'like', $term)
                   ->orWhere('name', 'like', $term);
            });
        }
        if ($this->couponFilter === 'eligible') {
            $couponsQuery->eligible();
        } elseif ($this->couponFilter === 'won') {
            $couponsQuery->whereHas('winners');
        }
        $coupons = $couponsQuery->paginate(20, ['*'], 'couponsPage');

        return view('livewire.admin.raffle-admin', [
            'stats' => $stats,
            'allPrizes' => $allPrizes,
            'winners' => $winners,
            'coupons' => $coupons,
            'drawCount' => $this->drawCount,
        ]);
    }
}
