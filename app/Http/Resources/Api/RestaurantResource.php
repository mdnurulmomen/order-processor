<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\RestaurantMealResource;
use App\Http\Resources\Api\RestaurantCuisineResource;
use App\Http\Resources\Api\RestaurantMenuCategoryResource;


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
            
            'booking' => new RestaurantBookingResource($this->booking),
            'meals' => RestaurantMealResource::collection($this->restaurantMealCategories),
            'cuisines' => RestaurantCuisineResource::collection($this->restaurantCuisines),
            'menu_categories' => RestaurantMenuCategoryResource::collection($this->restaurantMenuCategories),
            
            'min_order' => $this->min_order,
            'is_post_paid' => $this->is_post_paid,
            'is_self_service' => $this->is_self_service,
            'has_parking' => $this->has_parking,
			'discount' => $this->deal->net_discount,
            'service_schedule' => json_decode($this->service_schedule),
            'booking_break_schedule' => json_decode($this->booking_break_schedule),

        ];
    }
}
