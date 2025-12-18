<?php

namespace App\Livewire\Analytics;

use Carbon\Carbon;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    use WithPagination;

    #[Url]
    public string $dateRange = 'month';

    public function updateDateRange(string $range): void
    {
        $this->dateRange = $range;
        $this->resetPage();

        Cache::forget("dashboard.charts.{$this->dateRange}");

        $this->dispatch('charts-updated', chartData: $this->getChartData());
    }

    public function getChartData(): array
    {
        $cacheKey = "dashboard.charts.{$this->dateRange}";

        return Cache::remember($cacheKey, 60, function () {
            $endDate = now()->endOfDay();
            $startDate = match($this->dateRange) {
                'today' => now()->startOfDay(),
                'week' => now()->startOfWeek(),
                'month' => now()->startOfMonth(),
                default => now()->startOfMonth()
            };

            $orders = Order::completed()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $grouped = $orders->groupBy(fn($order) => $order->created_at->format('Y-m-d'));

            $labels = [];
            $revenueData = [];
            $ordersData = [];

            $period = Carbon::parse($startDate)->toPeriod($endDate);

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $dayOrders = $grouped->get($dateStr, collect());

                $labels[] = $date->format('M j');
                $revenueData[] = $dayOrders->sum('total');
                $ordersData[] = $dayOrders->count();
            }

            return [
                'labels' => $labels,
                'revenue' => $revenueData,
                'orders' => $ordersData,
            ];
        });
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
                ->paginate(10),
            'chartData' => $this->getChartData(),
        ]);
    }
}
