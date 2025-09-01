<?php

use App\Models\User;
use App\Models\Order;
use App\Services\SalesReportService;

beforeEach(function () {
   $this->service = new SalesReportService();
});

it('generates the complete dashboard report', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
    ]);

    Order::factory()
        ->completed()
        ->count(2)
        ->create([
            'user_id' => $user->id,
            'total' =>  100,
            'created_at' => now()
        ]);

    $report = $this->service->dashboardReport('month');


    expect($report['summary'])
        ->total_revenue->toBe(200.0)
        ->average_order_value->toBe(100.0)
    ->and($report['top_customers'])
        ->toHaveCount(1)
        ->first()->name->toBe('John Doe');
});
