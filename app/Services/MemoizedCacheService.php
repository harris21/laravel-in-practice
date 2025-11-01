<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class MemoizedCacheService
{
    public function __construct(private FlexibleCacheService $flexibleCacheService) {}

    public function dashboardReport(string $period = 'month'): array
    {
        $memoKey = "memo_dashboard_{$period}";

        return Cache::memo()->remember($memoKey, 3600, function () use ($period) {
            return $this->flexibleCacheService->dashboardReport($period);
        });
    }

}
