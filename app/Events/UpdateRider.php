<?php

namespace App\Events;

use App\Models\RiderDeliveryRecord;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use App\Http\Resources\Api\RiderOrderResource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UpdateRider implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $riderNewDeliveryRecord;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RiderDeliveryRecord $riderDeliveryRecord)
    {
        $this->riderNewDeliveryRecord = $riderDeliveryRecord;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [

            'id' => $this->riderNewDeliveryRecord->id,
            'delivery_order_acceptance' => $this->riderNewDeliveryRecord->delivery_order_acceptance,
            'order_id' => $this->riderNewDeliveryRecord->order_id,
            'rider_id' => $this->riderNewDeliveryRecord->rider_id,
            // 'created_at' => $this->riderNewDeliveryRecord->created_at,
            'order' => new RiderOrderResource($this->riderNewDeliveryRecord->order),
            'delivery' => $this->riderNewDeliveryRecord->delivery,
            'restaurant_order_cancelations' => $this->riderNewDeliveryRecord->restaurantOrderCancelations,
            'restaurants' => $this->riderNewDeliveryRecord->restaurants,
            'restaurants_accepted' => $this->riderNewDeliveryRecord->restaurantsAccepted,
            'rider_delivery_confirmation' => $this->riderNewDeliveryRecord->riderDeliveryConfirmation,
            'rider_food_pick_confirmations' => $this->riderNewDeliveryRecord->riderFoodPickConfirmations,
            'rider_order_cancelations' => $this->riderNewDeliveryRecord->riderOrderCancelations,

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
        return new PrivateChannel('notifyRider');
    }
}
