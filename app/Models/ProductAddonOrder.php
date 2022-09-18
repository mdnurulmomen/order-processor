<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAddonOrder extends Model
{
    protected $guarded = [
   		'id'
   	];

   	public $timestamps = false;

   	public function merchantProductAddon()
    {
        return $this->belongsTo(MerchantProductAddon::class, 'merchant_product_addon_id', 'id');
    }
}
