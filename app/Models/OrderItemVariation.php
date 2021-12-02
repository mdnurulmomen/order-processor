<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemVariation extends Model
{
    protected $guarded = [
   		'id'
   	];

   	public $timestamps = false;

   	public function restaurantMenuItemVariation()
    {
        return $this->belongsTo(RestaurantMenuItemVariation::class, 'restaurant_menu_item_variation_id', 'id');
    }
}
