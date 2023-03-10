<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class RiderDeliveryResource extends JsonResource
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
            'delivery_fee' => $this->delivery_fee,
            'rider_call_receiving_time' => $this->rider_call_receiving_time,
            'is_accepted' => $this->is_accepted,
            'accepted_at' => $this->accepted_at,
            'is_delivered' => $this->is_delivered,
            'delivered_at' => $this->delivered_at,
            'created_at' => $this->created_at,
        ];
    }
}
