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
            'type' => $this->type,
            'is_asap_order' => /*$this->whenLoaded('is_asap_order')*/ $this->when($this->is_asap_order, $this->is_asap_order ? true : false),
            'schedule' => /*$this->whenLoaded('schedule')*/ $this->when($this->schedule, $this->schedule ? $this->schedule->schedule : false),
            'has_cutlery' => /*$this->whenLoaded('cutlery')*/ $this->when($this->cutlery, $this->cutlery ? true : false),
            'price' => $this->price,
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
            'merchants' => route('api.v1.order-merchants.show', ['order' => $this->id]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
