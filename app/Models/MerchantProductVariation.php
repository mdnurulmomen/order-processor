<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantProductVariation extends Model
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

    public function orders()
    {
        return $this->hasMany(ProductVariationOrder::class, 'merchant_product_variation_id', 'id');
    }

    public function merchantProduct()
    {
        return $this->belongsTo(MerchantProduct::class, 'merchant_product_id', 'id')->withTrashed();
    }

   	public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id', 'id')->withTrashed();
    }
}
