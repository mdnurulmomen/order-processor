<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resources\Api\RestaurantMenuCategoryDetailResource;

class RestaurantMenuItemResource extends JsonResource
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
            
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'detail' => $this->detail,
            'has_addon' => $this->has_addon,
            'has_variation' => $this->has_variation,
            'customizable' => $this->customizable,
            // 'variations' => $this->has_variation ? MenuItemVariationResource::collection($this->restaurantMenuItemVariations) : false,
            // 'addons' => $this->has_addon ? MenuItemAddonResource::collection($this->restaurantMenuItemAddons) : false,
            // 'item_stock' => $this->item_stock,
            
        ];
    }
}
