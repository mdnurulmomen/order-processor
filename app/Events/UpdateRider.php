<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Http\Resources\Web\RestaurantOrderResource;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UpdateRider implements ShouldBroadcastNow
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
            // 'order_type' => $this->order->order_type,
            'is_asap_order' => $this->order->is_asap_order,
            'cutlery_addition' => $this->order->cutlery_addition,
            'order_price' => $this->order->order_price,
            'vat' => $this->order->vat,
            'discount' => $this->order->discount,
            // 'delivery_fee' => $this->order->delivery_fee,
            'net_payable' => $this->order->net_payable,
            'payment_method' => $this->order->payment_method=='cash' ? 'Not-paid' : 'Paid',
            // 'orderer_type' => $this->order->orderer_type,
            // 'orderer_id' => $this->order->orderer_id,
            // 'call_confirmation' => $this->order->call_confirmation ?? -1,
            'created_at' => $this->order->created_at,
            // 'updated_at' => $this->order->updated_at,
            // 'orderer' => $this->order->orderer,
            // 'restaurant_acceptances' => RestaurantAcceptanceResource::collection($this->order->restaurantAcceptances),
            // 'order_ready_confirmations' => OrderReadyConfirmationResource::collection($this->order->orderReadyConfirmations),
            // 'waiter_serve_confirmation' => $this->order->waiterServeConfirmation
            // 'rider_assignment' => $this->order->riderAssignment,
            
            // 'rider_delivery_confirmation' => $this->order->riderDeliveryConfirmation ? new RiderDeliveryResource($this->order->riderDeliveryConfirmation) : null,
            // 'rider_food_pick_confirmations' => OrderPickUpProgressionResource::collection($this->order->riderFoodPickConfirmations),
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
        return new PrivateChannel('notifyRestaurant');
    }
}
