<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheInvalidationService
{
    public function __construct(private readonly CacheWarmingService $warmingService)
    {
    }

    public function clearDashboardCache(string $period = 'month')
    {

        Cache::forget("memo_dashboard_{$period}");

        $dashboardKey = "dashboard:{$period}:" . now()->format('Y-m-d-H');
        $this->invalidateFlexibleCache($dashboardKey);

        $databaseKey = "database:{$period}:" . now()->format('Y-m-d-H');
        $this->invalidateFlexibleCache($databaseKey);

        $topCustomersKey = "top_customers:{$period}:3";
        $this->invalidateFlexibleCache($topCustomersKey);

        $dailyKey = "daily_breakdown:{$period}:" . now()->format('Y-m-d');
        $this->invalidateFlexibleCache($dailyKey);

        $avgKey = "avg_orders_per_customer:{$period}:" . now()->format('Y-m-d-H');
        $this->invalidateFlexibleCache($avgKey);
    }

    public function clearAllDashboardCaches()
    {
        $this->clearDashboardCache('today');
        $this->clearDashboardCache('week');
        $this->clearDashboardCache('month');
    }

    protected function invalidateFlexibleCache(string $dashboardKey): void
    {
        Cache::forget($dashboardKey);
        Cache::forget("illuminate:cache:flexible:created:{$dashboardKey}");
        Cache::forget("illuminate:cache:flexible:lock:{$dashboardKey}");
    }

    public function refreshDashboardCache(string $period = 'month')
    {
        $this->clearDashboardCache($period);
        $this->warmingService->warmDashboardCache($period);
    }

    public function refreshAllDashboardCaches()
    {
        $this->clearAllDashboardCaches();
        $this->warmingService->warmAllDashboardCaches();
    }
}
