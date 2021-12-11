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
use App\Http\Resources\Api\OrderRestaurantResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Http\Resources\Api\OrderRestaurantAcceptanceResource;

class UpdateRider implements ShouldBroadcast
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
            'delivery' => $this->riderNewDeliveryRecord->delivery,
            'order' => new RiderOrderResource($this->riderNewDeliveryRecord->order),
            'rider_order_cancelations' => $this->riderNewDeliveryRecord->riderOrderCancelations,
            'rider_delivery_confirmation' => $this->riderNewDeliveryRecord->riderDeliveryConfirmation,
            'rider_food_pick_confirmations' => $this->riderNewDeliveryRecord->riderFoodPickConfirmations,
            'restaurant_order_cancelations' => $this->riderNewDeliveryRecord->restaurantOrderCancelations,
            'restaurants' => OrderRestaurantResource::collection($this->riderNewDeliveryRecord->restaurants),
            'restaurants_accepted' => OrderRestaurantAcceptanceResource::collection($this->riderNewDeliveryRecord->restaurantsAccepted),

        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifyRider.'.$this->riderNewDeliveryRecord->rider_id);
    }
}
