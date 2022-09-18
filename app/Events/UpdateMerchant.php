<?php

namespace App\Events;

use App\Models\MerchantOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\Web\OrderResource;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\Web\OrderRestaurantItemResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UpdateMerchant implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $merchantOrder;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MerchantOrder $merchantOrder)
    {
        $this->merchantOrder = $merchantOrder;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'order_id' => $this->merchantOrder->order_id,
            'merchant_id' => $this->merchantOrder->merchant_id,
            'order' => new OrderResource($this->merchantOrder->order),
            'products'=> OrderRestaurantItemResource::collection($this->merchantOrder->items)
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs() 
    {
        return 'updation-for-merchant';
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifyMerchant.'.$this->merchantOrder->merchant_id);
    }
}
