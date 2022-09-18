<?php

namespace App\Events;

use App\Models\MerchantOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Http\Resources\Web\RestaurantOrderResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\Web\MyOrderedRestaurantResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UpdateAgents
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $merchantOrderRecord;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MerchantOrder $merchantOrderRecord)
    {
        $this->merchantOrderRecord = $merchantOrderRecord;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->merchantOrderRecord->id,
            'order_id' => $this->merchantOrderRecord->order_id,
            'merchant_id' => $this->merchantOrderRecord->merchant_id,
            'order_acceptance' => $this->merchantOrderRecord->order_acceptance,
            'order_ready_confirmation' => $this->merchantOrderRecord->order_ready_confirmation,
            'order' => new RestaurantOrderResource($this->merchantOrderRecord->order),
            'items' => OrderedRestaurantItemResource::collection($this->items),
            'order_cancellation_reasons' => $this->merchantOrderRecord->orderCancellations()->where('merchant_id', $this->merchantOrderRecord->merchant_id)->get(),
            'order_serve_progression' => $this->merchantOrderRecord->serveProgression,
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs() 
    {
        return 'updation-for-agents';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifyMerchantAgents.'.$this->merchantOrderRecord->merchant_id);
    }
}
