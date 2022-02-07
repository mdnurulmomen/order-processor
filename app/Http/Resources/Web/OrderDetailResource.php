<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
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
            'order_price' => $this->order_price,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'delivery_fee' => $this->when($this->delivery_fee, $this->delivery_fee),
            'net_payable' => $this->net_payable,
            'payment_method' => $this->payment_method,
            'payment' => $this->when($this->payment_method != 'cash', $this->payment ? $this->payment : 'cash'),
            'orderer' => $this->orderer,
            'asap' => /*$this->whenLoaded('asap')*/ $this->when($this->asap, $this->asap ? true : false),
            'schedule' => /*$this->whenLoaded('schedule')*/ $this->when($this->schedule, $this->schedule ? $this->schedule->order_schedule : false),
            'has_cutlery' => /*$this->whenLoaded('cutlery')*/ $this->when($this->cutlery, $this->cutlery ? true : false),
            'delivery' => $this->when($this->delivery, $this->delivery ? $this->delivery->additional_info : false),
            // 'reservation' => $this->reservation,
            'customer_confirmation' => $this->customer_confirmation,
            'in_progress' => $this->in_progress,
            'complete_order' => $this->complete_order,
            'restaurants' => OrderRestaurantResource::collection($this->restaurants) /*route('api.v1.ordered-restaurants.show', ['order' => $this->id])*/,
            'restaurant_acceptances' => OrderRestaurantAcceptanceResource::collection($this->restaurantAcceptances),
            'rider_assignment' => $this->riderAssignment,
            'order_ready_confirmations' => OrderReadyConfirmationResource::collection($this->orderReadyConfirmations),
            'rider_food_pick_confirmations' => OrderPickUpProgressionResource::collection($this->riderFoodPickConfirmations),
            'rider_delivery_confirmation' => $this->riderDeliveryConfirmation ? new RiderDeliveryResource($this->riderDeliveryConfirmation) : null,
            // 'rider_delivery_return' => $this->riderDeliveryReturn ? new RiderReturnResource($this->riderDeliveryReturn) : null,
            'order_serve_confirmation' => $this->when($this->orderServeConfirmation, $this->orderServeConfirmation),
            'customer_order_cancelation' => $this->when($this->customer_confirmation==0, $this->customerOrderCancelation),
            'restaurant_order_cancelations' => $this->when($this->restaurantOrderCancelations->count(), RestaurantCancelationResource::collection($this->restaurantOrderCancelations)),
            'admin_order_cancelation' => $this->when($this->adminOrderCancelation()->exists(), $this->adminOrderCancelation),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
