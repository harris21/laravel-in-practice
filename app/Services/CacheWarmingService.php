<?php

namespace App\Services;

class CacheWarmingService
{
    public function __construct(private readonly FlexibleCacheService $flexibleCacheService)
    {
    }

    public function warmDashboardCache(string $period = 'month')
    {
        $result = $this->flexibleCacheService->dashboardReport($period);

        return [
            'period' => $period,
            'warmed_at' => now()->toDateTimeString(),
            'data_points' => count($result),
        ];
    }

    public function warmAllDashboardCaches()
    {
        $results = [];

        $periods = ['today', 'week', 'month'];

        foreach ($periods as $period) {
            $results[$period] = $this->warmDashboardCache($period);
        }

        return $results;
    }
}
