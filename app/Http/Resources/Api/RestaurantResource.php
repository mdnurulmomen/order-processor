<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
            
            'delivery_charge_per_kilometer' => $this->delivery_charge_per_kilometer,
            'min_delivery_charge' => $this->min_delivery_charge,
            'max_delivery_charge' => $this->max_delivery_charge,

            'discount' => $this->deal->net_discount ?? 0,
            'service_schedule' => json_decode($this->service_schedule),
            'booking_break_schedule' => json_decode($this->booking_break_schedule),
            
            'booking' => new RestaurantBookingResource($this->booking),
            // 'meals' => RestaurantMealResource::collection($this->restaurantMealCategories),
            // 'cuisines' => RestaurantCuisineResource::collection($this->restaurantCuisines),
            // 'menu_categories' => RestaurantMenuCategoryResource::collection($this->restaurantMenuCategories),
            'meals' => RestaurantMealResource::collection($this->restaurantMeals),
            'cuisines' => RestaurantCuisineResource::collection($this->restaurantCuisines),
            'menu_categories' => RestaurantMenuCategoryDetailResource::collection($this->restaurantMenuCategories),
            'reviews' => /* RestaurantReviewResource::collection($this->reviews) */ route('api.v1.restaurant-reviews.show', [ 'restaurant'=>$this->id, 'perPage'=>10 ]),

        ];
    }
}
