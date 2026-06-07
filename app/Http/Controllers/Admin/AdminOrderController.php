<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort   = $request->input('sort', 'newest');
        $status = $request->input('status', 'all');

        $query = Order::with('user', 'items');

        if ($search) {
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('id', $search);
                }
                $q->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $query->when($sort === 'oldest',    fn($q) => $q->oldest())
              ->when($sort === 'amount_desc', fn($q) => $q->orderByDesc('total_price'))
              ->when($sort === 'amount_asc',  fn($q) => $q->orderBy('total_price'))
              ->when(!in_array($sort, ['oldest','amount_desc','amount_asc']), fn($q) => $q->latest());

        $orders = $query->paginate(20)->withQueryString();

        $counts = [
            'all'        => Order::count(),
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed'  => Order::where('status', 'completed')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'search', 'sort', 'status', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Order $order, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $statusChanged = $order->status !== $validated['status'];

        $order->update(['status' => $validated['status']]);

        if ($statusChanged) {
            app(NotificationService::class)->orderStatus($order->fresh());
        }

        return redirect()->back()->with('success', 'Статус заказа обновлен');
    }
}

