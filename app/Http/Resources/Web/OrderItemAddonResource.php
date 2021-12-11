<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemAddonResource extends JsonResource
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
            // 'id' => $this->id,
            'restaurant_menu_item_addon' => /*new RestaurantMenuItemAddonResource($this->restaurantMenuItemAddon)*/ $this->restaurantMenuItemAddon->addon->name,
            'restaurant_menu_item_addon_id' => $this->restaurant_menu_item_addon_id,
            'quantity' => $this->quantity,
        ];
    }
}
