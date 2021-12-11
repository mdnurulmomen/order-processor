<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderRestaurantResource extends JsonResource
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
            'menu_items' => OrderRestaurantMenuItemResource::collection($this->items),
        ];
    }
}
