<?php

namespace App\Models;

use App\Models\OrderItem;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Model;

class OrderedRestaurant extends Model
{
	protected $guarded = [
 		'id'
	];

   public function items()
   {
      return $this->hasMany(OrderItem::class, 'ordered_restaurant_id', 'id');
   }

   public function restaurant()
   {
      return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
   }
}
