<?php

namespace App\Http\Resources\Api;

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
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'asap' => $this->whenLoaded('asap'),
            'scheduled' => $this->whenLoaded('scheduled'),
            'cutlery_added' => $this->whenLoaded('cutleryAdded'),
            'order_price' => $this->order_price,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'delivery_fee' => $this->when($this->delivery_fee, $this->delivery_fee),
            'net_payable' => $this->net_payable,
            'payment_method' => $this->payment_method,
            'payment' => $this->when($this->payment_method != 'cash', $this->payment),
            'delivery' => $this->when($this->delivery, $this->delivery),
            // 'orderer' => $this->orderer,
            'confirmation' => $this->customer_confirmation,
            'restaurants' => OrderedRestaurantResource::collection($this->restaurants),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
