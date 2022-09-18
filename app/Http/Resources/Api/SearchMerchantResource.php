<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchMerchantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $reviews = $this->reviews;
        $number_reviews = count($this->reviews);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'banner_preview' => $this->banner_preview,
            'has_parking' => $this->has_parking,
            
            // 'mobile' => $this->mobile,
            // 'address' => $this->address,
            // 'website' => $this->website,
            // 'is_post_paid' => $this->is_post_paid,
            // 'is_self_service' => $this->is_self_service,
            
            'lat' => $this->lat,
            'lng' => $this->lng, 

            'delivery_charge_per_kilometer' => $this->delivery_charge_per_kilometer,
            'min_delivery_charge' => $this->min_delivery_charge,
            'max_delivery_charge' => $this->max_delivery_charge,
            
            'cuisines' => RestaurantCuisineResource::collection($this->restaurantCuisines),

            'total_reviews' => count($reviews),
            'mean_review' => $number_reviews ? ($reviews->sum('rating') / $number_reviews) : 0,
            'reviews' => route('api.v1.merchant-reviews.show', [ 'merchant'=>$this->id, 'perPage'=>10 ]),

            "url" => route('api.v1.merchants.show', [ 'merchant'=>$this->id ])
        ];
    }
}
