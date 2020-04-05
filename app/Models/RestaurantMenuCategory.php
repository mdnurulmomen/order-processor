<?php

namespace App\Models;

use App\Models\MenuCategory;
use App\Models\RestaurantMenuItem;
use Illuminate\Database\Eloquent\Model;

class RestaurantMenuCategory extends Model
{
   	public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

   	public function menuCategory()
   	{
   		return $this->belongsTo(MenuCategory::class, 'menu_category_id', 'id');
   	}

   	public function restaurantMenuItems()
   	{
   		return $this->hasMany(RestaurantMenuItem::class, 'restaurant_menu_category_id', 'id');
   	}
}
