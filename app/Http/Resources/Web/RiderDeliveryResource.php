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
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'rider_delivery_confirmation' => $this->rider_delivery_confirmation,
            'rider_id' => $this->rider_id,
            'order_id ' => $this->order_id,
        ];
    }
}
