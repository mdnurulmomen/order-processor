<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resources\Api\RestaurantMenuItemVariationResource;

class SelectedItemVariationResource extends JsonResource
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
            'restaurant_menu_item_variation' => /* new RestaurantMenuItemVariationResource($this->restaurantMenuItemVariation()->withTrashed()->first()) */ $this->restaurantMenuItemVariation()->withTrashed()->first()->variation->name,
        ];
    }
}
