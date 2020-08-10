<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use App\Models\RestaurantOrderRecord;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Http\Resources\Web\RestaurantOrderResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\Web\MyOrderedRestaurantResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UpdateWaiters implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $restaurantOrderRecord;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RestaurantOrderRecord $restaurantOrderRecord)
    {
        $this->restaurantOrderRecord = $restaurantOrderRecord;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->restaurantOrderRecord->id,
            'order_id' => $this->restaurantOrderRecord->order_id,
            'restaurant_id' => $this->restaurantOrderRecord->restaurant_id,
            'food_order_acceptance' => $this->restaurantOrderRecord->food_order_acceptance,
            'order' => new RestaurantOrderResource($this->restaurantOrderRecord->order),
            'ordered_restaurants' => MyOrderedRestaurantResource::collection($this->restaurantOrderRecord->orderedRestaurants()->where('restaurant_id', $this->restaurantOrderRecord->restaurant_id)->get()),
            'order_cancellation_reasons' => $this->restaurantOrderRecord->orderCancellationReasons()->where('restaurant_id', $this->restaurantOrderRecord->restaurant_id)->get(),
            'order_ready_confirmations' => $this->restaurantOrderRecord->orderReadyConfirmations()->where('restaurant_id', $this->restaurantOrderRecord->restaurant_id)->get(),
            'order_serve_progression' => $this->restaurantOrderRecord->orderServeProgression,
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs() 
    {
        return 'updation-for-waiters';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifyRestaurantWaiters.'.$this->restaurantOrderRecord->restaurant_id);
    }
}
