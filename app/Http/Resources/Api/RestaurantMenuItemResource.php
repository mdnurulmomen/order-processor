<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resources\Api\RestaurantMenuCategoryDetailResource;

class RestaurantMenuItemResource extends JsonResource
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

   //          'name' => $this->name,
   //          'mobile' => $this->mobile,
   //          'address' => $this->address,
   //          'lat' => $this->lat,
   //          'lng' => $this->lng,
   //          'website' => $this->website,
   //          'banner_preview' => $this->banner_preview,
   //          'min_order' => $this->min_order,
   //          'is_post_paid' => $this->is_post_paid,
   //          'is_self_service' => $this->is_self_service,
   //          'has_parking' => $this->has_parking,
   //          'discount' => $this->deal->net_discount,
   //          'service_schedule' => json_decode($this->service_schedule),
   //          'booking_break_schedule' => json_decode($this->booking_break_schedule),
            // 'meals' => RestaurantMealResource::collection($this->restaurantMealCategories),
            // 'cuisines' => RestaurantCuisineResource::collection($this->restaurantCuisines),
            // 'menu_categories' => RestaurantMenuCategoryResource::collection($this->restaurantMenuCategories),
            
            // 'menu_categories' => RestaurantMenuCategoryDetailResource::collection($this->menuCategories),
            
            'id' => $this->id,
            'name' => $this->name,
            'detail' => $this->detail,
            'has_addon' => $this->has_addon,
            'has_variation' => $this->has_variation,
            // 'variations' => $this->has_variation ? MenuItemVariationResource::collection($this->restaurantMenuItemVariations) : false,
            // 'addons' => $this->has_addon ? MenuItemAddonResource::collection($this->restaurantMenuItemAddons) : false,
            // 'price' => $this->price,
            'customizable' => $this->customizable,
            // 'item_stock' => $this->item_stock,
            
        ];
    }
}
