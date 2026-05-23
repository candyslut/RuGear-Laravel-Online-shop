<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'required|string|max:255',
            'delivery_type'  => 'required|string|in:courier,pickup,post',
            'payment_method' => 'required|string|in:card,cash',
        ]);

        $cartItems = $this->cartService->getUserCart($user);

        if (is_countable($cartItems) && count($cartItems) === 0) {
            return redirect()->back()->with('error', 'Ваша корзина пуста');
        }

        DB::beginTransaction();
        try {
            $totalPrice = collect($cartItems)->sum(fn($item) => $item->product->price * $item->quantity);

            $order = Order::create([
                'user_id'        => $user->id,
                'status'         => 'pending',
                'total_price'    => $totalPrice,
                'address'        => $request->address,
                'payment_method' => $request->payment_method,
                'full_name'      => $request->full_name,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'delivery_type'  => $request->delivery_type,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->price,
                ]);

                $product = Product::find($item->product_id);
                if ($product) {
                    $product->decrement('quantity', $item->quantity);
                }
            }

            $user->cartItems()->delete();

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Заказ ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' успешно оформлен!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ошибка при оформлении заказа');
        }
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('dashboard')->with('error', 'Заказ нельзя отменить в текущем статусе');
        }

        DB::beginTransaction();
        try {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('quantity', $item->quantity);
                }
            }

            $order->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Заказ ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' отменён. Товары возвращены в наличие.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard')->with('error', 'Ошибка при отмене заказа');
        }
    }
}
