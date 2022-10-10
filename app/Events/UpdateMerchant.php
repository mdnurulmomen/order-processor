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
use App\Http\Resources\Web\ProductOrderResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
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
        $this->merchantOrder = $merchantOrder->loadMissing(['products.merchantProduct', 'products.variation.merchantProductVariation.variation', 'products.addons.merchantProductAddon.addon', 'products.customization', 'order.orderer', 'serve']);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->merchantOrder->id,
            'merchant_id' => $this->merchantOrder->merchant_id,
            'merchant_name' => $this->merchantOrder->merchant->name,
            'is_accepted' => $this->merchantOrder->is_accepted,
            'accepted_at' => $this->merchantOrder->accepted_at,
            'is_ready' => $this->merchantOrder->is_ready,
            'ready_at' => $this->merchantOrder->ready_at,
            'order_id' => $this->merchantOrder->order_id,
            'order' => new OrderResource($this->merchantOrder->order),
            'products' => ProductOrderResource::collection($this->merchantOrder->products),
            'order_serve_confirmation' => $this->merchantOrder->serve,
            'created_at' => $this->merchantOrder->created_at
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
