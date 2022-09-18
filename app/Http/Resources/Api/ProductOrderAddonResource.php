<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductOrderAddonResource extends JsonResource
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
            'merchant_product_addon' => /*new RestaurantMenuItemAddonResource($this->merchantProductAddon)*/ $this->merchantProductAddon->addon->name,
            'merchant_product_addon_id' => $this->merchant_product_addon_id,
            'quantity' => $this->quantity,
        ];
    }
}
