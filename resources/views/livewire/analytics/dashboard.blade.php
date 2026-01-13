<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <!-- Date Range Selector (from Episode 14) -->
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Analytics Dashboard</flux:heading>

        @if($liveUpdateCount > 0)
            <div class="flex items-center text-green-600 dark:text-green-400">
                <div class="w-2 h-2 bg-green-600 rounded-full animate-pulse mr-2"></div>
                <span class="text-sm">
                    {{ $liveUpdateCount }} live {{ Str::plural('update', $liveUpdateCount) }}
                </span>
            </div>
        @endif

        <div class="flex items-center gap-2">
            <flux:button
                wire:click="updateDateRange('today')"
                :variant="$dateRange === 'today' ? 'primary' : 'ghost'"
                size="sm"
                class="cursor-pointer">
                <span wire:loading.remove wire:target="updateDateRange('today')">Today</span>
                <span wire:loading wire:target="updateDateRange('today')" class="flex items-center gap-2">
                    <flux:icon.arrow-path class="size-4 animate-spin" />
                    Today
                </span>
            </flux:button>
            <flux:button
                wire:click="updateDateRange('week')"
                :variant="$dateRange === 'week' ? 'primary' : 'ghost'"
                size="sm"
                class="cursor-pointer">
                <span wire:loading.remove wire:target="updateDateRange('week')">This Week</span>
                <span wire:loading wire:target="updateDateRange('week')" class="flex items-center gap-2">
                    <flux:icon.arrow-path class="size-4 animate-spin" />
                    This Week
                </span>
            </flux:button>
            <flux:button
                wire:click="updateDateRange('month')"
                :variant="$dateRange === 'month' ? 'primary' : 'ghost'"
                size="sm"
                class="cursor-pointer">
                <span wire:loading.remove wire:target="updateDateRange('month')">This Month</span>
                <span wire:loading wire:target="updateDateRange('month')" class="flex items-center gap-2">
                    <flux:icon.arrow-path class="size-4 animate-spin" />
                    This Month
                </span>
            </flux:button>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Revenue Trend</flux:heading>

            <div
                x-data="chartComponent({
                    type: 'line',
                    label: 'Revenue',
                    labels: @js($chartData['labels']),
                    data: @js($chartData['revenue']),
                    color: 'rgb(59, 130, 246)',
                    field: 'revenue'
                })"
                @charts-updated.window="updateChart($event.detail.chartData)"
                wire:ignore
                class="aspect-[2/1]"
            >
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <!-- Orders Chart -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">Orders Count</flux:heading>

            <div
                x-data="chartComponent({
                    type: 'bar',
                    label: 'Orders',
                    labels: @js($chartData['labels']),
                    data: @js($chartData['orders']),
                    color: 'rgb(34, 197, 94)',
                    field: 'orders'
                })"
                @charts-updated.window="updateChart($event.detail.chartData)"
                wire:ignore
                class="aspect-[2/1]"
            >
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>
    </div>

    <!-- Stats Grid (from Episode 14) -->
    <div class="grid auto-rows-min gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:text class="text-zinc-500 dark:text-zinc-400">Total Revenue</flux:text>
            <flux:heading size="xl" class="mt-1">${{ number_format($this->stats['total_revenue'], 2) }}</flux:heading>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:text class="text-zinc-500 dark:text-zinc-400">Total Orders</flux:text>
            <flux:heading size="xl" class="mt-1">{{ $this->stats['total_orders'] }}</flux:heading>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:text class="text-zinc-500 dark:text-zinc-400">Average Order</flux:text>
            <flux:heading size="xl" class="mt-1">${{ number_format($this->stats['average_order'], 2) }}</flux:heading>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:text class="text-zinc-500 dark:text-zinc-400">Unique Customers</flux:text>
            <flux:heading size="xl" class="mt-1">{{ $this->stats['unique_customers'] }}</flux:heading>
        </div>
    </div>

    <!-- Orders Table with Pagination (from Episode 14) -->
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
        <flux:heading size="lg" class="mb-4">Recent Orders</flux:heading>

        <table class="w-full text-left text-sm">
            <thead class="border-b border-neutral-200 dark:border-neutral-700">
            <tr>
                <th class="pb-3 font-medium text-zinc-500">Order ID</th>
                <th class="pb-3 font-medium text-zinc-500">Customer</th>
                <th class="pb-3 font-medium text-zinc-500">Total</th>
                <th class="pb-3 font-medium text-zinc-500">Date</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
            @foreach($orders as $order)
                <tr
                    wire:key="order-{{ $order->id }}"
                    x-data="{ isNew: false }"
                    @order-received.window="
            if ($event.detail.order.id === {{ $order->id }}) {
                isNew = true;
                setTimeout(() => isNew = false, 3000);
            }
        "
                    class="transition-all duration-500"
                    :class="{ 'bg-green-50 dark:bg-green-900/20': isNew }"
                >
                    <td class="py-3">#{{ $order->id }}</td>
                    <td class="py-3">{{ $order->user->name }}</td>
                    <td class="py-3">${{ number_format($order->total, 2) }}</td>
                    <td class="py-3 text-zinc-500">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('chartComponent', (config) => {
        let chartInstance = null;

        return {
            init() {
                this.createChart(config.labels, config.data);
            },

            createChart(labels, data) {
                const canvas = this.$refs.canvas;
                if (!canvas) return;

                if (chartInstance) {
                    chartInstance.destroy();
                    chartInstance = null;
                }

                chartInstance = new Chart(canvas, {
                    type: config.type,
                    data: {
                        labels: [...labels],
                        datasets: [{
                            label: config.label,
                            data: [...data],
                            borderColor: config.color,
                            backgroundColor: config.type === 'bar' ? config.color : (config.color + '20'),
                            fill: config.type === 'line',
                            tension: 0.4,
                            borderRadius: config.type === 'bar' ? 4 : 0,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            },

            updateChart(newData) {
                if (!newData || !newData.labels) return;

                const labels = [...newData.labels];
                const data = [...newData[config.field]];

                if (chartInstance) {
                    chartInstance.data.labels = labels;
                    chartInstance.data.datasets[0].data = data;
                    chartInstance.update();
                } else {
                    this.createChart(labels, data);
                }
            }
        };
    });
</script>
@endscript
