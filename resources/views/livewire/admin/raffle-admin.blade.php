<div wire:poll.2500ms="refreshRaffleState"
     x-data="{
         channel: null,
         init() {
             if (typeof BroadcastChannel !== 'undefined') {
                 this.channel = new BroadcastChannel('raffle_channel');
             }
             window.addEventListener('raffle-action', (e) => {
                 const payload = e.detail[0] || e.detail;
                 if (payload) {
                     if (this.channel) {
                         this.channel.postMessage(payload);
                     }
                     try {
                         payload.timestamp = Date.now();
                         localStorage.setItem('raffle_sync_event', JSON.stringify(payload));
                     } catch (err) {}
                 }
             });
         }
     }"
     class="space-y-6">

    <!-- Flash Alert Messages -->
    @if ($successMessage)
        <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-200 shadow-lg shadow-emerald-950/50 animate-fade-in">
            <div class="flex items-center space-x-3">
                <span class="text-xl">✅</span>
                <span class="text-sm font-semibold">{{ $successMessage }}</span>
            </div>
            <button wire:click="clearAlerts" class="text-emerald-400 hover:text-emerald-200 text-sm font-bold">&times;</button>
        </div>
    @endif

    @if ($errorMessage)
        <div class="flex items-center justify-between p-4 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-200 shadow-lg shadow-rose-950/50 animate-fade-in">
            <div class="flex items-center space-x-3">
                <span class="text-xl">⚠️</span>
                <span class="text-sm font-semibold">{{ $errorMessage }}</span>
            </div>
            <button wire:click="clearAlerts" class="text-rose-400 hover:text-rose-200 text-sm font-bold">&times;</button>
        </div>
    @endif

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <!-- Card 1: Eligible Coupons -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-md flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-2xl font-bold">
                🎟️
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kupon Siap Diundi</p>
                <div class="flex items-baseline space-x-1.5 mt-0.5">
                    <span class="text-2xl font-extrabold text-white font-mono-num">{{ number_format($stats['eligible_coupons']) }}</span>
                    <span class="text-xs text-slate-500">/ {{ number_format($stats['total_coupons']) }}</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Remaining Prize Quota -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-md flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-2xl font-bold">
                🎁
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sisa Kuota Hadiah</p>
                <div class="flex items-baseline space-x-1.5 mt-0.5">
                    <span class="text-2xl font-extrabold text-amber-400 font-mono-num">{{ number_format($stats['remaining_quota']) }}</span>
                    <span class="text-xs text-slate-500">/ {{ number_format($stats['total_quota']) }} unit</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Valid Winners -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-md flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-2xl font-bold">
                🏆
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pemenang Sah</p>
                <div class="flex items-baseline space-x-1.5 mt-0.5">
                    <span class="text-2xl font-extrabold text-emerald-400 font-mono-num">{{ number_format($stats['valid_winners']) }}</span>
                    <span class="text-xs text-slate-500">pemenang</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Annulled / Burnt -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-md flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400 text-2xl font-bold">
                🚫
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kupon Dianulir</p>
                <div class="flex items-baseline space-x-1.5 mt-0.5">
                    <span class="text-2xl font-extrabold text-rose-400 font-mono-num">{{ number_format($stats['annulled_winners']) }}</span>
                    <span class="text-xs text-slate-500">hangus</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-slate-800/80 flex items-center space-x-2 overflow-x-auto pb-1">
        <button wire:click="setTab('raffle')"
                class="px-4 py-2.5 rounded-xl font-bold text-sm flex items-center space-x-2 transition-all {{ $activeTab === 'raffle' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
            <span>🎯</span>
            <span>Raffle Controller</span>
        </button>
        <button wire:click="setTab('winners')"
                class="px-4 py-2.5 rounded-xl font-bold text-sm flex items-center space-x-2 transition-all {{ $activeTab === 'winners' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
            <span>🏆</span>
            <span>Daftar Pemenang</span>
            <span class="px-2 py-0.5 rounded-full text-xs {{ $activeTab === 'winners' ? 'bg-slate-950/30 text-slate-950' : 'bg-slate-800 text-slate-300' }}">{{ $stats['valid_winners'] }}</span>
        </button>
        <button wire:click="setTab('prizes')"
                class="px-4 py-2.5 rounded-xl font-bold text-sm flex items-center space-x-2 transition-all {{ $activeTab === 'prizes' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
            <span>🎁</span>
            <span>Manajemen Hadiah</span>
            <span class="px-2 py-0.5 rounded-full text-xs {{ $activeTab === 'prizes' ? 'bg-slate-950/30 text-slate-950' : 'bg-slate-800 text-slate-300' }}">{{ $stats['total_prizes'] }}</span>
        </button>
        <button wire:click="setTab('coupons')"
                class="px-4 py-2.5 rounded-xl font-bold text-sm flex items-center space-x-2 transition-all {{ $activeTab === 'coupons' ? 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
            <span>🎟️</span>
            <span>Manajemen Kupon</span>
            <span class="px-2 py-0.5 rounded-full text-xs {{ $activeTab === 'coupons' ? 'bg-slate-950/30 text-slate-950' : 'bg-slate-800 text-slate-300' }}">{{ number_format($stats['total_coupons']) }}</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: RAFFLE CONTROLLER (MAIN ENGINE) -->
    <!-- ========================================================================= -->
    @if ($activeTab === 'raffle')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Main Raffle Action Deck -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. Active Prize Selector Card -->
                <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                                <span>🎁</span>
                                <span>Pilih Hadiah yang Akan Diundi</span>
                            </h2>
                            <p class="text-xs text-slate-400">Pastikan hadiah memiliki sisa kuota yang cukup sebelum memulai pengundian.</p>
                        </div>
                    </div>

                    <!-- Prize Select Dropdown -->
                    <div class="relative">
                        <select wire:model.live="selectedPrizeId"
                                class="w-full bg-slate-950 border border-slate-700 text-white rounded-2xl px-4 py-3.5 text-base font-semibold focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent">
                            <option value="">-- Pilih Hadiah --</option>
                            @foreach ($allPrizes as $prize)
                                <option value="{{ $prize->id }}" {{ $prize->remaining_quota <= 0 ? 'disabled' : '' }}>
                                    {{ $prize->name }} &bull; Sisa Kuota: {{ $prize->remaining_quota }}/{{ $prize->quota }} unit {{ $prize->remaining_quota <= 0 ? '(HABIS)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Active Prize Preview Banner -->
                    @if (!empty($raffleState['prize']))
                        <div class="mt-4 p-4 rounded-2xl bg-gradient-to-r from-amber-500/10 via-yellow-500/5 to-transparent border border-amber-500/30 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-400/40 flex items-center justify-center text-2xl">
                                    🏆
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Hadiah Aktif di Layar</span>
                                    <h3 class="text-lg font-extrabold text-white">{{ $raffleState['prize']['name'] }}</h3>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 block">Sisa Kuota</span>
                                <span class="text-xl font-extrabold text-amber-400 font-mono-num">
                                    {{ $raffleState['prize']['remaining_quota'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $raffleState['prize']['quota'] }}</span>
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Multi-Winner Batch Selector -->
                    <div class="mt-4 pt-4 border-t border-slate-800 flex flex-col xl:flex-row xl:items-center justify-between gap-3 text-left">
                        <span class="text-xs font-bold text-slate-300 flex items-center space-x-1.5 shrink-0">
                            <span>🔢</span>
                            <span>Jumlah Pemenang Sekaligus:</span>
                        </span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach ([1, 2, 3, 4, 5] as $num)
                                <button type="button"
                                        wire:click="setDrawCount({{ $num }})"
                                        class="px-3 py-1.5 rounded-xl text-xs font-black transition-all border {{ $drawCount === $num ? 'bg-amber-500 text-slate-950 border-amber-400 shadow-md shadow-amber-500/20 scale-105' : 'bg-slate-950 text-slate-300 border-slate-700 hover:border-slate-500' }}">
                                    {{ $num }} {{ $num === 1 ? 'Pemenang' : 'Orang' }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Animation Speed Selector -->
                    <div class="mt-3 pt-3 border-t border-slate-800/60 flex flex-col xl:flex-row xl:items-center justify-between gap-3 text-left">
                        <span class="text-xs font-bold text-slate-300 flex items-center space-x-1.5 shrink-0">
                            <span>⏱️</span>
                            <span>Kecepatan Animasi per Digit:</span>
                        </span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach ([
                                150 => '⚡ Cepat (150ms)',
                                350 => '🎯 Normal (350ms)',
                                700 => '🎭 Dramatis (700ms)',
                                1200 => '🔥 Super Dramatis (1.2s)'
                            ] as $ms => $label)
                                <button type="button"
                                        wire:click="setDigitDelay({{ $ms }})"
                                        class="px-2.5 py-1.5 rounded-xl text-[11px] font-bold transition-all border {{ ($digitDelayMs ?? 350) === $ms ? 'bg-indigo-600 text-white border-indigo-400 shadow-md shadow-indigo-500/30 scale-105' : 'bg-slate-950 text-slate-400 border-slate-800 hover:border-slate-600' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 2. Theatrical Control Console -->
                <div class="p-6 md:p-8 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-2xl text-center space-y-6">

                    <!-- State Status Badge -->
                    <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border max-w-full truncate
                        @if ($raffleState['status'] === 'rolling') bg-amber-500/20 text-amber-300 border-amber-500/40 animate-pulse
                        @elseif ($raffleState['status'] === 'winner') bg-emerald-500/20 text-emerald-300 border-emerald-500/40
                        @else bg-slate-800 text-slate-300 border-slate-700 @endif">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0
                            @if ($raffleState['status'] === 'rolling') bg-amber-400 animate-ping
                            @elseif ($raffleState['status'] === 'winner') bg-emerald-400
                            @else bg-slate-500 @endif"></span>
                        <span class="truncate">Status Mesin: {{ strtoupper($raffleState['status']) }} (Batch: {{ $drawCount }} Pemenang)</span>
                    </div>

                    <!-- STATE 1: IDLE -->
                    @if ($raffleState['status'] === 'idle')
                        <div class="py-6 space-y-4">
                            <div class="w-20 h-20 md:w-24 md:h-24 mx-auto rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center text-3xl md:text-4xl shadow-inner">
                                🎲
                            </div>
                            <p class="text-slate-400 text-sm max-w-md mx-auto">
                                Mesin siap mengundi <strong class="text-amber-400">{{ $drawCount }} pemenang</strong> sekaligus. Tekan tombol di bawah untuk memicu animasi.
                            </p>

                            <div class="pt-4">
                                <button wire:click="startRolling"
                                        @if(!$selectedPrizeId || empty($raffleState['prize']) || $raffleState['prize']['remaining_quota'] < $drawCount || $stats['eligible_coupons'] < $drawCount) disabled @endif
                                        class="w-full sm:w-auto px-8 sm:px-10 py-4 sm:py-5 rounded-2xl bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 hover:from-amber-400 hover:to-yellow-300 text-slate-950 text-lg sm:text-xl font-black shadow-2xl shadow-amber-500/40 transition-all transform hover:-translate-y-1 active:translate-y-0 disabled:opacity-40 disabled:cursor-not-allowed disabled:transform-none">
                                    ▶ MULAI PUTAR ({{ $drawCount }} PEMENANG)
                                </button>
                            </div>
                        </div>

                    <!-- STATE 2: ROLLING -->
                    @elseif ($raffleState['status'] === 'rolling')
                        <div class="py-6 space-y-6">
                            <div class="relative w-24 h-24 md:w-28 md:h-28 mx-auto">
                                <div class="absolute inset-0 rounded-full bg-amber-500/20 animate-ping"></div>
                                <div class="relative w-24 h-24 md:w-28 md:h-28 rounded-full bg-gradient-to-tr from-amber-500 to-yellow-300 flex items-center justify-center text-4xl md:text-5xl shadow-2xl shadow-amber-500/50 animate-spin">
                                    ⚙️
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xl sm:text-2xl font-black text-amber-300 uppercase tracking-widest animate-pulse">
                                    SEDANG MENGACAK {{ $drawCount }} NOMOR KUPON...
                                </h3>
                                <p class="text-xs text-slate-400 mt-1">Layar display sedang menampilkan animasi pengacakan angka.</p>
                            </div>

                            <div class="pt-2">
                                <button wire:click="drawWinner"
                                        class="w-full sm:w-auto px-8 sm:px-12 py-5 sm:py-6 rounded-2xl bg-gradient-to-r from-rose-600 via-red-500 to-rose-600 hover:from-rose-500 hover:to-red-400 text-white text-xl sm:text-2xl font-black shadow-2xl shadow-rose-600/50 transition-all transform hover:-translate-y-1 active:translate-y-0 animate-bounce">
                                    🛑 STOP & TAMPILKAN PEMENANG
                                </button>
                            </div>
                        </div>

                    <!-- STATE 3: WINNER (2x2 GRID FOR 4 WINNERS) -->
                    @elseif ($raffleState['status'] === 'winner')
                        <div class="py-4 space-y-6">
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest block">🎉 Selamat Kepada {{ count($raffleState['winners'] ?? []) }} Pemenang!</span>

                            <!-- Multi-Winner Cards Centered Grid -->
                            <div class="flex flex-wrap justify-center items-center gap-3">
                                @foreach ($raffleState['winners'] ?? [] as $wItem)
                                    <div class="p-4 rounded-2xl bg-gradient-to-b from-emerald-950/60 to-slate-900 border border-emerald-500/50 shadow-xl text-center relative space-y-1.5 overflow-hidden transition-all
                                        {{ count($raffleState['winners'] ?? []) === 1 ? 'w-full max-w-md' : '' }}
                                        {{ count($raffleState['winners'] ?? []) === 2 || count($raffleState['winners'] ?? []) === 4 ? 'w-full sm:w-[calc(50%-0.5rem)] max-w-sm' : '' }}
                                        {{ count($raffleState['winners'] ?? []) === 3 || count($raffleState['winners'] ?? []) === 5 ? 'w-full sm:w-[calc(33.33%-0.5rem)] max-w-xs' : '' }}">
                                        <div class="text-2xl md:text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-400 font-mono-num">
                                            {{ $wItem['coupon_number'] }}
                                        </div>
                                        <div class="text-xs text-emerald-300 font-medium truncate">
                                            Hadiah: {{ $wItem['prize_name'] }}
                                        </div>
                                        <div class="pt-2">
                                            <button wire:click="confirmAnnul({{ $wItem['id'] }})"
                                                    class="px-2.5 py-1 rounded-lg bg-rose-950/80 hover:bg-rose-900 border border-rose-800 text-rose-300 text-[11px] font-semibold transition-all">
                                                Anulir Pemenang Ini
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                                <button wire:click="resetRaffle"
                                        class="px-6 py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all">
                                    🔄 Undi Hadiah Selanjutnya
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right 1 Col: Live History & Quick Info -->
            <div class="space-y-6">

                <!-- 1. Live Winners Sidebar -->
                <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-white flex items-center space-x-2">
                            <span>📜</span>
                            <span>Pemenang Terkini</span>
                        </h3>
                        <button wire:click="setTab('winners')" class="text-xs text-amber-400 hover:text-amber-300 font-semibold">
                            Lihat Semua &rarr;
                        </button>
                    </div>

                    <div class="space-y-2.5 max-h-96 overflow-y-auto pr-1">
                        @forelse ($winners->take(6) as $winnerItem)
                            <div class="p-3 rounded-2xl bg-slate-950/80 border {{ $winnerItem->status === 'valid' ? 'border-emerald-900/50' : 'border-rose-900/50 opacity-60' }} flex items-center justify-between text-xs">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono font-black text-amber-400">{{ $winnerItem->coupon?->coupon_number }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $winnerItem->status === 'valid' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                            {{ strtoupper($winnerItem->status) }}
                                        </span>
                                    </div>
                                    <p class="font-semibold text-slate-200 mt-0.5 truncate max-w-[140px]">{{ $winnerItem->coupon?->name ?? 'Tanpa Nama' }}</p>
                                    <p class="text-[11px] text-slate-400 truncate max-w-[140px]">{{ $winnerItem->prize?->name }}</p>
                                </div>
                                <div>
                                    @if ($winnerItem->status === 'valid')
                                        <button wire:click="confirmAnnul({{ $winnerItem->id }})"
                                                class="px-2.5 py-1 rounded-lg bg-rose-950/80 hover:bg-rose-900 border border-rose-800 text-rose-300 text-[11px] font-semibold">
                                            Anulir
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-500 text-xs">
                                Belum ada pemenang yang diundi.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 2. Dual-View Instructions Box -->
                <div class="p-5 rounded-3xl bg-indigo-950/40 border border-indigo-800/40 text-xs space-y-3">
                    <div class="flex items-center space-x-2 text-indigo-300 font-bold">
                        <span>💡</span>
                        <span>Petunjuk Dual-Screen</span>
                    </div>
                    <ul class="space-y-1.5 text-slate-300 list-disc list-inside">
                        <li>Buka URL <code class="text-amber-300 bg-slate-900 px-1 py-0.5 rounded font-mono">/display</code> di layar monitor kedua/proyektor.</li>
                        <li>Tekan <kbd class="px-1 py-0.5 bg-slate-800 rounded font-mono text-slate-300">F11</kbd> pada browser proyektor untuk fullscreen.</li>
                        <li>Gunakan panel ini untuk mengontrol jalannya pengundian.</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 2: DAFTAR PEMENANG (WINNERS) -->
    <!-- ========================================================================= -->
    @if ($activeTab === 'winners')
        <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-6">
            <!-- Header & Filter Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                        <span>🏆</span>
                        <span>Daftar Pemenang Undian</span>
                    </h2>
                    <p class="text-xs text-slate-400">Kelola dan pantau seluruh pemenang yang telah diundi.</p>
                </div>

                <!-- Export CSV Button -->
                <button wire:click="exportWinnersCsv"
                        class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Download CSV Pemenang</span>
                </button>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input wire:model.live.debounce.300ms="winnerSearch"
                       type="text"
                       placeholder="Cari kupon atau nama peserta..."
                       class="bg-slate-950 border border-slate-800 text-white rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-amber-400 focus:outline-none">

                <select wire:model.live="winnerStatusFilter"
                        class="bg-slate-950 border border-slate-800 text-white rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="valid">Sah (Valid)</option>
                    <option value="annulled">Dianulir (Annulled)</option>
                </select>

                <select wire:model.live="winnerPrizeFilter"
                        class="bg-slate-950 border border-slate-800 text-white rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    <option value="">Semua Hadiah</option>
                    @foreach ($allPrizes as $prize)
                        <option value="{{ $prize->id }}">{{ $prize->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/80 text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="p-3.5">#</th>
                            <th class="p-3.5">Nomor Kupon</th>
                            <th class="p-3.5">Nama Peserta</th>
                            <th class="p-3.5">Hadiah</th>
                            <th class="p-3.5">Waktu Pengundian</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse ($winners as $index => $w)
                            <tr class="hover:bg-slate-800/40 transition-colors {{ $w->status === 'annulled' ? 'opacity-50' : '' }}">
                                <td class="p-3.5 font-mono text-slate-500">{{ $winners->firstItem() + $index }}</td>
                                <td class="p-3.5 font-mono font-bold text-amber-400 text-sm">{{ $w->coupon?->coupon_number ?? '-' }}</td>
                                <td class="p-3.5 font-semibold text-white">{{ $w->coupon?->name ?? 'Tanpa Nama' }}</td>
                                <td class="p-3.5 text-slate-300">{{ $w->prize?->name ?? '-' }}</td>
                                <td class="p-3.5 text-slate-400 font-mono">{{ $w->created_at?->format('H:i:s, d M Y') }}</td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $w->status === 'valid' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                        {{ strtoupper($w->status) }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-right">
                                    @if ($w->status === 'valid')
                                        <button wire:click="confirmAnnul({{ $w->id }})"
                                                class="px-3 py-1 rounded-lg bg-rose-950/80 hover:bg-rose-900 border border-rose-800 text-rose-300 text-xs font-semibold transition-colors">
                                            Anulir
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-500 italic">Hangus</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-500">
                                    Tidak ada data pemenang yang sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $winners->links() }}
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 3: MANAJEMEN HADIAH (PRIZES) -->
    <!-- ========================================================================= -->
    @if ($activeTab === 'prizes')
        <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                        <span>🎁</span>
                        <span>Manajemen Hadiah</span>
                    </h2>
                    <p class="text-xs text-slate-400">Atur daftar hadiah dan kuota masing-masing untuk pengundian.</p>
                </div>
                <button wire:click="openAddPrizeModal"
                        class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-md shadow-amber-500/30 transition-all">
                    <span>+ Tambah Hadiah Baru</span>
                </button>
            </div>

            <!-- Prizes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($allPrizes as $prize)
                    <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 flex flex-col justify-between space-y-4 hover:border-slate-700 transition-all">
                        <div class="space-y-2">
                            <div class="flex items-start justify-between">
                                <h3 class="font-bold text-white text-base">{{ $prize->name }}</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $prize->remaining_quota > 0 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                    {{ $prize->remaining_quota > 0 ? 'Tersedia' : 'Habis' }}
                                </span>
                            </div>

                            <!-- Quota Progress Bar -->
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs text-slate-400">
                                    <span>Pemenang: <strong class="text-white">{{ $prize->valid_winners_count }}</strong></span>
                                    <span>Sisa: <strong class="text-amber-400 font-mono-num">{{ $prize->remaining_quota }}</strong> / {{ $prize->quota }} unit</span>
                                </div>
                                <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-500 to-yellow-400 transition-all"
                                         style="width: {{ $prize->quota > 0 ? min(100, ($prize->valid_winners_count / $prize->quota) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-800/80">
                            <button wire:click="openEditPrizeModal({{ $prize->id }})"
                                    class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold">
                                Edit
                            </button>
                            <button wire:click="confirmDeletePrize({{ $prize->id }})"
                                    class="px-3 py-1 rounded-lg bg-rose-950/60 hover:bg-rose-900 text-rose-300 text-xs font-semibold">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-slate-500 text-sm">
                        Belum ada hadiah yang didaftarkan. Klik tombol Tambah Hadiah Baru di atas.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TAB 4: MANAJEMEN KUPON (COUPONS) -->
    <!-- ========================================================================= -->
    @if ($activeTab === 'coupons')
        <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-6">
            <!-- Header & Action Buttons -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                        <span>🎟️</span>
                        <span>Manajemen Kupon Peserta</span>
                    </h2>
                    <p class="text-xs text-slate-400">Kelola kupon undian, generate nomor secara massal, atau impor data dari CSV.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="openGenerateModal"
                            class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition-all shadow-md shadow-amber-500/20">
                        ⚡ Generate Range Kupon
                    </button>
                    <button wire:click="openImportModal"
                            class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-md shadow-indigo-600/20">
                        📁 Import CSV
                    </button>
                    <button wire:click="confirmResetCoupons"
                            class="px-3.5 py-2 rounded-xl bg-rose-950 hover:bg-rose-900 border border-rose-800 text-rose-300 text-xs font-bold transition-all">
                        🗑️ Reset Semua Kupon
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input wire:model.live.debounce.300ms="couponSearch"
                       type="text"
                       placeholder="Cari nomor kupon atau nama peserta..."
                       class="bg-slate-950 border border-slate-800 text-white rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-amber-400 focus:outline-none">

                <select wire:model.live="couponFilter"
                        class="bg-slate-950 border border-slate-800 text-white rounded-xl px-3.5 py-2 text-xs focus:ring-1 focus:ring-amber-400 focus:outline-none">
                    <option value="">Semua Kupon</option>
                    <option value="eligible">Siap Diundi (Eligible)</option>
                    <option value="won">Sudah Pernah Diundi (Menang / Anulir)</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/80 text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="p-3.5">#</th>
                            <th class="p-3.5">Nomor Kupon</th>
                            <th class="p-3.5">Nama Pemilik Kupon</th>
                            <th class="p-3.5">Status Partisipasi</th>
                            <th class="p-3.5">Dibuat Pada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse ($coupons as $index => $c)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="p-3.5 font-mono text-slate-500">{{ $coupons->firstItem() + $index }}</td>
                                <td class="p-3.5 font-mono font-bold text-amber-400 text-sm">{{ $c->coupon_number }}</td>
                                <td class="p-3.5 font-semibold text-white">{{ $c->name ?? '-' }}</td>
                                <td class="p-3.5">
                                    @if ($c->winner)
                                        @if ($c->winner->status === 'valid')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                                🏆 Menang ({{ $c->winner->prize?->name }})
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                                🚫 Hangus (Dianulir)
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                            🎟️ Siap Diundi
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-slate-400 font-mono">{{ $c->created_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500">
                                    Tidak ada data kupon. Silakan generate atau impor kupon terlebih dahulu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $coupons->links() }}
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- MODALS SECTION -->
    <!-- ========================================================================= -->

    <!-- 1. Modal Anulir Pemenang -->
    @if ($showAnnulModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-2xl mx-auto">
                    ⚠️
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-bold text-white">Konfirmasi Anulir Pemenang</h3>
                    <p class="text-xs text-slate-300 mt-1">
                        Apakah Anda yakin ingin menganulir pemenang ini? Kuota hadiah akan dikembalikan, dan kupon ini akan dianggap <strong>hangus</strong> (tidak akan bisa menang lagi).
                    </p>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button wire:click="cancelAnnul" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                        Batal
                    </button>
                    <button wire:click="executeAnnul" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-md shadow-rose-600/30">
                        Ya, Anulir Pemenang
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. Modal Add / Edit Prize -->
    @if ($showPrizeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-white">
                    {{ $editingPrizeId ? 'Edit Data Hadiah' : 'Tambah Hadiah Baru' }}
                </h3>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Nama Hadiah</label>
                        <input wire:model="prizeName" type="text" placeholder="Contoh: Sepeda Gunung Polygon"
                               class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3.5 py-2 focus:ring-1 focus:ring-amber-400 focus:outline-none">
                        @error('prizeName') <span class="text-rose-400 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Total Kuota Unit</label>
                        <input wire:model="prizeQuota" type="number" min="1" max="10000"
                               class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3.5 py-2 focus:ring-1 focus:ring-amber-400 focus:outline-none">
                        @error('prizeQuota') <span class="text-rose-400 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button wire:click="closePrizeModal" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                        Batal
                    </button>
                    <button wire:click="savePrize" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold">
                        Simpan Hadiah
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 3. Modal Delete Prize -->
    @if ($showDeletePrizeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <div class="text-center">
                    <div class="text-3xl mb-2">🗑️</div>
                    <h3 class="text-lg font-bold text-white">Hapus Hadiah</h3>
                    <p class="text-xs text-slate-300 mt-1">Apakah Anda yakin ingin menghapus hadiah ini? Semua data terkait hadiah ini akan dihapus.</p>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button wire:click="cancelDeletePrize" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                        Batal
                    </button>
                    <button wire:click="executeDeletePrize" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 4. Modal Generate Coupons -->
    @if ($showGenerateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-white">Generate Range Kupon Otomatis</h3>
                <p class="text-xs text-slate-400">Buat nomor kupon berurutan secara otomatis.</p>
                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Prefix Kupon</label>
                            <input wire:model="genPrefix" type="text" placeholder="Contoh: JLS-"
                                   class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Panjang Digit (Padding)</label>
                            <input wire:model="genPadding" type="number" min="1" max="8"
                                   class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3 py-2">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Mulai Dari</label>
                            <input wire:model="genStart" type="number" min="1" max="50000"
                                   class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Sampai Dengan</label>
                            <input wire:model="genEnd" type="number" min="1" max="50000"
                                   class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3 py-2">
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 pt-1">
                        <input wire:model="genIncludeNames" type="checkbox" id="genNames" class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-0">
                        <label for="genNames" class="text-slate-300">Generate nama peserta contoh (Faker Indonesia)</label>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button wire:click="closeGenerateModal" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                        Batal
                    </button>
                    <button wire:click="generateCoupons" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold">
                        ⚡ Mulai Generate
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 5. Modal Import CSV -->
    @if ($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-white">Import Kupon dari CSV</h3>
                <p class="text-xs text-slate-400">File CSV harus memiliki kolom nomor kupon (misal: <code>coupon_number</code>) dan nama (opsional).</p>
                <div class="space-y-3 text-xs">
                    <input wire:model="csvFile" type="file" accept=".csv,.txt"
                           class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs">
                    @error('csvFile') <span class="text-rose-400 text-[11px]">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button wire:click="closeImportModal" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                        Batal
                    </button>
                    <button wire:click="importCsv" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold">
                        Upload & Proses CSV
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 6. Modal Reset Coupons -->
    @if ($showResetCouponsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fade-in">
            <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <div class="text-center">
                    <div class="text-4xl mb-2">⚠️</div>
                    <h3 class="text-lg font-bold text-white">Kosongkan Semua Kupon & Pemenang?</h3>
                    <p class="text-xs text-rose-300 mt-1">PERINGATAN: Aksi ini akan menghapus semua kupon dan riwayat pemenang secara permanen!</p>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button wire:click="cancelResetCoupons" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">
                        Batal
                    </button>
                    <button wire:click="executeResetCoupons" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold">
                        Ya, Kosongkan Data
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
