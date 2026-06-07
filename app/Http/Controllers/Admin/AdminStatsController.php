<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
}
