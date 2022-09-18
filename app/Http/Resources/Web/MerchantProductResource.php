<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProductResource extends JsonResource
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
            'customizable' => $this->customizable,
            'has_variation' => $this->has_variation,
            // 'itemVariations' => $this->has_variation ? MenuItemVariationResource::collection($this->restaurantMenuItemVariations) : false,
            // 'itemAddons' => $this->has_addon ? MenuItemAddonResource::collection($this->restaurantMenuItemAddons) : false,
            // 'item_stock' => $this->item_stock,
        ];
    }
}
