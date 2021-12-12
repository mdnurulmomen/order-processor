<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderRestaurantItemResource extends JsonResource
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
           'item_variation'=>($this->restaurantMenuItem->has_variation & $this->variation()->exists()) ? new OrderItemVariationResource($this->variation) : false,
           'item_addons'=>($this->restaurantMenuItem->has_addon & $this->addons()->exists()) ? OrderItemAddonResource::collection($this->addons) : false,
           'customization' => $this->when($this->customization, $this->customization ? $this->customization->custom_instruction : false),
        ];
    }
}
