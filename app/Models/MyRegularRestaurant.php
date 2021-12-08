<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyRegularRestaurant extends Model
{
      protected $guarded = [
 		   'id'
      ];

      public $timestamps = false;

   	public function menuItems()
   	{
      return $this->hasMany(MyRegularItem::class, 'my_regular_restaurant_id', 'id');
   	}

   	public function myRegular()
   	{
      return $this->belongsTo(MyRegular::class, 'my_regular_id', 'id');
   	}

   	public function restaurant()
   	{
      return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
   	}
}
