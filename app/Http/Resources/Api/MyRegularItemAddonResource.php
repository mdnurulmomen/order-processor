<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRegularItemAddonResource extends JsonResource
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
            'restaurant_menu_item_addon_id' => /*new RestaurantMenuItemAddonResource($this->restaurantMenuItemAddon)*/ $this->restaurantMenuItemAddon->id,
            'restaurant_menu_item_addon' => /*new RestaurantMenuItemAddonResource($this->restaurantMenuItemAddon)*/ $this->restaurantMenuItemAddon->addon->name,
            // 'restaurant_menu_item_addon_id' => $this->restaurant_menu_item_addon_id,
            'quantity' => $this->quantity,
        ];
    }
}
