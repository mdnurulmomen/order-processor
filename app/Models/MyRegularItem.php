<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyRegularItem extends Model
{
      protected $guarded = [
         'id'
      ];

      public $timestamps = false;

      public function variation()
   	{
   		return $this->hasOne(MyRegularItemVariation::class, 'my_regular_item_id', 'id');
   	}

   	public function addons()
   	{
   		return $this->hasMany(MyRegularItemAddon::class, 'my_regular_item_id', 'id');
   	}

   	/*
      public function customization()
   	{
   		return $this->hasOne(MyRegularItemCustomization::class, 'order_item_id', 'id');
   	}
      */

      public function restaurantMenuItem()
      {
         return $this->belongsTo(RestaurantMenuItem::class, 'restaurant_menu_item_id', 'id');
      }
}
