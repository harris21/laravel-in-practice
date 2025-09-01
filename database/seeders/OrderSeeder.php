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
        // Create some products first
        $products = Product::factory(10)->create();

        // Create some users
        $users = User::factory(5)->create();

        // Create orders with items for each user
        $users->each(function ($user) use ($products) {
            // Create 2-8 orders per user
            $orderCount = rand(2, 8);

            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::factory()->create([
                    'user_id' => $user->id,
                    'status' => 'completed',
                    'created_at' => now()->subDays(rand(0, 30)),
                    'total' => 0 // We'll calculate this
                ]);

                // Add 1-4 items to each order
                $itemCount = rand(1, 4);
                $orderTotal = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products->random();
                    $quantity = rand(1, 3);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price
                    ]);

                    $orderTotal += $product->price * $quantity;
                }

                // Update order total
                $order->update(['total' => $orderTotal]);
            }
        });
    }
}
