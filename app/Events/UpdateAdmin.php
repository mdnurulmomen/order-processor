<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use App\Http\Resources\Web\ServingOrderResource;
use App\Http\Resources\Web\RiderDeliveryResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Http\Resources\Web\MerchantOrderResource;
use App\Http\Resources\Web\OrderCollectionResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\Web\MerchantCancellationResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UpdateAdmin implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->order->id,
            'type' => $this->order->type,
            'in_progress' => $this->order->in_progress,
            'customer_confirmation' => $this->order->customer_confirmation ?? -1,
            'merchants' => MerchantOrderResource::collection($this->order->merchants),
            'rider_assigned' => $this->order->riderAssigned()->exists() ? new RiderDeliveryResource($this->order->riderAssigned) : null,
            'collections' => OrderCollectionResource::collection($this->order->collections),
            'order_serve_confirmation' => $this->order->serve()->exists() ? new ServingOrderResource($this->order->serve) : null,
            'merchant_order_cancellations' => MerchantCancellationResource::collection($this->order->merchantOrderCancellations),
            
            /*
            'restaurant_acceptances' => RestaurantAcceptanceResource::collection($this->order->restaurantAcceptances),
            'rider_assignment' => $this->order->riderAssignment,
            'order_ready_confirmations' => OrderReadyConfirmationResource::collection($this->order->orderReadyConfirmations),
            'rider_food_pick_confirmations' => OrderPickUpProgressionResource::collection($this->order->riderFoodPickConfirmations),
            'rider_delivery_confirmation' => $this->order->riderDeliveryConfirmation ? new RiderDeliveryResource($this->order->riderDeliveryConfirmation) : null,
            'order_serve_confirmation' => $this->order->orderServeConfirmation,
            'restaurant_order_cancellations' => RestaurantCancellationResource::collection($this->order->restaurantOrderCancellations),
             */
            
            /*
            'order_price' => $this->order->order_price,
            'vat' => $this->order->vat,
            'discount' => $this->order->discount,
            'delivery_fee' => $this->order->delivery_fee,
            'net_payable' => $this->order->net_payable,
            'payment_method' => $this->order->payment_method,
            'orderer_type' => $this->order->orderer_type,
            'orderer_id' => $this->order->orderer_id,
            'created_at' => $this->order->created_at,
            'updated_at' => $this->order->updated_at,
            'orderer' => $this->order->orderer,
            */
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs() 
    {
        return 'updation-for-admin';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('notifyAdmin');
    }
}
