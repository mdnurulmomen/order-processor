<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\SelectedItemVariationResource;
use App\Http\Resources\Api\AdditionalOrderedAddonResource;


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
            'selected_item_variation' => new SelectedItemVariationResource($this->selectedItemVariation),
            'additional_ordered_addons' => AdditionalOrderedAddonResource::collection($this->additionalOrderedAddons),
        ];
    }
}
