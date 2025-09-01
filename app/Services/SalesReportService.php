<?php

namespace App\Services;

use App\Models\Order;

class SalesReportService
{
    public function dashboardReport(string $period = 'month'): array
    {
        $orders = Order::completed()
            ->forPeriod($period)
            ->with('items.product')
            ->get();

        return [
            'summary' => $orders->businessSummary(),
            'avg_orders_per_customer' => $orders->averageOrdersByCustomer(),
            'top_customers' => $orders->topCustomers(),
            'daily_breakdown' => $orders->dailyBreakdown(),
        ];
    }
}
