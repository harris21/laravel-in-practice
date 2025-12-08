<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OptimizedSalesReportService extends SalesReportService
{
    protected function getTopCustomersOptimized(string $period, int $limit = 3)
    {
        return DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $this->getPeriodStart($period))
            ->groupBy('orders.user_id', 'users.name')
            ->selectRaw('
                users.name,
                SUM(orders.total) as total_spent,
                COUNT(*) as order_count
            ')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get()
            ->map(fn($customer) => [
                'name' => $customer->name,
                'total_spent' => (float) $customer->total_spent,
                'order_count' => (int) $customer->order_count
            ]);
    }

    protected function getDatabaseSummary(string $period): array
    {
        $result = DB::table('orders')
            ->where('status', 'completed')
            ->where('created_at', '>=', $this->getPeriodStart($period))
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                AVG(total) as average_order_value,
                COUNT(DISTINCT user_id) as unique_customers
            ')
            ->first();

        return [
            'total_orders' => (int) $result->total_orders,
            'total_revenue' => (float) $result->total_revenue,
            'average_order_value' => (float) $result->average_order_value,
            'avg_orders_per_customer' => $result->total_orders / max($result->unique_customers, 1)
        ];
    }

    public function dashboardReport(string $period = 'month'): array
    {
        return [
            'summary' => $this->getDatabaseSummary($period),
            'top_customers' => $this->getTopCustomersOptimized($period),
            'daily_breakdown' => $this->getDailyBreakdownOptimized($period),
            'avg_orders_per_customer' => $this->getAvgOrdersPerCustomer($period)
        ];
    }

    protected function getDailyBreakdownOptimized(string $period)
    {
        return DB::table('orders')
            ->where('status', 'completed')
            ->where('created_at', '>=', $this->getPeriodStart($period))
            ->selectRaw("
                DATE(created_at) as date,
                SUM(total) as revenue,
                COUNT(*) as orders
            ")
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(fn($day) => [
                'date' => \Illuminate\Support\Facades\Date::parse($day->date)->format('M j'),
                'revenue' => (float) $day->revenue,
                'orders' => (int) $day->orders
            ]);
    }

    protected function getAvgOrdersPerCustomer(string $period): float
    {
        $result = DB::table('orders')
            ->where('status', 'completed')
            ->where('created_at', '>=', $this->getPeriodStart($period))
            ->selectRaw('
                COUNT(*) as total_orders,
                COUNT(DISTINCT user_id) as unique_customers
            ')
            ->first();

        return $result->unique_customers > 0
            ? round($result->total_orders / $result->unique_customers, 2)
            : 0;
    }

    private function getPeriodStart(string $period)
    {
        return match($period) {
            'today' => today(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfMonth()
        };
    }
}
