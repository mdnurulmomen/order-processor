<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resources\Api\SelectedItemVariationResource;
// use App\Http\Resources\Api\AdditionalOrderedAddonResource;


class OrderedRestaurantMenuItemResource extends JsonResource
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
            'quantity' => $this->quantity,
            'restaurant_menu_item_id' => $this->restaurant_menu_item_id,
            'restaurant_menu_item' => new RestaurantMenuItemResource($this->restaurantMenuItem),
            'selected_item_variation' => $this->when($this->restaurantMenuItem->has_variation, new SelectedItemVariationResource($this->selectedItemVariation)),
            'additional_ordered_addons' => AdditionalOrderedAddonResource::collection($this->additionalOrderedAddons),
            'custom_instruction' => $this->when($this->orderedItemCustomization, $this->orderedItemCustomization ? $this->orderedItemCustomization->custom_instruction : 'No Custom Instruction'),
        ];
    }
}
