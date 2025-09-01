<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class OrderCollection extends Collection
{
    public function totalRevenue(): float
    {
        return $this->sum('total');
    }

    public function averageOrderValue(): float
    {
        return $this->avg('total');
    }

    public function businessSummary(): array
    {
        return [
            'total_revenue' => $this->totalRevenue(),
            'average_order_value' => $this->averageOrderValue(),
            'total_orders' => $this->count(),
        ];
    }

    public function averageOrdersByCustomer(): float
    {
        if ($this->isEmpty()) {
            return 0;
        }

        $customerOrderCounts = $this->groupBy('user_id')->map(fn($orders) => $orders->count());

        return $customerOrderCounts->avg();
    }

    public function topCustomers(int $limit = 5): SupportCollection
    {
        return $this->groupBy('user_id')
            ->map(fn($orders) => [
                'name' => $orders->first()->user->name,
                'total_spent' => $orders->sum('total'),
                'order_count' => $orders->count()
            ])
            ->sortByDesc('total_spent')
            ->take($limit)
            ->values();
    }

    public function dailyBreakdown(): SupportCollection
    {
        return $this->groupBy(fn($order) => $order->created_at->toDateString())
            ->map(fn($orders) => [
                'date' => $orders->first()->created_at->format('M j'),
                'revenue' => $orders->sum('total'),
                'orders' => $orders->count()
            ])
            ->sortBy('date')
            ->values();
    }

    public function topProducts(int $limit = 5): SupportCollection
    {
        return $this->flatMap->items
            ->groupBy('product_id')
            ->map(fn($items) => [
                'product_name' => $items->first()->product->name,
                'revenue' => $items->sum(fn($item) => $item->quantity * $item->price)
            ])
            ->sortByDesc('revenue')
            ->take($limit)
            ->values();
    }
}
