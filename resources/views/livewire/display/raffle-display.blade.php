<div x-data="raffleDisplay()"
     x-init="init()"
     class="w-full h-full min-h-[calc(100vh-100px)] flex flex-col justify-between p-4 md:p-6 space-y-4">

    <!-- Top & Main Stage Split Container (Left Stage + Right MC Sidebar Table) -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-6 items-stretch w-full">

        <!-- ================================================================= -->
        <!-- LEFT COLUMN (3/4 Width): MAIN RAFFLE STAGE -->
        <!-- ================================================================= -->
        <div class="lg:col-span-3 flex flex-col justify-between items-center space-y-6">

            <!-- 1. Active Prize Stage Banner -->
            <div class="w-full text-center">
                <template x-if="prizeName !== ''">
                    <div class="inline-flex items-center space-x-3 px-6 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500/10 via-yellow-500/20 to-amber-500/10 border border-amber-500/40 shadow-lg shadow-amber-500/10 backdrop-blur-md animate-fade-in">
                        <span class="text-2xl md:text-3xl">🏆</span>
                        <div class="text-left">
                            <span class="text-[11px] font-extrabold text-amber-400 uppercase tracking-widest block">Hadiah yang Sedang Diundi</span>
                            <h2 class="text-xl md:text-3xl font-black text-white tracking-wide" x-text="prizeName"></h2>
                        </div>
                        <div class="hidden sm:block pl-4 border-l border-amber-500/30 text-right">
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Sisa Kuota</span>
                            <span class="text-lg font-black text-amber-400 font-mono-num">
                                <span x-text="remainingQuota"></span> unit
                            </span>
                        </div>
                    </div>
                </template>
                <template x-if="prizeName === ''">
                    <div class="inline-flex items-center space-x-2 px-5 py-2 rounded-2xl bg-slate-900/60 border border-slate-800 text-slate-400 text-sm font-semibold">
                        <span>🎲</span>
                        <span>Menunggu pemilihan hadiah oleh panitia...</span>
                    </div>
                </template>
            </div>

            <!-- 2. Middle Central Stage Display (CASINO JACKPOT REEL STAGE) -->
            <div class="w-full flex-1 flex flex-col items-center justify-center my-auto">

                <div class="w-full max-w-5xl p-6 md:p-10 rounded-[2.5rem] bg-gradient-to-b from-slate-900/95 via-slate-900/70 to-slate-950/95 border-2 transition-all duration-500 shadow-2xl relative overflow-hidden text-center"
                     :class="{
                         'border-amber-400 shadow-amber-500/30 ring-4 ring-amber-400/20': status === 'rolling' || status === 'locking',
                         'border-emerald-400 shadow-emerald-500/40 ring-4 ring-emerald-400/20': status === 'winner',
                         'border-slate-800 shadow-slate-950': status === 'idle'
                     }">

                    <!-- Glowing Backdrop Shine -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/5 via-yellow-500/5 to-transparent pointer-events-none"></div>

                    <!-- Status Indicator Label -->
                    <div class="mb-6">
                        <span class="inline-block px-4 py-1.5 rounded-full text-xs md:text-sm font-black uppercase tracking-widest border"
                              :class="{
                                  'bg-amber-500/20 text-amber-300 border-amber-400/50 animate-pulse': status === 'rolling' || status === 'locking',
                                  'bg-emerald-500/20 text-emerald-300 border-emerald-400/50': status === 'winner',
                                  'bg-slate-800 text-slate-400 border-slate-700': status === 'idle'
                              }">
                            <span x-show="status === 'rolling'">🎰 JACKPOT SPINNING: MENGACAK KUPON...</span>
                            <span x-show="status === 'locking'">🔒 JACKPOT DECELERATING: MENGUNCI DIGIT...</span>
                            <span x-show="status === 'winner'" x-text="'🎉 ' + (winners.length > 1 ? winners.length + ' PEMENANG SAH UNDIAN 🎉' : 'PEMENANG SAH UNDIAN 🎉')"></span>
                            <span x-show="status === 'idle'">⏳ MENUNGGU PENGUNDIAN DIMULAI</span>
                        </span>
                    </div>

                    <!-- 60 FPS CASINO SLOT REEL CARDS (ROLLING, LOCKING, & WINNER) -->
                    <template x-if="status === 'rolling' || status === 'locking' || status === 'winner'">
                        <div class="py-4 my-2">
                            <div class="flex flex-wrap justify-center items-center gap-4 max-w-5xl mx-auto">
                                <template x-for="(card, cardIdx) in cardsData" :key="'card-' + cardIdx">
                                    <div class="rounded-3xl bg-gradient-to-b from-slate-950/90 to-slate-900/90 border-2 shadow-2xl transition-all flex flex-col items-center justify-center space-y-2 overflow-hidden"
                                         :class="{
                                             'border-emerald-400/90 shadow-emerald-500/30 scale-105': status === 'winner',
                                             'border-amber-500/40 shadow-amber-500/10': status !== 'winner',
                                             'w-full max-w-lg p-6 md:p-8': drawCount === 1,
                                             'w-full sm:w-[calc(50%-0.75rem)] max-w-md p-4 sm:p-5': drawCount === 2 || drawCount === 4,
                                             'w-full sm:w-[calc(33.33%-0.75rem)] max-w-xs p-3 sm:p-4': drawCount === 3 || drawCount === 5
                                         }">

                                        <!-- Badge Header -->
                                        <div class="text-[10px] sm:text-xs font-black uppercase tracking-widest"
                                             :class="status === 'winner' ? 'text-emerald-400' : 'text-amber-400'"
                                             x-text="'PEMENANG #' + (cardIdx + 1)"></div>

                                        <!-- 3D SLOT MACHINE REEL CONTAINER -->
                                        <div class="flex items-center justify-center space-x-1 sm:space-x-1.5 py-1 w-full overflow-hidden">
                                            <!-- Prefix Tag -->
                                            <span class="font-black font-mono-num tracking-tighter"
                                                  :class="{
                                                      'text-3xl sm:text-5xl md:text-6xl': drawCount === 1,
                                                      'text-2xl sm:text-3xl md:text-4xl': drawCount === 2 || drawCount === 4,
                                                      'text-xl sm:text-2xl md:text-3xl': drawCount === 3 || drawCount === 5,
                                                      'text-amber-300': status === 'winner',
                                                      'text-amber-400': status !== 'winner'
                                                  }">JLS-</span>

                                            <!-- 4 Vertical Reel Cells (Digits 1 to 4) -->
                                            <template x-for="(reel, digitIdx) in card.reels" :key="'reel-' + cardIdx + '-' + digitIdx">
                                                <div class="slot-reel-cell flex justify-center items-start"
                                                     :class="{
                                                         'h-16 w-11 sm:h-20 sm:w-14 md:h-24 md:w-16': drawCount === 1,
                                                         'h-12 w-8 sm:h-16 sm:w-11 md:h-18 md:w-13': drawCount === 2 || drawCount === 4,
                                                         'h-10 w-7 sm:h-12 sm:w-8 md:h-14 md:w-9': drawCount === 3 || drawCount === 5
                                                     }">
                                                    <!-- Vertical Reel Strip 0 to 9 -->
                                                    <div class="flex flex-col items-center font-black font-mono-num w-full"
                                                         :class="{
                                                             'text-3xl sm:text-4xl md:text-5xl': drawCount === 1,
                                                             'text-2xl sm:text-3xl md:text-4xl': drawCount === 2 || drawCount === 4,
                                                             'text-lg sm:text-xl md:text-2xl': drawCount === 3 || drawCount === 5,
                                                             'slot-strip-spinning text-amber-400/90': reel.isSpinning,
                                                             'slot-strip-locking text-yellow-300 drop-shadow-[0_0_15px_rgba(251,191,36,0.8)]': !reel.isSpinning
                                                         }"
                                                         :style="!reel.isSpinning ? 'transform: translateY(-' + (reel.targetDigit * 10) + '%);' : ''">
                                                        <div class="flex items-center justify-center flex-shrink-0 w-full" :class="{'h-16 sm:h-20 md:h-24': drawCount === 1, 'h-12 sm:h-16 md:h-18': drawCount === 2 || drawCount === 4, 'h-10 sm:h-12 md:h-14': drawCount === 3 || drawCount === 5}">0</div>
                                                        <div class="flex items-center justify-center flex-shrink-0 w-full" :class="{'h-16 sm:h-20 md:h-24': drawCount === 1, 'h-12 sm:h-16 md:h-18': drawCount === 2 || drawCount === 4, 'h-10 sm:h-12 md:h-14': drawCount === 3 || drawCount === 5}">1</div>
                                                        <div class="flex items-center justify-center flex-shrink-0 w-full" :class="{'h-16 sm:h-20 md:h-24': drawCount === 1, 'h-12 sm:h-16 md:h-18': drawCount === 2 || drawCount === 4, 'h-10 sm:h-12 md:h-14': drawCount === 3 || drawCount === 5}">2</div>
                                                        <div class="flex items-center justify-center flex-shrink-0 w-full" :class="{'h-16 sm:h-20 md:h-24': drawCount === 1, 'h-12 sm:h-16 md:h-18': drawCount === 2 || drawCount === 4, 'h-10 sm:h-12 md:h-14': drawCount === 3 || drawCount === 5}">3</div>
                                                        <div class="flex items-center justify-center flex-shrink-0 w-full" :class="{'h-16 sm:h-20 md:h-24': drawCount === 1, 'h-12 sm:h-16 md:h-18': drawCount === 2 || drawCount === 4, 'h-10 sm:h-12 md:h-14': drawCount === 3 || drawCount === 5}">4</div>
                                                        <div class="flex items-center justify-center flex-shrink-0 w-full" :class="{'h-16 sm:h-20 md:h-24': drawCount === 1, 'h-12 sm:h-16 md:h-18': drawCount === 2 || drawCount === 4, 'h-10 sm:h-12 md:h-14': drawCount === 3 || drawCount === 5}">5</div>
                                                        <div class="flex items-center justify-center flex-shrink-0 w-full" :class="{'h-16 sm:h-20 md:h-24': drawCount === 1, 'h-12 sm:h-16 md:h-18': drawCount === 2 || drawCount === 4, 'h-10 sm:h-12 md:h-14': drawCount === 3 || drawCount === 5}">6</div>
                                                        <div class="flex items-center justify-center flex-shrink-0 w-full" :class="{'h-16 sm:h-20 md:h-24': drawCount === 1, 'h-12 sm:h-16 md:h-18': drawCount === 2 || drawCount === 4, 'h-10 sm:h-12 md:h-14': drawCount === 3 || drawCount === 5}">7</div>
                                                        <div class="flex items-center justify-center flex-shrink-0 w-full" :class="{'h-16 sm:h-20 md:h-24': drawCount === 1, 'h-12 sm:h-16 md:h-18': drawCount === 2 || drawCount === 4, 'h-10 sm:h-12 md:h-14': drawCount === 3 || drawCount === 5}">8</div>
                                                        <div class="flex items-center justify-center flex-shrink-0 w-full" :class="{'h-16 sm:h-20 md:h-24': drawCount === 1, 'h-12 sm:h-16 md:h-18': drawCount === 2 || drawCount === 4, 'h-10 sm:h-12 md:h-14': drawCount === 3 || drawCount === 5}">9</div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Prize Badge -->
                                        <div class="inline-flex items-center space-x-1 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 text-[10px] sm:text-xs font-bold truncate max-w-full">
                                            <span>🎁 <strong x-text="card.prizeName || prizeName || '-'"></strong></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- IDLE STATE DISPLAY -->
                    <template x-if="status === 'idle'">
                        <div class="py-8 my-2 space-y-4">
                            <div class="text-6xl md:text-8xl font-black font-mono-num text-slate-700 tracking-wider">
                                JLS-????
                            </div>
                            <div class="text-xs md:text-sm text-slate-500">
                                Kupon yang sudah pernah keluar tidak akan muncul kembali secara sistem.
                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- RIGHT COLUMN (1/4 Width): MC MONITOR TABLE (PEMENANG TERKINI) -->
        <!-- ================================================================= -->
        <div class="lg:col-span-1 flex flex-col h-full bg-slate-900/90 border border-slate-800 rounded-3xl p-4 backdrop-blur-md shadow-2xl space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">📋</span>
                    <div>
                        <h3 class="text-sm font-black text-amber-400 uppercase tracking-wider">Tabel Pemenang (MC)</h3>
                        <p class="text-[10px] text-slate-400">Pemenang sah terbaru</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold">LIVE</span>
            </div>

            <!-- Table Body (VALID & ANNULLED WINNERS FOR MC) -->
            <div class="flex-1 overflow-y-auto max-h-[calc(100vh-250px)] pr-1 space-y-2">
                <template x-if="recentWinners && recentWinners.length > 0">
                    <div class="space-y-2">
                        <template x-for="(w, idx) in recentWinners" :key="'mc-' + w.id + '-' + idx">
                            <div class="p-3 rounded-2xl transition-all flex items-center justify-between border"
                                 :class="{
                                     'bg-slate-950/80 border-slate-800/80 hover:border-amber-500/40': w.status === 'valid',
                                     'bg-rose-950/40 border-rose-900/60 shadow-inner': w.status === 'annulled'
                                 }">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base font-black font-mono-num"
                                              :class="w.status === 'annulled' ? 'text-rose-400 line-through opacity-85' : 'text-amber-400'"
                                              x-text="w.coupon_number"></span>
                                        <template x-if="w.status === 'annulled'">
                                            <span class="text-[9px] font-black text-rose-200 bg-rose-900/90 px-1.5 py-0.5 rounded-full border border-rose-700 uppercase tracking-wider">🚫 ANULIR</span>
                                        </template>
                                    </div>
                                    <div class="text-[11px] font-semibold truncate max-w-[140px]"
                                         :class="w.status === 'annulled' ? 'text-rose-300/70' : 'text-indigo-300'"
                                         x-text="w.prize_name"></div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-mono"
                                          :class="w.status === 'annulled' ? 'text-rose-400/60' : 'text-slate-400'"
                                          x-text="w.time"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="!recentWinners || recentWinners.length === 0">
                    <div class="text-center py-12 text-slate-500 text-xs italic">
                        Belum ada pemenang yang diundi.
                    </div>
                </template>
            </div>
        </div>

    </div>

    <!-- 3. Bottom Section: Live Winners Marquee Ticker -->
    <div class="w-full bg-slate-950/90 border border-slate-800/80 rounded-2xl p-2.5 backdrop-blur-md overflow-hidden">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 flex items-center space-x-1.5 px-3 py-1 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-black uppercase tracking-wider">
                <span>📜</span>
                <span>Pemenang Terkini</span>
            </div>

            <!-- Marquee Running Text -->
            <div class="flex-1 overflow-hidden relative">
                <template x-if="recentWinners && recentWinners.length > 0">
                    <div class="animate-marquee whitespace-nowrap flex items-center space-x-8 text-xs font-semibold text-slate-300">
                        <template x-for="w in recentWinners" :key="'marq1-' + w.id">
                            <div class="inline-flex items-center space-x-2 bg-slate-900/60 px-3.5 py-1 rounded-lg border border-slate-800">
                                <span class="text-amber-400 font-mono font-black text-sm" x-text="w.coupon_number"></span>
                                <span class="text-slate-500">&bull;</span>
                                <span class="text-indigo-300" x-text="w.prize_name"></span>
                            </div>
                        </template>
                        <!-- Duplicate list for seamless loop -->
                        <template x-for="w in recentWinners" :key="'marq2-' + w.id">
                            <div class="inline-flex items-center space-x-2 bg-slate-900/60 px-3.5 py-1 rounded-lg border border-slate-800">
                                <span class="text-amber-400 font-mono font-black text-sm" x-text="w.coupon_number"></span>
                                <span class="text-slate-500">&bull;</span>
                                <span class="text-indigo-300" x-text="w.prize_name"></span>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="!recentWinners || recentWinners.length === 0">
                    <span class="text-xs text-slate-500 italic">Belum ada riwayat pemenang yang tersimpan.</span>
                </template>
            </div>
        </div>
    </div>

</div>

<script>
function raffleDisplay() {
    return {
        status: 'idle', // 'idle' | 'rolling' | 'locking' | 'winner'
        drawCount: 1,
        prizeName: '',
        remainingQuota: 0,
        totalQuota: 0,
        cardsData: [],
        winners: [],
        recentWinners: [],
        pendingRecentWinners: null,
        lastCelebratedWinnerId: null,
        isPolling: false,

        init() {
            // Setup initial cards structure
            this.updateCardsStructure(1);

            // 1. Initial State Fetch
            this.fetchState();

            // 2. High-speed lightweight JSON polling (800ms)
            setInterval(() => {
                this.fetchState();
            }, 800);

            // 3. Instant BroadcastChannel listener (0ms, same browser)
            if (typeof BroadcastChannel !== 'undefined') {
                const bc = new BroadcastChannel('raffle_channel');
                bc.onmessage = (event) => {
                    this.handleInstantEvent(event.data);
                };
            }

            // 4. Instant LocalStorage listener (0ms, across all browser windows)
            window.addEventListener('storage', (event) => {
                if (event.key === 'raffle_sync_event' && event.newValue) {
                    try {
                        const data = JSON.parse(event.newValue);
                        this.handleInstantEvent(data);
                    } catch(e) {}
                }
            });
        },

        updateCardsStructure(count, winnerList = []) {
            this.drawCount = count;
            const newCards = [];

            for (let c = 0; c < count; c++) {
                const targetCoupon = winnerList[c] ? (winnerList[c].coupon_number || 'JLS-0000') : 'JLS-0000';
                const digitsStr = targetCoupon.split('-')[1] || '0000';

                const reels = [];
                for (let d = 0; d < 4; d++) {
                    const digitValue = parseInt(digitsStr[d] || '0', 10);
                    reels.push({
                        isSpinning: this.status === 'rolling',
                        targetDigit: digitValue
                    });
                }

                newCards.push({
                    couponNumber: targetCoupon,
                    prizeName: winnerList[c] ? winnerList[c].prize_name : this.prizeName,
                    reels: reels
                });
            }

            this.cardsData = newCards;
        },

        handleInstantEvent(data) {
            if (!data || !data.action) return;

            if (data.action === 'count_change') {
                this.drawCount = data.draw_count || 1;
                if (this.status === 'idle') {
                    this.updateCardsStructure(this.drawCount);
                }
            } else if (data.action === 'rolling') {
                const count = data.draw_count || 1;
                this.startJackpotSpin(count);
            } else if (data.action === 'winner') {
                const count = data.draw_count || (data.winners ? data.winners.length : 1);
                const winnerList = data.winners || [{
                    id: data.winner_id,
                    coupon_number: data.coupon_number,
                    prize_name: data.prize_name
                }];
                this.stopJackpotSpin(count, winnerList);
            } else if (data.action === 'idle') {
                this.status = 'idle';
                this.resetSlots();
            }
        },

        async fetchState() {
            if (this.isPolling) return;
            this.isPolling = true;

            try {
                const res = await fetch('/api/raffle/state', {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                });

                if (!res.ok) return;
                const json = await res.json();
                const state = json.state || {};

                // Update Prize info
                if (state.prize) {
                    this.prizeName = state.prize.name || '';
                    this.remainingQuota = state.prize.remaining_quota ?? 0;
                    this.totalQuota = state.prize.quota ?? 0;
                } else {
                    this.prizeName = '';
                    this.remainingQuota = 0;
                    this.totalQuota = 0;
                }

                // Update Recent Winners for MC Table (Buffer during locking animation)
                if (json.recent_winners) {
                    if (this.status === 'locking') {
                        this.pendingRecentWinners = json.recent_winners;
                    } else {
                        this.recentWinners = json.recent_winners;
                        this.pendingRecentWinners = null;
                    }
                }

                const serverStatus = state.status || 'idle';
                const count = state.draw_count || 1;
                if (state.digit_delay_ms) {
                    this.digitDelayMs = state.digit_delay_ms;
                }

                if (serverStatus === 'rolling') {
                    if (this.status !== 'rolling' && this.status !== 'locking') {
                        this.startJackpotSpin(count);
                    }
                } else if (serverStatus === 'winner' && state.winners && state.winners.length > 0) {
                    if (this.status !== 'winner' && this.status !== 'locking') {
                        this.stopJackpotSpin(count, state.winners);
                    }
                } else if (serverStatus === 'idle') {
                    if (this.status !== 'idle') {
                        this.status = 'idle';
                        this.resetSlots();
                    }
                }
            } catch (e) {
                // Silently swallow fetch errors
            } finally {
                this.isPolling = false;
            }
        },

        startJackpotSpin(count = 1) {
            this.status = 'rolling';
            this.drawCount = count;
            this.updateCardsStructure(count);

            // Set all reels spinning at 60 FPS
            this.cardsData.forEach(card => {
                card.reels.forEach(reel => {
                    reel.isSpinning = true;
                });
            });
        },

        /**
         * 60 FPS Casino Jackpot Sequential Reel Deceleration & Elastic Bounce
         */
        stopJackpotSpin(count, winnerList) {
            if (!winnerList || winnerList.length === 0) {
                this.resetSlots();
                return;
            }

            this.status = 'locking';
            this.winners = winnerList;
            this.updateCardsStructure(count, winnerList);

            // Start reels spinning initially
            this.cardsData.forEach(card => {
                card.reels.forEach(reel => {
                    reel.isSpinning = true;
                });
            });

            // Dynamic Sequential Reel Lock based on digitDelayMs
            const step = this.digitDelayMs || 350;
            const digitDelays = [step, step * 2, step * 3, step * 4];

            digitDelays.forEach((delay, digitIdx) => {
                setTimeout(() => {
                    // Lock digitIdx across all cards simultaneously
                    this.cardsData.forEach(card => {
                        if (card.reels[digitIdx]) {
                            card.reels[digitIdx].isSpinning = false;
                        }
                    });

                    // Snappy mechanical click sound
                    if (window.playTickSound) window.playTickSound();

                    // Final Reel 4 Lock
                    if (digitIdx === 3) {
                        setTimeout(() => {
                            this.status = 'winner';
                            if (this.pendingRecentWinners) {
                                this.recentWinners = this.pendingRecentWinners;
                                this.pendingRecentWinners = null;
                            }

                            // Trigger confetti & fanfare celebration
                            const firstWinnerId = winnerList[0] ? winnerList[0].id : null;
                            if (firstWinnerId && firstWinnerId !== this.lastCelebratedWinnerId) {
                                this.lastCelebratedWinnerId = firstWinnerId;
                                if (window.launchConfetti) window.launchConfetti();
                                if (window.playFanfareSound) window.playFanfareSound();
                            }
                        }, 150);
                    }
                }, delay);
            });
        },

        resetSlots() {
            this.status = 'idle';
            this.winners = [];
            this.updateCardsStructure(1);
        }
    };
}
</script>
