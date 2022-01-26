<?php

namespace App\Models;

use App\Models\RestaurantMenuItem;
use App\Models\RestaurantMenuCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuCategory extends Model
{
   	use SoftDeletes;
   	
   	public $timestamps = false;
   	protected $guarded = ['id'];

    protected $casts = [
      'search_preference' => 'boolean',
    ];

   	public function restaurantMenuCategories()
   	{
   		return $this->hasMany(RestaurantMenuCategory::class, 'menu_category_id', 'id');
   	}

   	/**
     * Get all of the menuItems for the menuCategory.
     */
    public function menuItems()
    {
        return $this->hasManyThrough(RestaurantMenuItem::class, RestaurantMenuCategory::class, 'menu_category_id', 'restaurant_menu_category_id', 'id', 'id');
    }
}
