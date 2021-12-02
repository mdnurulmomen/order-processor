<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemAddon extends Model
{
    protected $guarded = [
   		'id'
   	];

   	public $timestamps = false;

   	public function restaurantMenuItemAddon()
    {
        return $this->belongsTo(RestaurantMenuItemAddon::class, 'restaurant_menu_item_addon_id', 'id');
    }
}
