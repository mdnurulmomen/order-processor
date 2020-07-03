<?php

namespace App\Events;

use App\Models\OrderedRestaurant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UpdateRestaurant implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderedRestaurant;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(OrderedRestaurant $orderedRestaurant)
    {
        $this->orderedRestaurant = $orderedRestaurant;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'order_id' => $this->orderedRestaurant->order_id,
            'order_schedule' => $this->orderedRestaurant->order->is_asap_order ? 'ASAP' : $this->orderedRestaurant->order->delivery_datetime,
            'order_items' => $this->orderedRestaurant->items,
            'cutlery_addition' => $this->orderedRestaurant->order->cutlery_addition,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('notifyRestaurant.'.$this->orderedRestaurants->restaurant_id);
        return new PrivateChannel('notifyRestaurant');
    }
}
