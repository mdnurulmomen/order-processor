<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
   	protected $guarded = [
   		'id'
   	];

      public $timestamps = false;

   	public function variation()
   	{
   		return $this->hasOne(OrderItemVariation::class, 'order_item_id', 'id');
   	}

   	public function addons()
   	{
   		return $this->hasMany(OrderItemAddon::class, 'order_item_id', 'id');
   	}

   	public function customization()
   	{
   		return $this->hasOne(OrderItemCustomization::class, 'order_item_id', 'id');
   	}

      public function restaurantMenuItem()
      {
         return $this->belongsTo(RestaurantMenuItem::class, 'restaurant_menu_item_id', 'id')->withTrashed();
      }
}
