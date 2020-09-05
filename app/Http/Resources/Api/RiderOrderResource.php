<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RiderOrderResource extends JsonResource
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
            'is_asap_order' => $this->is_asap_order,
            'order_schedule' => $this->order_schedule,
            'cutlery_addition' => $this->cutlery_addition,
            'order_price' => $this->order_price,
            'vat' => $this->vat,
            'discount' => $this->discount,
            'net_payable' => $this->net_payable,
            'payment_method' => $this->payment_method=='cash' ? 'Not-paid' : 'Paid',
            'orderer' => $this->orderer,
            'created_at' => $this->created_at,
        ];
    }
}
