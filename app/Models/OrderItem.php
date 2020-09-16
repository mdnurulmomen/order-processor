<?php

namespace App\Models;

use App\Models\RestaurantMenuItem;
use App\Models\SelectedItemVariation;
use App\Models\AdditionalOrderedAddon;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderedItemCustomization;

class OrderItem extends Model
{
   	protected $guarded = [
   		'id'
   	];

      public $timestamps = false;

   	public function selectedItemVariation()
   	{
   		return $this->hasOne(SelectedItemVariation::class, 'order_item_id', 'id');
   	}

   	public function additionalOrderedAddons()
   	{
   		return $this->hasMany(AdditionalOrderedAddon::class, 'order_item_id', 'id');
   	}

   	public function orderedItemCustomization()
   	{
   		return $this->hasOne(OrderedItemCustomization::class, 'order_item_id', 'id');
   	}

      public function restaurantMenuItem()
      {
         return $this->belongsTo(RestaurantMenuItem::class, 'restaurant_menu_item_id', 'id')->withTrashed();
      }
}
