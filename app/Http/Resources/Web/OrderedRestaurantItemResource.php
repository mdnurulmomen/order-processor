<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Web\RestaurantMenuItemResource;
use App\Http\Resources\Api\SelectedItemVariationResource;
use App\Http\Resources\Api\AdditionalOrderedAddonResource;

class OrderedRestaurantItemResource extends JsonResource
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
           'restaurant_menu_item'=>new RestaurantMenuItemResource($this->restaurantMenuItem),
           'quantity'=>$this->quantity,
           'selected_item_variation'=>($this->restaurantMenuItem->has_variation & $this->selectedItemVariation()->exists()) ? new SelectedItemVariationResource($this->selectedItemVariation) : false,
           'additional_ordered_addons'=>($this->restaurantMenuItem->has_addon & $this->additionalOrderedAddons()->exists()) ? AdditionalOrderedAddonResource::collection($this->additionalOrderedAddons) : false,
        ];
    }
}
