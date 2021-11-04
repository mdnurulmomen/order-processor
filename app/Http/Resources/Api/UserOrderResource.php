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
            'asap' => $this->whenLoaded('asap'),
            'scheduled' => $this->whenLoaded('scheduled'),
            'cutlery_added' => $this->whenLoaded('cutlery_added'),
            'order_price' => $this->order_price,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'delivery_fee' => $this->when($this->delivery_fee, $this->delivery_fee),
            'net_payable' => $this->net_payable,
            'payment_method' => $this->payment_method=='cash' ? 'Not-paid' : 'Paid',
            // 'orderer' => $this->orderer,
            'confirmation' => $this->customer_confirmation,
            'created_at' => $this->created_at,
        ];
    }
}
