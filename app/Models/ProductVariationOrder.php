<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariationOrder extends Model
{
    protected $guarded = [
   		'id'
   	];

   	public $timestamps = false;

   	public function merchantProductVariation()
    {
        return $this->belongsTo(MerchantProductVariation::class, 'merchant_product_variation_id', 'id');
    }
}
