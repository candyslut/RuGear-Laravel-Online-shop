<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminStatsController extends Controller
{
    public function index()
    {
        // ── Status distribution ──────────────────────────────────────────
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        $countFor    = fn (string $s) => (int) ($statusCounts[$s] ?? 0);
        $totalOrders = array_sum($statusCounts);
        $completed   = $countFor('completed');
        $cancelled   = $countFor('cancelled');

        $statusMeta = [
            'completed'  => ['label' => 'Завершён',    'color' => '#22c55e'],
            'processing' => ['label' => 'В обработке', 'color' => '#3b82f6'],
            'pending'    => ['label' => 'Ожидает',     'color' => '#f59e0b'],
            'cancelled'  => ['label' => 'Отменён',     'color' => '#ef4444'],
        ];
        $statusData = [];
        foreach ($statusMeta as $key => $meta) {
            $c = $countFor($key);
            $statusData[] = [
                'key'   => $key,
                'label' => $meta['label'],
                'color' => $meta['color'],
                'count' => $c,
                'pct'   => $totalOrders ? round($c / $totalOrders * 100, 1) : 0,
            ];
        }

        // ── Headline KPIs ────────────────────────────────────────────────
        $totalRevenue    = (float) Order::where('status', 'completed')->sum('total_price');
        $pipelineRevenue = (float) Order::whereIn('status', ['pending', 'processing'])->sum('total_price');
        $aov             = (float) Order::where('status', '!=', 'cancelled')->avg('total_price');
        $completionRate  = $totalOrders ? round($completed / $totalOrders * 100) : 0;
        $cancelRate      = $totalOrders ? round($cancelled / $totalOrders * 100) : 0;

        $itemsSold = (int) OrderItem::whereHas('order', fn ($q) => $q->where('status', 'completed'))
            ->sum('quantity');

        $activeCustomers = (int) Order::where('status', '!=', 'cancelled')
            ->distinct()
            ->count('user_id');

        // ── 14-day revenue / orders series ───────────────────────────────
        $days  = 14;
        $start = Carbon::today()->subDays($days - 1);

        $rows = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $start)
            ->select(
                DB::raw('DATE(created_at) as d'),
                DB::raw('COUNT(*) as c'),
                DB::raw('SUM(total_price) as s'),
            )
            ->groupBy('d')
            ->get()
            ->keyBy('d');

        $series = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $row = $rows->get($day->format('Y-m-d'));
            $series[] = [
                'label'   => $day->format('d.m'),
                'orders'  => $row ? (int) $row->c : 0,
                'revenue' => $row ? (float) $row->s : 0.0,
                'weekend' => $day->isWeekend(),
            ];
        }
        $maxRevenue    = max(array_map(fn ($x) => $x['revenue'], $series)) ?: 1;
        $periodRevenue = array_sum(array_map(fn ($x) => $x['revenue'], $series));
        $periodOrders  = array_sum(array_map(fn ($x) => $x['orders'], $series));
        $bestDay       = collect($series)->sortByDesc('revenue')->first();

        // ── Top products by units sold ───────────────────────────────────
        $topProducts = OrderItem::whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
            ->select('product_id', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(price * quantity) as revenue'))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(5)
            ->with('product')
            ->get();
        $topProductMax = (int) ($topProducts->max('qty') ?: 1);

        // ── Top customers by spend ───────────────────────────────────────
        $topCustomers = Order::where('status', '!=', 'cancelled')
            ->select('user_id', DB::raw('COUNT(*) as orders'), DB::raw('SUM(total_price) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('user')
            ->get();
        $topCustomerMax = (float) ($topCustomers->max('total') ?: 1);

        // ── Payment & delivery breakdown ─────────────────────────────────
        $paymentLabels  = ['card' => 'Карта', 'qr' => 'СБП / QR'];
        $deliveryLabels = ['courier' => 'Курьер', 'pickup' => 'Самовывоз', 'post' => 'Почта'];

        $payments = Order::where('status', '!=', 'cancelled')
            ->select('payment_method', DB::raw('COUNT(*) as c'))
            ->groupBy('payment_method')
            ->get()
            ->map(fn ($r) => [
                'label' => $paymentLabels[$r->payment_method] ?? ($r->payment_method ?: '—'),
                'count' => (int) $r->c,
            ]);

        $deliveries = Order::where('status', '!=', 'cancelled')
            ->select('delivery_type', DB::raw('COUNT(*) as c'))
            ->groupBy('delivery_type')
            ->get()
            ->map(fn ($r) => [
                'label' => $deliveryLabels[$r->delivery_type] ?? ($r->delivery_type ?: '—'),
                'count' => (int) $r->c,
            ]);

        $nonCancelled = max($totalOrders - $cancelled, 1);

        return view('admin.statistics', compact(
            'totalRevenue',
            'pipelineRevenue',
            'aov',
            'completionRate',
            'cancelRate',
            'itemsSold',
            'activeCustomers',
            'totalOrders',
            'completed',
            'statusData',
            'series',
            'maxRevenue',
            'periodRevenue',
            'periodOrders',
            'bestDay',
            'topProducts',
            'topProductMax',
            'topCustomers',
            'topCustomerMax',
            'payments',
            'deliveries',
            'nonCancelled',
        ));
    }

    /**
     * Выгрузка статистики в CSV (открывается в Excel).
     *
     * Период берётся из query-параметров from/to (формат Y-m-d). Если даты
     * не заданы — берём все данные. Файл отдаётся с BOM и разделителем «;»,
     * чтобы кириллица и колонки корректно открывались в Excel на RU-локали.
     */
    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->date('from')?->startOfDay();
        $to   = $request->date('to')?->endOfDay();

        // Применяет выбранный период к запросу по полю created_at.
        $period = function ($query) use ($from, $to) {
            if ($from) {
                $query->where('created_at', '>=', $from);
            }
            if ($to) {
                $query->where('created_at', '<=', $to);
            }

            return $query;
        };

        // ── Статусы ──────────────────────────────────────────────────────
        $statusCounts = $period(Order::query())
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        $countFor    = fn (string $s) => (int) ($statusCounts[$s] ?? 0);
        $totalOrders = array_sum($statusCounts);
        $completed   = $countFor('completed');
        $cancelled   = $countFor('cancelled');

        $statusMeta = [
            'completed'  => 'Завершён',
            'processing' => 'В обработке',
            'pending'    => 'Ожидает',
            'cancelled'  => 'Отменён',
        ];

        // ── KPI ──────────────────────────────────────────────────────────
        $totalRevenue    = (float) $period(Order::where('status', 'completed'))->sum('total_price');
        $pipelineRevenue = (float) $period(Order::whereIn('status', ['pending', 'processing']))->sum('total_price');
        $aov             = (float) $period(Order::where('status', '!=', 'cancelled'))->avg('total_price');
        $completionRate  = $totalOrders ? round($completed / $totalOrders * 100, 1) : 0;
        $cancelRate      = $totalOrders ? round($cancelled / $totalOrders * 100, 1) : 0;

        $itemsSold = (int) OrderItem::whereHas('order', fn ($q) => $period($q->where('status', 'completed')))
            ->sum('quantity');

        // ── Список заказов с датами ──────────────────────────────────────
        $orders = $period(Order::query())
            ->withSum('items as units', 'quantity')
            ->orderByDesc('created_at')
            ->get();

        // ── Сборка CSV ───────────────────────────────────────────────────
        $periodLabel = ($from || $to)
            ? (($from?->format('d.m.Y') ?? '…') . ' — ' . ($to?->format('d.m.Y') ?? '…'))
            : 'За всё время';

        $fileName = 'Статистика продаж ' . now()->format('d.m.Y') . '.csv';

        $callback = function () use (
            $periodLabel, $totalRevenue, $pipelineRevenue, $aov, $itemsSold,
            $completionRate, $cancelRate, $totalOrders, $statusMeta, $orders
        ) {
            $out = fopen('php://output', 'w');

            // BOM — чтобы Excel распознал UTF-8 и кириллицу.
            fwrite($out, "\xEF\xBB\xBF");

            // Деньги и проценты оформляем по-человечески: «1 250 000 ₽», «87 %».
            $money = fn ($v) => number_format(round((float) $v), 0, '.', ' ') . ' ₽';
            $pct   = fn ($v) => str_replace('.', ',', (string) $v) . ' %';

            $row   = fn (array $cells) => fputcsv($out, $cells, ';');
            $blank = fn () => fwrite($out, "\r\n");
            $title = fn (string $text) => fputcsv($out, [$text], ';');

            // Дату отдаём как текст-формулу: иначе Excel хранит её как число-дату
            // и при узкой колонке показывает «####». Так дата видна всегда.
            $dateText = fn ($dt) => '="' . $dt->format('d.m.Y H:i') . '"';

            // Шапка
            $title('Статистика продаж RuGear');
            $row(['Период:', $periodLabel]);
            $row(['Сформировано:', now()->format('d.m.Y в H:i')]);
            $blank();

            // Список заказов (с датами)
            $title('Список заказов');
            $row(['Дата', 'Покупатель', 'Статус', 'Товаров', 'Сумма']);
            foreach ($orders as $o) {
                $row([
                    $dateText($o->created_at),
                    $o->full_name ?: ('Пользователь #' . $o->user_id),
                    $statusMeta[$o->status] ?? $o->status,
                    ((int) $o->units) . ' шт',
                    $money($o->total_price),
                ]);
            }
            if ($orders->isEmpty()) {
                $row(['За выбранный период заказов нет', '', '', '', '']);
            }
            $blank();

            // Общая информация по заказам
            $title('Итого по заказам');
            $row(['Всего заказов', $totalOrders]);
            $row(['Выручка (завершённые заказы)', $money($totalRevenue)]);
            $row(['Выручка в работе', $money($pipelineRevenue)]);
            $row(['Средний чек', $money($aov)]);
            $row(['Продано товаров', $itemsSold . ' шт']);
            $row(['Завершаемость', $pct($completionRate)]);
            $row(['Отмены', $pct($cancelRate)]);

            fclose($out);
        };

        return response()->streamDownload($callback, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
