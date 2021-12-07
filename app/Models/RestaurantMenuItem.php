<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItem extends Model
{
   	use SoftDeletes;

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
        'promoted' => 'boolean',
    ];

    public function restaurantMenuCategory()
    {
        return $this->belongsTo(RestaurantMenuCategory::class, 'restaurant_menu_category_id', 'id')->withTrashed();
    }

    // pivot tables doesn't follow soft delete rules
    public function variations()
    {
        return $this->belongsToMany(ItemVariation::class, 'restaurant_menu_item_variations', 'restaurant_menu_item_id', 'variation_id')->withPivot('id', 'price', 'deleted_at')->withTrashed();
    }

    // pivot tables doesn't follow soft delete rules
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'restaurant_menu_item_addons', 'restaurant_menu_item_id', 'addon_id')->withPivot('id', 'price', 'deleted_at')->withTrashed();
    }
}
