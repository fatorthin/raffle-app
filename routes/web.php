<?php

use App\Livewire\Admin\RaffleAdmin;
use App\Livewire\Display\RaffleDisplay;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin');
});

Route::get('/admin', RaffleAdmin::class)->name('admin');
Route::get('/display', RaffleDisplay::class)->name('display');

// Ultra-fast lightweight state endpoint for Display screen and LAN polling
Route::get('/api/raffle/state', function (App\Services\RaffleService $service) {
    return response()->json([
        'state' => $service->getCurrentState(),
        'recent_winners' => App\Models\Winner::with(['coupon', 'prize'])
            ->orderBy('id', 'desc')
            ->take(15)
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'coupon_number' => $w->coupon?->coupon_number ?? '-',
                'name' => $w->coupon?->name ?? 'Peserta',
                'prize_name' => $w->prize?->name ?? '-',
                'status' => $w->status,
                'time' => $w->created_at?->format('H:i:s') ?? '-',
            ]),
    ]);
});
