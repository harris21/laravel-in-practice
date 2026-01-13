<?php

namespace App\Livewire\Analytics;

use App\Models\Order;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    #[Url]
    public string $dateRange = 'month';

    public int $liveUpdateCount = 0;

    #[On('echo:orders,.order.placed')]
    public function handleNewOrder(array $payload): void
    {
        $this->liveUpdateCount++;

        Cache::forget("dashboard.charts.{$this->dateRange}");

        $chartData = $this->fetchChartData();
        Cache::put("dashboard.charts.{$this->dateRange}", $chartData, 60);

        $this->dispatch('charts-updated', chartData: $chartData);
        $this->dispatch('order-received', order: $payload['order']);

    }

    public function updateDateRange(string $range): void
    {
        $this->dateRange = $range;
        $this->resetPage();

        $chartData = $this->fetchChartData();
        Cache::put("dashboard.charts.{$this->dateRange}", $chartData, 60);

        $this->dispatch('charts-updated', chartData: $chartData);
    }

    public function getChartData(): array
    {
        $cacheKey = "dashboard.charts.{$this->dateRange}";

        return Cache::remember($cacheKey, 60, fn () => $this->fetchChartData());
    }

    private function fetchChartData(): array
    {
        $endDate = now()->endOfDay();
        $startDate = match ($this->dateRange) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfMonth()
        };

        $orders = Order::completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $grouped = $orders->groupBy(fn ($order) => $order->created_at->format('Y-m-d'));

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
