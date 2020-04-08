<?php

namespace App\Models;

use App\Models\RestaurantDeal;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{  	
   	protected $guarded = ['id'];
   	public $timestamps = false;

   	public function restaurantDeals()
   	{
   		return $this->hasMany(RestaurantDeal::class, 'discount_id', 'id');
   	}
}
