<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Web\RestaurantAcceptanceResource;
use App\Http\Resources\Web\RestaurantCancelationResource;
use App\Http\Resources\Web\OrderReadyConfirmationResource;

class RestaurantOrderResource extends JsonResource
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
            'is_asap_order' => $this->is_asap_order,
            'delivery_datetime' => $this->delivery_datetime,
            'order_price' => $this->order_price,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'delivery_fee' => $this->delivery_fee,
            'net_payable' => $this->net_payable,
            'payment_method' => $this->payment_method,
            'cutlery_addition' => $this->cutlery_addition,
            'orderer_type' => $this->orderer_type,
            'orderer_id' => $this->orderer_id,
            'call_confirmation' => $this->call_confirmation,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'orderer' => $this->orderer,
            'restaurant_acceptances' => RestaurantAcceptanceResource::collection($this->restaurantAcceptances),
            'order_ready_confirmations' => OrderReadyConfirmationResource::collection($this->orderReadyConfirmations),
            'waiter_serve_confirmation' => $this->waiterServeConfirmation,
            'restaurant_order_cancelations' => RestaurantCancelationResource::collection($this->restaurantOrderCancelations),
        ];
    }
}
