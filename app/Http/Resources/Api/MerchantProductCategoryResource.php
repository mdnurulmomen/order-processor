<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProductCategoryResource extends JsonResource
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

            'product_category_id' => $this->product_category_id,
            'serving_from' => $this->serving_from,
            'serving_to' => $this->serving_to,
            'product_category_name' => $this->productCategory->name,
            'products' => MerchantProductResource::collection($this->whenLoaded('products')),

        ];
    }
}
