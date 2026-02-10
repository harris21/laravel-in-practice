<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <!-- Date Range Selector (from Episode 14) -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:heading size="xl">Analytics Dashboard</flux:heading>

            <div x-data="{ liveUpdateCount: 0 }"
                 @order-received.window="liveUpdateCount++"
                 x-show="liveUpdateCount > 0"
                 x-cloak
                 class="flex items-center text-green-600 dark:text-green-400">
                <div class="w-2 h-2 bg-green-600 rounded-full animate-pulse mr-2"></div>
                <span class="text-sm">
                    <span x-text="liveUpdateCount"></span> live <span x-text="liveUpdateCount === 1 ? 'update' : 'updates'"></span>
                </span>
            </div>
        </div>

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
    <div class="grid auto-rows-min gap-4 md:grid-cols-4"
         x-data="{
             totalRevenue: {{ $this->stats['total_revenue'] }},
             totalOrders: {{ $this->stats['total_orders'] }},
             averageOrder: {{ $this->stats['average_order'] }},
             uniqueCustomers: {{ $this->stats['unique_customers'] }},
             formatCurrency(value) {
                 return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
             }
         }"
         @stats-updated.window="
             totalRevenue = $event.detail.stats.total_revenue;
             totalOrders = $event.detail.stats.total_orders;
             averageOrder = $event.detail.stats.average_order;
             uniqueCustomers = $event.detail.stats.unique_customers;
         ">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:text class="text-zinc-500 dark:text-zinc-400">Total Revenue</flux:text>
            <flux:heading size="xl" class="mt-1">$<span x-text="formatCurrency(totalRevenue)"></span></flux:heading>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:text class="text-zinc-500 dark:text-zinc-400">Total Orders</flux:text>
            <flux:heading size="xl" class="mt-1" x-text="totalOrders"></flux:heading>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:text class="text-zinc-500 dark:text-zinc-400">Average Order</flux:text>
            <flux:heading size="xl" class="mt-1">$<span x-text="formatCurrency(averageOrder)"></span></flux:heading>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-zinc-900">
            <flux:text class="text-zinc-500 dark:text-zinc-400">Unique Customers</flux:text>
            <flux:heading size="xl" class="mt-1" x-text="uniqueCustomers"></flux:heading>
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
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700"
                   x-data="ordersTable()"
                   @order-received.window="prependOrder($event.detail.order)">
            <template x-for="order in newOrders" :key="'new-' + order.id">
                <tr x-show="order.show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    class="bg-green-50 dark:bg-green-900/20 transition-all duration-500">
                    <td class="py-3" x-text="'#' + order.id"></td>
                    <td class="py-3" x-text="order.user_name"></td>
                    <td class="py-3" x-text="order.formatted_total"></td>
                    <td class="py-3 text-zinc-500" x-text="order.formatted_date"></td>
                </tr>
            </template>
            @foreach($orders as $index => $order)
                <tr wire:key="order-{{ $order->id }}"
                    x-data="{ show: false }"
                    x-init="setTimeout(() => show = true, {{ $index * 50 }})"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    class="transition-all duration-500">
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
    Alpine.data('ordersTable', () => ({
        newOrders: [],

        prependOrder(order) {
            this.newOrders.unshift({ ...order, show: false });

            setTimeout(() => {
                this.newOrders[0].show = true;
            }, 10);
        }
    }));

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
