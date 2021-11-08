<?php

namespace App\Events;

use App\Models\OrderedRestaurant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Http\Resources\Web\RestaurantOrderResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\Web\OrderedRestaurantItemResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UpdateRestaurant implements ShouldBroadcast
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
            'restaurant_id' => $this->orderedRestaurant->restaurant_id,
            'order' => new RestaurantOrderResource($this->orderedRestaurant->order),
            'items'=> OrderedRestaurantItemResource::collection($this->orderedRestaurant->items)
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs() 
    {
        return 'updation-for-restaurant';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifyRestaurant.'.$this->orderedRestaurant->restaurant_id);
    }
}
