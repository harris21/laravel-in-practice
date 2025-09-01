<?php

use App\Models\User;
use App\Models\Order;

it('find top customers with relationships', function () {
    $john = User::factory()->create(['name' => 'John Doe']);
    $jane = User::factory()->create(['name' => 'Jane Smith']);

    Order::factory()->completed()->create([
        'user_id' => $john->id,
        'total' => 100
    ]);
    Order::factory()->completed()->create([
        'user_id' => $john->id,
        'total' => 200
    ]);
    Order::factory()->completed()->create([
        'user_id' => $jane->id,
        'total' => 150
    ]);

    $orders = Order::completed()->withUser()->get();

    $topCustomers = $orders->topCustomers(2);

    expect($topCustomers)->toHaveCount(2)
        ->first()->name->toBe('John Doe')
        ->first()->total_spent->toBe(300.0);
});

it('generates the daily breakdown from the database', function () {
   Order::factory()->completed()->create([
      'total' => 100,
      'created_at' => now(),
   ]);

   Order::factory()->completed()->create([
       'total' => 200,
       'created_at' => now()->subDay(),
   ]);

   $orders = Order::completed()
       ->get();
   $breakdown = $orders->dailyBreakdown();

   expect($breakdown)->toHaveCount(2)
       ->each->toHaveKeys([
           'date',
           'revenue',
           'orders'
       ]);
});

it('filter completed orders with completed scope', function () {
   Order::factory()->completed()->create();

   Order::factory()->create([
       'status' => 'pending'
   ]);

    Order::factory()->completed()->create();

    $completedOrders = Order::completed()->get();

    expect($completedOrders)->toHaveCount(2);
});

it('filters by timeframe for the forPeriod scope', function () {
   Order::factory()->completed()->create([
       'created_at' => now(),
   ]);

   Order::factory()->completed()->create([
       'created_at' => now()->subMonth(),
   ]);

   $thisMonthsOrder = Order::completed()
       ->forPeriod('month')
       ->get();

   expect($thisMonthsOrder)->toHaveCount(1);
});

it('chains scopes together', function () {
   Order::factory()->completed()
       ->thisMonth()
       ->create([
           'total' => 150,
       ]);

   Order::factory()->create([
       'status' => 'pending',
       'created_at' => now()
   ]);

   $orders = Order::completed()
       ->forPeriod('month')
       ->popular(110)
       ->get();

   expect($orders)->toHaveCount(1);
});
