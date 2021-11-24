<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_type' => $this->order_type,
            'asap' => /*$this->whenLoaded('asap')*/ $this->when($this->asap, $this->asap ? true : false),
            'scheduled' => /*$this->whenLoaded('scheduled')*/ $this->when($this->scheduled, $this->scheduled ? $this->scheduled->order_schedule : false),
            'cutlery_added' => /*$this->whenLoaded('cutleryAdded')*/ $this->when($this->cutleryAdded, $this->cutleryAdded ? true : false),
            'order_price' => $this->order_price,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'delivery_fee' => $this->when($this->delivery_fee, $this->delivery_fee),
            'net_payable' => $this->net_payable,
            'payment_method' => $this->payment_method,
            'payment' => $this->when($this->payment_method != 'cash', $this->payment ? $this->payment : 'cash'),
            'delivery' => $this->when($this->delivery, $this->delivery ? $this->delivery->additional_info : false),
            // 'orderer' => $this->orderer,
            'customer_confirmation' => $this->customer_confirmation,
            // 'restaurants' => OrderedRestaurantResource::collection($this->restaurants) /*route('api.v1.ordered-restaurants.show', ['order' => $this->id])*/,
            'restaurant_acceptances' => OrderedRestaurantAcceptanceResource::collection($this->restaurantAcceptances),
            'rider_assignment' => $this->riderAssignment,
            'order_ready_confirmations' => OrderReadyConfirmationResource::collection($this->orderReadyConfirmations),
            'rider_food_pick_confirmations' => OrderPickUpProgressionResource::collection($this->riderFoodPickConfirmations),
            'rider_delivery_confirmation' => $this->riderDeliveryConfirmation ? new RiderDeliveryResource($this->riderDeliveryConfirmation) : null,
            'order_serve_confirmation' => $this->when($this->orderServeConfirmation, $this->orderServeConfirmation),
            'customer_order_cancelation' => $this->when($this->customer_confirmation==0, $this->customerOrderCancelation),
            'restaurant_order_cancelations' => $this->when($this->restaurantOrderCancelations->count(), RestaurantCancelationResource::collection($this->restaurantOrderCancelations)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
