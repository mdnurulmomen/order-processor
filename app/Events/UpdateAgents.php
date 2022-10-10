<?php

namespace App\Events;

use App\Models\MerchantOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\Web\OrderResource;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use App\Http\Resources\Web\ServingOrderResource;
use App\Http\Resources\Web\ProductOrderResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
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
            'is_accepted' => $this->merchantOrderRecord->is_accepted,
            'accepted_at' => $this->merchantOrderRecord->accepted_at,
            'is_ready' => $this->merchantOrderRecord->is_ready,
            'ready_at' => $this->merchantOrderRecord->ready_at,
            'order' => new OrderResource($this->merchantOrderRecord->order),
            'products' => ProductOrderResource::collection($this->products),
            'order_cancellation_reasons' => $this->merchantOrderRecord->orderCancellations()->where('merchant_id', $this->merchantOrderRecord->merchant_id)->get(),
            'order_serve_progression' => new ServingOrderResource($this->merchantOrderRecord->serve),
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
