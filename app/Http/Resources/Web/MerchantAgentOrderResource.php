<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantAgentOrderResource extends JsonResource
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
            'type' => $this->type,
            'is_asap_order' => $this->is_asap_order,
            'schedule' => $this->whenLoaded('schedule'),
            'price' => $this->price,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'delivery_fee' => $this->delivery_fee,
            'net_payable' => $this->net_payable,
            'payment_method' => $this->payment_method,
            'has_cutlery' => $this->has_cutlery,
            'orderer_type' => $this->orderer_type,
            'orderer_id' => $this->orderer_id,
            'customer_confirmation' => $this->customer_confirmation,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'orderer' => $this->orderer,
        ];
    }
}
