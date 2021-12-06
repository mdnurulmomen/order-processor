<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRegularItemVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $restaurantMenuItemVariation = $this->restaurantMenuItemVariation()->withTrashed()->first();

        return [
            'restaurant_menu_item_variation_id' => $this->id,
            'restaurant_menu_item_variation' => /* new RestaurantMenuItemVariationResource($this->restaurantMenuItemVariation()->withTrashed()->first()) */ $restaurantMenuItemVariation->variation->name,
            'price' => $restaurantMenuItemVariation->price,
        ];
    }
}
