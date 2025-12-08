<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\CacheInvalidationService;

class OrderObserver
{
    public function __construct(private readonly CacheInvalidationService $cacheService)
    {
    }

    public function created(Order $order)
    {
        if ($order->created_at->isToday()) {
            $this->cacheService->refreshDashboardCache('today');
        }

        if ($order->created_at->isCurrentWeek()) {
            $this->cacheService->refreshDashboardCache('week');
        }

        if ($order->created_at->isCurrentMonth()) {
            $this->cacheService->refreshDashboardCache('month');
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
