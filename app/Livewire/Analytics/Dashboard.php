<?php

namespace App\Livewire\Analytics;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class Dashboard extends Component
{
    use WithPagination;

    #[Url]
    public string $dateRange = 'month';

    public function updateDateRange(string $range): void
    {
        $this->dateRange = $range;
        $this->resetPage();
    }

    public function getStatsProperty(): array
    {
        $orders = Order::query()
            ->completed()
            ->forPeriod($this->dateRange)
            ->get();

        return [
            'total_revenue' => $orders->totalRevenue(),
            'total_orders' => $orders->count(),
            'average_order' => $orders->averageOrderValue(),
            'unique_customers' => $orders->unique('user_id')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.analytics.dashboard', [
            'orders' => Order::query()
            ->completed()
            ->forPeriod($this->dateRange)
            ->withUser()
            ->latest()
            ->paginate(10)
        ]);
    }
}
