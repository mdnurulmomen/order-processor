<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantProduct extends Model
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

    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'has_variation' => 'boolean',
        'has_addon' => 'boolean',
        'customizable' => 'boolean',
        'available' => 'boolean',
        'promoted' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(ProductOrder::class, 'merchant_product_id', 'id');
    }

    public function merchantProductVariations()
    {
        return $this->hasMany(MerchantProductVariation::class, 'merchant_product_id', 'id');
    }

    public function merchantProductAddons()
    {
        return $this->hasMany(MerchantProductAddon::class, 'merchant_product_id', 'id');
    }

    public function merchantProductCategory()
    {
        return $this->belongsTo(MerchantProductCategory::class, 'merchant_product_category_id', 'id')->withTrashed();
    }

    // pivot tables doesn't follow soft delete rules
    public function variations()
    {
        return $this->belongsToMany(Variation::class, 'merchant_product_variations', 'merchant_product_id', 'variation_id')->withPivot('id', 'price', 'deleted_at')->withTrashed();
    }

    // pivot tables doesn't follow soft delete rules
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'merchant_product_addons', 'merchant_product_id', 'addon_id')->withPivot('id', 'price', 'deleted_at')->withTrashed();
    }
}
