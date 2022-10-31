<?php

namespace App\Events;

use App\Models\RiderDelivery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use App\Http\Resources\Web\RiderOrderResource;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Http\Resources\Web\MerchantOrderResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UpdateRider implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $riderNewDeliveryRecord;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RiderDelivery $riderDelivery)
    {
        $this->riderNewDeliveryRecord = $riderDelivery;
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
            'is_accepted' => $this->riderNewDeliveryRecord->is_accepted,
            'order_id' => $this->riderNewDeliveryRecord->order_id,
            'rider_id' => $this->riderNewDeliveryRecord->rider_id,
            'is_delivered' => $this->riderNewDeliveryRecord->is_delivered,
            'order' => new RiderOrderResource($this->riderNewDeliveryRecord->order),
            'rider_order_cancellations' => $this->riderNewDeliveryRecord->riderOrderCancellations,
            'merchant_order_cancellations' => $this->riderNewDeliveryRecord->merchantOrderCancellations,
            'merchants' => MerchantOrderResource::collection($this->riderNewDeliveryRecord->merchants()->where('is_self_delivery', 0)->get()),

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
