<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
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
            'payment_method' => 'required|string|in:card,qr',
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

            return redirect()->route('orders.payment', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ошибка при оформлении заказа');
        }
    }

    public function payment(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.payment', compact('order'));
    }

    public function paymentConfirm(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $user     = auth()->user();
        $orderNum = 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
        $redirect = redirect()->route('dashboard')
            ->with('success', 'Оплата по заказу ' . $orderNum . ' прошла успешно! Ожидайте доставку.');

        // ── Накопительная сумма всех не отменённых заказов ───────────
        $totalSpent = Order::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        $candidates = [];

        // Первый заказ
        if (Order::where('user_id', $user->id)->where('status', '!=', 'cancelled')->count() === 1) {
            $candidates[] = Achievement::updateOrCreate(
                ['slug' => 'first_order'],
                [
                    'title'       => 'Первый заказ',
                    'description' => 'Вы оформили свой первый заказ на RuGear. Добро пожаловать в семью покупателей!',
                    'experience'  => 100,
                    'coins'       => 25,
                ]
            );
        }

        // Накопительная сумма >= 10 000 ₽
        if ($totalSpent >= 10000) {
            $candidates[] = Achievement::updateOrCreate(
                ['slug' => 'order_10k'],
                [
                    'title'       => 'Крупная покупка',
                    'description' => 'Вы потратили в RuGear суммарно от 10 000 ₽. Серьёзный подход!',
                    'experience'  => 75,
                    'coins'       => 40,
                ]
            );
        }

        // Накопительная сумма >= 50 000 ₽
        if ($totalSpent >= 50000) {
            $candidates[] = Achievement::updateOrCreate(
                ['slug' => 'order_50k'],
                [
                    'title'       => 'Кит',
                    'description' => 'Суммарные траты в RuGear от 50 000 ₽. Вы настоящий энтузиаст!',
                    'experience'  => 200,
                    'coins'       => 150,
                ]
            );
        }

        // Заказал хотя бы раз каждую из 4 категорий
        $orderedCategoryIds = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.user_id', $user->id)
            ->where('orders.status', '!=', 'cancelled')
            ->distinct()
            ->pluck('products.category_id')
            ->toArray();

        if (count(array_intersect([1, 2, 3, 4], $orderedCategoryIds)) === 4) {
            $candidates[] = Achievement::updateOrCreate(
                ['slug' => 'all_categories'],
                [
                    'title'       => 'Полный арсенал',
                    'description' => 'Вы заказали товары из всех категорий: клавиатуру, мышь, наушники и коврик.',
                    'experience'  => 150,
                    'coins'       => 75,
                ]
            );
        }

        // ── Выдаём все ачивки, собираем все для тоста ──────────
        $awardedAchievements = [];
        $toastLevelUp        = null;

        foreach ($candidates as $ach) {
            $result = $user->awardAchievement($ach);
            if ($result) {
                $awardedAchievements[] = [
                    'title'      => $ach->title,
                    'experience' => $ach->experience,
                    'coins'      => $ach->coins,
                ];
                if ($result['leveled_up']) {
                    $toastLevelUp = [
                        'level' => $result['new_level'],
                        'coins' => $result['level_coins'],
                    ];
                }
            }
        }

        if (!empty($awardedAchievements)) {
            $redirect = $redirect->with('achievements_awarded', $awardedAchievements);
        }
        if ($toastLevelUp) {
            $redirect = $redirect->with('level_up', $toastLevelUp);
        }

        return $redirect;
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
