<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
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
            'user_name' => $this->user_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'website' => $this->website,
            'banner_preview' => $this->banner_preview,
            'min_order' => $this->min_order,
            'is_post_paid' => $this->is_post_paid,
            'is_self_service' => $this->is_self_service,
            'has_parking' => $this->has_parking,
            'is_sponsored' => $this->is_sponsored,
            'is_open' => $this->is_open,

            'discount' => $this->discount ?? 0,
            'service_schedule' => json_decode($this->service_schedule),
            // 'booking_break_schedule' => json_decode($this->booking_break_schedule),
            
            // 'booking' => new MerchantBookingResource($this->booking),
            
            'meals' => MerchantMealResource::collection($this->merchantMeals),
            'cuisines' => MerchantCuisineResource::collection($this->merchantCuisines),
            'product_categories' => MerchantProductCategoryResource::collection($this->merchantProductCategories),
            'reviews' => /* MerchantReviewResource::collection($this->reviews) */ route('api.v1.merchant-reviews.show', [ 'merchant'=>$this->id, 'perPage'=>10 ]),
        ];
    }
}
