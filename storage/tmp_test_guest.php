<?php

// Свежий процесс: авторизованная страница с Livewire-компонентами —
// livewire.js должен быть ровно один (без задвоения от авто-инжекта).
Illuminate\Support\Facades\Auth::login(App\Models\User::first());
$html = app(App\Http\Controllers\ShopController::class)->index()->render();
echo 'market livewire <script>: ' . preg_match_all('/<script[^>]+livewire/i', $html) . PHP_EOL;
echo 'market burger x-data:     ' . (str_contains($html, 'x-data') ? 'OK' : 'MISSING') . PHP_EOL;
