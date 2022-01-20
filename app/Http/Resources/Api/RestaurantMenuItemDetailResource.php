<?php

namespace App\Http\Resources\Api;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Resources\Json\JsonResource;


class RestaurantMenuItemDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        $discount = $this->restaurantMenuCategory->restaurant->deal->net_discount ?? 0;

        return [

            'id' => $this->id,
            'name' => $this->name,
            'detail' => $this->detail,
            'price' => $this->price,
            'discount_price' => round($this->price - ($this->price * $discount/100)),
            'has_addon' => $this->has_addon,
            'has_variation' => $this->has_variation,
            'addons' => $this->has_addon ? RestaurantMenuItemAddonResource::collection($this->restaurantMenuItemAddons) : [],
            'variations' => $this->has_variation ? RestaurantMenuItemVariationResource::collection($this->restaurantMenuItemVariations) : [],
            'customizable' => $this->customizable,
            'item_stock' => $this->item_stock,
            'restaurant_id' => $this->when(strpos(Route::currentRouteName(), 'promotional-menu-items.index') != false, $this->restaurantMenuCategory->restaurant_id),
            'restaurant_name' => $this->when(strpos(Route::currentRouteName(), 'promotional-menu-items.index') != false, $this->restaurantMenuCategory->restaurant->name)

        ];
    }
}
