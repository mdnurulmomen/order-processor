<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RiderRestaurantItemsResource extends JsonResource
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
            'restaurant_menu_item_id' => $this->restaurant_menu_item_id,
            'quantity' => $this->quantity,
            'restaurant_menu_item' => $this->restaurantMenuItem,
            'selected_item_variation' => $this->selectedItemVariation,
            'additional_ordered_addons' => $this->additionalOrderedAddons,
        ];
    }
}
