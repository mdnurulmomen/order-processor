<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRegularRestaurantResource extends JsonResource
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
            'restaurant_id' => $this->restaurant_id,
            'restaurant_name' => $this->restaurant->name,
            'menu_items' => MyRegularItemResource::collection($this->menuItems),
        ];
    }
}
