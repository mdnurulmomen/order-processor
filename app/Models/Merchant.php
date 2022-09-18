<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Intervention\Image\ImageManagerStatic as ImageIntervention;

class Merchant extends Model
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
        'break_schedule' => 'json',
        'taking_order' => 'boolean',
        'admin_approval' => 'boolean',
        'sponsored' => 'boolean',
    ];

    public function deal()
    {
        return $this->hasOne(MerchantDeal::class, 'merchant_id', 'id');
    }

    public function kitchen()
    {
        return $this->hasOne(Kitchen::class, 'merchant_id', 'id');
    }

    public function booking()
    {
        return $this->hasOne(MerchantBookingStatus::class, 'merchant_id', 'id');
    }

    public function agents()    // waiters
    {
        return $this->hasMany(MerchantAgent::class, 'merchant_id', 'id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'merchant_id', 'id');
    }

    public function merchantProductCategories()
    {
        return $this->hasMany(MerchantProductCategory::class, 'merchant_id', 'id');
    }

    public function merchantCuisines()
    {
        return $this->hasMany(MerchantCuisine::class, 'merchant_id', 'id');
    }

    public function merchantMeals()
    {
        return $this->hasMany(MerchantMeal::class, 'merchant_id', 'id');
    }

    // when pivot table doesnn't follow soft-deleted or not soft-deleted
    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'merchant_meals', 'merchant_id', 'meal_id');
    }

    public function cuisines()
    {
        return $this->belongsToMany(Cuisine::class, 'merchant_cuisines', 'merchant_id', 'cuisine_id');
    }

    public function productCategories()
    {
        return $this->belongsToMany(ProductCategory::class, 'merchant_product_categories', 'merchant_id', 'product_category_id')->withPivot('id', 'deleted_at', 'serving_from', 'serving_to');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function owner()
    {
        return $this->belongsTo(MerchantOwner::class, 'merchant_owner_id', 'id');
    }
   
    public function setBannerPreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/merchant/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.$this->id.'.jpg');

            $this->attributes['banner_preview'] = $directory.$this->id.'.jpg';
        }
    }
}
