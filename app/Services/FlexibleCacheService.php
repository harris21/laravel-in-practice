<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class FlexibleCacheService extends OptimizedSalesReportService
{
    public function dashboardReport(string $period = 'month'): array
    {
        $cacheKey = "dashboard:{$period}:" . now()->format('Y-m-d-H');

        return Cache::flexible($cacheKey, [300, 600], fn() => parent::dashboardReport($period));
    }

    public function getTopCustomersOptimized(string $period, int $limit = 3)
    {
        $cacheKey = "top_customers:{$period}:{$limit}";

        return Cache::flexible($cacheKey, [1800, 1800], fn() => parent::getTopCustomersOptimized($period, $limit));
    }

    public function getDailyBreakdownOptimized(string $period)
    {
        $cacheKey = "daily_breakdown:{$period}:" . now()->format('Y-m-d');

        return Cache::flexible($cacheKey, [3600, 3600], fn() => parent::getDailyBreakdownOptimized($period));
    }

    public function getDatabaseSummary(string $period): array
    {
        $cacheKey = "database:{$period}:" . now()->format('Y-m-d-H');

        return Cache::flexible($cacheKey, [300, 600], fn() => parent::getDatabaseSummary($period));
    }

    public function getAvgOrdersPerCustomer(string $period): float
    {
        $cacheKey = "avg_orders_per_customer:{$period}:" . now()->format('Y-m-d-H');

        return Cache::flexible($cacheKey, [300, 600], fn() => parent::getAvgOrdersPerCustomer($period));
    }
}
