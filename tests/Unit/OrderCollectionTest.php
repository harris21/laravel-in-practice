<?php

use App\Models\User;
use App\Models\Order;
use App\Collections\OrderCollection;

test('it calculates total revenue', function () {
    $orders = new OrderCollection(
        [
            new Order(['total' => 100]),
            new Order(['total' => 200]),
            new Order(['total' => 300]),
        ]
    );

    expect($orders->totalRevenue())->toBe(600.0);
});

test('it generates business summary', function () {
   $orders = new OrderCollection(
       [
           new Order(['total' => 100]),
           new Order(['total' => 200]),
       ]
   );

   $summary = $orders->businessSummary();

   expect($summary)->toMatchArray([
       'total_revenue' => 300.0,
       'average_order_value' => 150.0,
       'total_orders' => 2
   ]);
});

it('it calculates the average orders by customer', function () {
    $orders = new OrderCollection([
        new Order(['user_id' => 1]),
        new Order(['user_id' => 1]),
        new Order(['user_id' => 2]),
    ]);

    expect($orders->averageOrdersByCustomer())->toBe(1.5);
});
