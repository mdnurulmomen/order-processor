<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchedRestaurantResource extends JsonResource
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
            // 'mobile' => $this->mobile,
            // 'address' => $this->address,
            // 'lat' => $this->lat,
            // 'lng' => $this->lng,
            // 'website' => $this->website,
            // 'is_post_paid' => $this->is_post_paid,
            // 'is_self_service' => $this->is_self_service,
            'banner_preview' => $this->banner_preview,
            'has_parking' => $this->has_parking,
            'reviews' => RestaurantReviewResource::collection($this->reviews)
        ];
    }
}
