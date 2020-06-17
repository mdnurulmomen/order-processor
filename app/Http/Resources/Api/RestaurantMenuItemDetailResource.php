<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\MenuItemAddonResource;
use App\Http\Resources\Api\MenuItemVariationResource;


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

        return [

            'id' => $this->id,
            'name' => $this->name,
            'detail' => $this->detail,
            'itemVariations' => $this->has_variation ? MenuItemVariationResource::collection($this->restaurantMenuItemVariations) : false,
            'itemAddons' => $this->has_addon ? MenuItemAddonResource::collection($this->restaurantMenuItemAddons) : false,
            'price' => $this->price,
            'customizable' => $this->customizable,
            'item_stock' => $this->item_stock,

        ];
    }
}
