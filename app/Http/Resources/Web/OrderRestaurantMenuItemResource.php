<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderRestaurantMenuItemResource extends JsonResource
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
            // 'restaurant_menu_item_id' => $this->restaurant_menu_item_id,
            'restaurant_menu_item' => new RestaurantMenuItemResource($this->restaurantMenuItem),
            'quantity' => $this->quantity,
            'item_variation' => $this->when($this->restaurantMenuItem->has_variation, new OrderItemVariationResource($this->variation)),
            'item_addons' => OrderItemAddonResource::collection($this->addons),
            'customization' => $this->when($this->customization, $this->customization ? $this->customization->custom_instruction : false),
        ];
    }
}
