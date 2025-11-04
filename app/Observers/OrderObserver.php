<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\CacheInvalidationService;

class OrderObserver
{
    private CacheInvalidationService $cacheService;

    public function __construct(CacheInvalidationService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function created(Order $order)
    {
        if ($order->created_at->isToday()) {
            $this->cacheService->clearDashboardCache('today');
        }

        if ($order->created_at->isCurrentWeek()) {
            $this->cacheService->clearDashboardCache('week');
        }

        if ($order->created_at->isCurrentMonth()) {
            $this->cacheService->clearDashboardCache('month');
        }
    }

    public function updated(Order $order)
    {
        if ($order->isDirty(['status', 'total'])) {
            $this->created($order);
        }
    }

    public function deleted(Order $order)
    {
        $this->created($order);
    }
}
