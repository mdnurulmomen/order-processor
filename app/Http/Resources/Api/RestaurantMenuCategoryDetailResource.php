<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\RestaurantMenuItemDetailResource;

class RestaurantMenuCategoryDetailResource extends JsonResource
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

            'restaurantMenuCategoryName' => $this->menuCategory->name,
            'serving_from' => $this->serving_from,
            'serving_to' => $this->serving_to,
            'menuItems' => RestaurantMenuItemDetailResource::collection($this->restaurantMenuItems),

        ];
    }
}
