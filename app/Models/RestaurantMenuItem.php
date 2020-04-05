<?php

namespace App\Models;

use App\Models\RestaurantMenuCategory;
use Illuminate\Database\Eloquent\Model;

class RestaurantMenuItem extends Model
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'has_variation' => 'boolean',
        'has_addon' => 'boolean',
        'customizable' => 'boolean',
        'item_stock' => 'boolean',
    ];

    public function restaurantMenuCategory()
    {
        return $this->belongsTo(RestaurantMenuCategory::class, 'restaurant_menu_category_id', 'id');
    }
}
