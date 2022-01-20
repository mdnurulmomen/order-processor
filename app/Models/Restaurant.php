<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Intervention\Image\ImageManagerStatic as ImageIntervention;

class Restaurant extends Model
{
   	use SoftDeletes;

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
        'is_post_paid' => 'boolean',
        'is_self_service' => 'boolean',
        'has_parking' => 'boolean',
        'service_schedule' => 'json',
        'booking_break_schedule' => 'json',
        'taking_order' => 'boolean',
        'admin_approval' => 'boolean',
        'sponsored' => 'boolean',
    ];
    
    public function admin()
    {
        return $this->belongsTo(RestaurantAdmin::class, 'restaurant_admins_id', 'id');
    }

    public function kitchen()
    {
        return $this->hasOne(Kitchen::class, 'restaurant_id', 'id');
    }

    public function waiters()
    {
        return $this->hasMany(Waiter::class, 'restaurant_id', 'id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function deal()
    {
        return $this->hasOne(RestaurantDeal::class, 'restaurant_id', 'id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'restaurant_id', 'id');
    }

    public function booking()
    {
        return $this->hasOne(RestaurantBookingStatus::class, 'restaurant_id', 'id');
    }

    public function restaurantMenuCategories()
    {
        return $this->hasMany(RestaurantMenuCategory::class, 'restaurant_id', 'id');
    }

    public function restaurantCuisines()
    {
        return $this->hasMany(RestaurantCuisine::class, 'restaurant_id', 'id');
    }

    public function restaurantMeals()
    {
        return $this->hasMany(RestaurantMeal::class, 'restaurant_id', 'id');
    }

    // when pivot table doesnn't follow soft-deleted or not soft-deleted
    public function cuisines()
    {
        return $this->belongsToMany(Cuisine::class, 'restaurant_cuisines', 'restaurant_id', 'cuisine_id');
    }

    public function menuCategories()
    {
        return $this->belongsToMany(MenuCategory::class, 'restaurant_menu_categories', 'restaurant_id', 'menu_category_id')->withPivot('id', 'serving_from', 'serving_to');
    }

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'restaurant_meals', 'restaurant_id', 'meal_id');
    }
   
    public function setBannerPreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/restaurant/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.$this->id.'.jpg');

            $this->attributes['banner_preview'] = $directory.$this->id.'.jpg';
        }
    }

}
