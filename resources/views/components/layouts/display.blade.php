<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100 overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Layar Pengundian - Undian Jalan Sehat' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800;900&family=JetBrains+Mono:wght@700;800;900&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Outfit', 'Plus Jakarta Sans', system-ui, sans-serif;
            user-select: none;
        }
        .font-mono-num {
            font-family: 'JetBrains Mono', monospace;
        }
        [x-cloak] { display: none !important; }

        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            width: max-content;
            animation: marquee 30s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }

        /* Pulse Glow Animation */
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.08); }
        }
        .animate-pulse-glow {
            animation: pulseGlow 4s ease-in-out infinite;
        }

        /* 3D Casino Slot Machine Reel Styling (Fluid Responsive) */
        .slot-reel-cell {
            position: relative;
            overflow: hidden;
            background: linear-gradient(to bottom, #020617, #0f172a, #020617);
            border-radius: 0.75rem;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.9), 0 0 8px rgba(245,158,11,0.25);
            border: 1.5px solid rgba(245,158,11,0.4);
            flex-shrink: 0;
        }

        /* 3D Cylindrical Shadow Overlays */
        .slot-reel-cell::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 30%;
            background: linear-gradient(to bottom, rgba(2,6,23,0.95), transparent);
            z-index: 10;
            pointer-events: none;
        }
        .slot-reel-cell::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30%;
            background: linear-gradient(to top, rgba(2,6,23,0.95), transparent);
            z-index: 10;
            pointer-events: none;
        }

        /* Continuous 60 FPS Slot Machine Spin */
        @keyframes slotSpinFast {
            0% { transform: translateY(0%); }
            100% { transform: translateY(-50%); }
        }
        .slot-strip-spinning {
            animation: slotSpinFast 0.3s linear infinite;
            will-change: transform;
        }

        /* Smooth Eased Deceleration & Elastic Bounce */
        .slot-strip-locking {
            transition: transform 0.65s cubic-bezier(0.15, 0.85, 0.35, 1.2);
            will-change: transform;
        }
    </style>
</head>
<body class="h-full w-full flex flex-col antialiased bg-slate-950 text-slate-100 selection:bg-amber-500 selection:text-slate-950 relative overflow-hidden"
      x-data="{
          isFullscreen: false,
          isMuted: false,
          toggleFullscreen() {
              if (!document.fullscreenElement) {
                  document.documentElement.requestFullscreen().catch(err => {});
                  this.isFullscreen = true;
              } else {
                  if (document.exitFullscreen) {
                      document.exitFullscreen();
                      this.isFullscreen = false;
                  }
              }
          },
          toggleAudio() {
              if (window.toggleAudioMute) {
                  this.isMuted = window.toggleAudioMute();
              }
          },
          init() {
              document.addEventListener('fullscreenchange', () => {
                  this.isFullscreen = !!document.fullscreenElement;
              });
              // Unlock Web Audio on first user interaction
              window.addEventListener('click', () => {
                  if (window.unlockAudioContext) window.unlockAudioContext();
              }, { once: true });
          }
      }">

    <!-- Stage Background Lighting FX -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <!-- Top Golden Spotlight -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[500px] bg-gradient-to-b from-amber-500/20 via-yellow-500/10 to-transparent rounded-full blur-3xl opacity-60 animate-pulse-glow"></div>
        <!-- Bottom Indigo Ambient Glow -->
        <div class="absolute -bottom-40 left-1/2 -translate-x-1/2 w-[1100px] h-[500px] bg-gradient-to-t from-indigo-600/20 via-violet-600/10 to-transparent rounded-full blur-3xl opacity-50"></div>
        <!-- Subtle Grid Pattern -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b15_1px,transparent_1px),linear-gradient(to_bottom,#1e293b15_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>
    </div>

    <!-- Top Screen Header (Title + Quick Controls) -->
    <header class="relative z-20 w-full px-6 py-4 flex items-center justify-between border-b border-slate-800/40 bg-slate-950/40 backdrop-blur-sm">
        <!-- Title & Event Badge -->
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-300 flex items-center justify-center text-slate-950 font-black text-xl shadow-lg shadow-amber-500/30">
                🎉
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-xl md:text-2xl font-black uppercase tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-400">
                        UNDIAN JALAN SEHAT
                    </h1>
                    <span class="hidden sm:inline-block px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-bold uppercase tracking-widest">
                        Live Stage
                    </span>
                </div>
            </div>
        </div>

        <!-- Controls: Audio Mute & Fullscreen Button -->
        <div class="flex items-center space-x-2">
            <!-- Audio Toggle Button -->
            <button @click="toggleAudio()"
                    class="p-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white transition-all text-xs flex items-center space-x-1.5"
                    :title="isMuted ? 'Nyalakan Efek Suara' : 'Bisukan Efek Suara'">
                <span x-text="isMuted ? '🔇' : '🔊'"></span>
                <span class="hidden md:inline text-[11px] font-semibold" x-text="isMuted ? 'Audio Off' : 'Audio On'"></span>
            </button>

            <!-- Fullscreen Button -->
            <button @click="toggleFullscreen()"
                    class="px-3.5 py-2 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white transition-all text-xs font-bold flex items-center space-x-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
                <span class="hidden md:inline" x-text="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen (F11)'"></span>
            </button>
        </div>
    </header>

    <!-- Main Dynamic Stage -->
    <main class="relative z-10 flex-1 flex flex-col justify-center items-center px-4 md:px-8 py-4 overflow-hidden">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
