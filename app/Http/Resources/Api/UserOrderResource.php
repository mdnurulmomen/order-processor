<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'order_type' => $this->order_type,
            'asap' => /*$this->whenLoaded('asap')*/ $this->when($this->asap, $this->asap ? true : false),
            'scheduled' => /*$this->whenLoaded('scheduled')*/ $this->when($this->scheduled, $this->scheduled ? $this->scheduled->order_schedule : false),
            'has_cutlery' => /*$this->whenLoaded('cutlery')*/ $this->when($this->cutlery, $this->cutlery ? true : false),
            'order_price' => $this->order_price,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'delivery_fee' => $this->when($this->delivery_fee, $this->delivery_fee),
            'net_payable' => $this->net_payable,
            'payment_method' => $this->payment_method,
            'payment_id' => $this->when($this->payment_method != 'cash', $this->payment ? $this->payment->payment_id : false),
            'delivery' => $this->when($this->delivery, $this->delivery ? $this->delivery->additional_info : false),
            // 'orderer' => $this->orderer,
            'customer_confirmation' => $this->customer_confirmation,
            'in_progress' => $this->in_progress,
            'restaurants' => /*OrderedRestaurantResource::collection($this->restaurants)*/ route('api.v1.order-restaurants.show', ['order' => $this->id]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
