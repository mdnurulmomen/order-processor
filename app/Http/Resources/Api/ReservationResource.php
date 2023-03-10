<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'guest_number' => $this->guest_number,
            'mobile' => $this->mobile,
            'merchant_id' => $this->merchant_id,
            'max_payment_time' => $this->max_payment_time,
            'confirmation' => $this->order->customer_confirmation,
            'order_id' => $this->order_id,
            // 'order' => new OrderResource($this->order),
        ];
    }
}
