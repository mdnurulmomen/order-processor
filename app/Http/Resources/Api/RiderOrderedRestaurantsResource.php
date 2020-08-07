<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\RiderRestaurantItemsResource;

class RiderOrderedRestaurantsResource extends JsonResource
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
            'restaurant_id' => $this->restaurant_id,
            'order_id' => $this->order_id,
            'restaurant' => $this->restaurant,
            'items' => RiderRestaurantItemsResource::collection($this->items),
        ];
    }
}
