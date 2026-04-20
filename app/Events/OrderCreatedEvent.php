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

class OrderCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public array $data;

    public function __construct(Order $order)
    {
        $this->data = [
            'id' => $order->id,
            'order_number' => $order->order_number ?? $order->id,
            'customer_name' => $order->user->name ?? 'Customer',
            'total' => $order->total ?? 0,
            'created_at' => now()->timestamp,
            'message' => 'تم انشاء طلب جديد  #' . ($order->order_number ?? $order->id),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('admin-orders');
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}
