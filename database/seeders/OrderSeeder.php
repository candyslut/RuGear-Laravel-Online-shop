<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $users = User::all();

        if ($products->isEmpty() || $users->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentMethods = ['card', 'cash', 'sbp'];
        $deliveryTypes = ['courier', 'pickup', 'post'];

        $addresses = [
            'г. Москва, ул. Тверская, д. 12, кв. 45',
            'г. Санкт-Петербург, Невский пр., д. 78, кв. 3',
            'г. Новосибирск, ул. Ленина, д. 5, кв. 112',
            'г. Екатеринбург, ул. Малышева, д. 51, кв. 27',
            'г. Казань, ул. Баумана, д. 34, кв. 9',
        ];

        // Pick ~12 users to be customers, give each a few orders with varied statuses.
        $customers = $users->shuffle()->take(12);

        $statusIndex = 0;

        foreach ($customers as $user) {
            $orderCount = rand(1, 3);

            for ($i = 0; $i < $orderCount; $i++) {
                // Cycle through statuses so all four are represented across the dataset.
                $status = $statuses[$statusIndex % count($statuses)];
                $statusIndex++;

                $createdAt = now()->subDays(rand(0, 90))->subHours(rand(0, 23));

                $order = Order::create([
                    'user_id'        => $user->id,
                    'status'         => $status,
                    'total_price'    => 0,
                    'notes'          => rand(0, 1) ? 'Позвоните перед доставкой.' : null,
                    'address'        => $addresses[array_rand($addresses)],
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'full_name'      => $user->name,
                    'phone'          => '+7 (9' . rand(10, 99) . ') ' . rand(100, 999) . '-' . rand(10, 99) . '-' . rand(10, 99),
                    'email'          => $user->email,
                    'delivery_type'  => $deliveryTypes[array_rand($deliveryTypes)],
                    'created_at'     => $createdAt,
                    'updated_at'     => $createdAt,
                ]);

                $orderProducts = $products->shuffle()->take(rand(1, 4));
                $total = 0;

                foreach ($orderProducts as $product) {
                    $quantity = rand(1, 3);
                    $total += $product->price * $quantity;

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $product->id,
                        'quantity'   => $quantity,
                        'price'      => $product->price,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }

                $order->update(['total_price' => $total]);
            }
        }
    }
}
