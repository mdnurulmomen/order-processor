<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderedRestaurantResource extends JsonResource
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
            'id' => $this->restaurant->id,
            'name' => $this->restaurant->name,
            'items' => OrderedRestaurantMenuItemResource::collection($this->items),
        ];
    }
}
