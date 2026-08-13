<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Panel - Undian Jalan Sehat' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        .font-mono-num {
            font-family: 'JetBrains Mono', monospace;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full flex flex-col antialiased bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 min-h-screen text-slate-100 selection:bg-amber-500 selection:text-slate-950">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-300 flex items-center justify-center shadow-lg shadow-amber-500/20 text-slate-950 font-black text-xl">
                        🎟️
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-black tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-yellow-200 to-amber-500 text-lg uppercase">
                                Undian Jalan Sehat
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 font-semibold border border-indigo-500/30">
                                Admin Panel
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">Offline-First Local Raffle Engine</p>
                    </div>
                </div>

                <!-- Right Action Buttons & Status -->
                <div class="flex items-center space-x-3">
                    <!-- Local Server Indicator -->
                    <div class="hidden sm:flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-emerald-950/50 border border-emerald-800/40 text-emerald-400 text-xs font-medium">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>LAN Local Online</span>
                    </div>

                    <!-- Open Public Display Button -->
                    <a href="/display" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0 border border-indigo-400/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Buka Layar Display (Proyektor)</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950/40 py-4 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; {{ date('Y') }} Undian Jalan Sehat &bull; Offline-First Local System</span>
            <div class="flex items-center space-x-4 text-slate-400">
                <span>Database: <strong class="text-amber-400">SQLite Local</strong></span>
                <span>Framework: <strong class="text-indigo-400">Livewire 3 + Alpine.js</strong></span>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
