<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantAcceptanceResource extends JsonResource
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
            // 'id' => $this->id,
            'order_id' => $this->order_id,
            'restaurant_id' => $this->restaurant_id,
            'restaurant_name' => $this->restaurant->name,
            'food_order_acceptance' => $this->food_order_acceptance,
        ];
    }
}
