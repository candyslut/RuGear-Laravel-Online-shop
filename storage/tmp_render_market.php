<?php

$u = App\Models\User::first();
Illuminate\Support\Facades\Auth::login($u);

$html = app(App\Http\Controllers\ShopController::class)->index()->render();

echo 'len: ' . strlen($html) . PHP_EOL;
foreach ([
    'Темы сайта', 'Няшная', 'Тёмная', 'Светлая',
    'orb__dot--theme-dark', 'orb__dot--theme-light', 'orb__dot--theme-cosmic', 'orb__dot--theme-pink',
    '.theme-art--dark', '.theme-art--light',
    'syncBaseTheme', "pink: 'Няшная'", "dark: 'Тёмная'",
] as $needle) {
    echo str_pad($needle, 32) . (str_contains($html, $needle) ? 'OK' : 'MISSING') . PHP_EOL;
}

// Бейдж темы: svg-иконка должна исчезнуть.
$badge = preg_match('/id="site-theme-badge".*?<\/a>/s', $html, $m) ? $m[0] : '';
echo str_pad('badge has svg', 32) . (str_contains($badge, '<svg') ? 'YES (bad)' : 'NO (good)') . PHP_EOL;

// Порядок тем в лотке (по data-cat="theme" кнопкам).
preg_match_all('/orb__dot--theme-([a-z]+)/', $html, $mm);
echo 'theme order: ' . implode(', ', array_values(array_unique($mm[1]))) . PHP_EOL;

// Бесплатные темы помечены как owned в catData.
$json = preg_match('/const market = (\{.*?\});\n/s', $html, $m2) ? $m2[1] : '';
$data = $json ? json_decode($json, true) : null;
if ($data && isset($data['theme'])) {
    foreach ($data['theme'] as $t) {
        echo str_pad('theme ' . $t['css'], 20)
            . ' price=' . $t['price']
            . ' owned=' . var_export($t['owned'], true)
            . ' equipped=' . var_export($t['equipped'], true) . PHP_EOL;
    }
} else {
    echo 'market json: PARSE FAIL' . PHP_EOL;
}
