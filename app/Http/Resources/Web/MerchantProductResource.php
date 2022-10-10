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
        $discount = $this->merchantProductCategory->merchant->deal->net_discount ?? 0;

        return [
            
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'detail' => $this->detail,
            'has_addon' => $this->has_addon,
            'is_promoted' => $this->is_promoted,
            'is_sponsored' => $this->is_sponsored,
            'customizable' => $this->customizable,
            'has_variation' => $this->has_variation,

            'discount_price' => round($this->price - ($this->price * $discount/100)),
            
            'addons' => MerchantProductAddonResource::collection($this->whenLoaded('merchantProductAddons')),
            'variations' => MerchantProductVariationResource::collection($this->whenLoaded('merchantProductVariations')),

            'available' => $this->available,
            
            // 'itemVariations' => $this->has_variation ? MenuItemVariationResource::collection($this->restaurantMenuItemVariations) : false,
            // 'itemAddons' => $this->has_addon ? MenuItemAddonResource::collection($this->restaurantMenuItemAddons) : false,
            // 'item_stock' => $this->item_stock,
        ];
    }
}
