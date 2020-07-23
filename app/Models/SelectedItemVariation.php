<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantMenuItemVariation;

class SelectedItemVariation extends Model
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
