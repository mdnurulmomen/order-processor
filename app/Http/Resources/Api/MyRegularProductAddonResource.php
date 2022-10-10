<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MyRegularProductAddonResource extends JsonResource
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
            'merchant_product_addon_id' => $this->merchantProductAddon->id,
            'merchant_product_addon' => $this->merchantProductAddon->addon->name,
            // 'merchant_product_addon_id' => $this->merchant_product_addon_id,
            'quantity' => $this->quantity,
        ]
    }
}
