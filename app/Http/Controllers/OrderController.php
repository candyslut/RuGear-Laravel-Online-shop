<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'address' => 'required|string|max:255',
            'payment_method' => 'required|string|in:card,cash',
        ]);

        // Get cart items
        $cartItems = $this->cartService->getUserCart($user);

        if (is_countable($cartItems) && count($cartItems) === 0) {
            return redirect()->back()->with('error', 'Ваша корзина пуста');
        }

        DB::beginTransaction();
        try {
            // Calculate total price using Collection method
            $totalPrice = collect($cartItems)->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_price' => $totalPrice,
                'notes' => null,
                'address' => $request->address,
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            // Clear user's cart
            $user->cartItems()->delete();

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Заказ успешно оформлен!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ошибка при оформлении заказа');
        }
    }
}
