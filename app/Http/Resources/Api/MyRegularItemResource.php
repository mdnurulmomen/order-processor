<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRegularItemResource extends JsonResource
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
            'variation'=>($this->restaurantMenuItem->has_variation & $this->variation()->exists()) ? new MyRegularItemVariationResource($this->variation) : false,
            'addons'=>($this->restaurantMenuItem->has_addon & $this->addons()->exists()) ? MyRegularItemAddonResource::collection($this->addons) : false,
        ];
    }
}
