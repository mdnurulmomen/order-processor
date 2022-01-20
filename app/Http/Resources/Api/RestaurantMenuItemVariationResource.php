<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantMenuItemVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $discount = $this->restaurantMenuItem->restaurantMenuCategory->restaurant->deal->net_discount ?? 0;

        return [

            'id' => $this->id,
            'name' => $this->variation->name,
            'price' => $this->price,
            'discount_price' => round($this->price - ($this->price * $discount/100)),
            // 'variation' => $this->variation,
        ];
    }
}
