<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderPickUpProgressionResource extends JsonResource
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
            'rider_food_pick_confirmation' => $this->rider_food_pick_confirmation,
            'order_id' => $this->order_id,
            'restaurant_id' => $this->restaurant_id,
            'rider_id' => $this->rider_id,
            'restaurant' => $this->restaurant,
            'rider' => $this->rider,
        ];
    }
}
