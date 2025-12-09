<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <!-- Date Range Selector -->
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Analytics Dashboard</flux:heading>

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

    <!-- Stats Grid -->
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

    <!-- Recent Orders Table -->
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
                <tr wire:key="order-{{ $order->id }}">
                    <td class="py-3">#{{ $order->id }}</td>
                    <td class="py-3">{{ $order->user->name }}</td>
                    <td class="py-3">${{ number_format($order->total, 2) }}</td>
                    <td class="py-3 text-zinc-500">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
