<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->load('user');
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('orders'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id' => $this->order->id,
                'user_name' => $this->order->user->name,
                'total' => $this->order->total,
                'created_at' => $this->order->created_at->toISOString(),
                'formatted_date' => $this->order->created_at->format('M d, Y'),
                'formatted_total' => '$' . number_format($this->order->total, 2),
            ],
            'stats_impact' => [
                'revenue_increase' => $this->order->total,
                'order_count_increase' => 1,
            ]
        ];

    }
}
