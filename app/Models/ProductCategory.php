<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes;
   	
   	public $timestamps = false;
   	protected $guarded = ['id'];

    protected $casts = [
      'search_preference' => 'boolean',
    ];

   	public function merchantProductCategories()
   	{
   		return $this->hasMany(MerchantProductCategory::class, 'product_category_id', 'id');
   	}

    public function merchantProducts()
    {
        return $this->hasManyThrough(MerchantProduct::class, MerchantProductCategory::class, 'product_category_id', 'merchant_product_category_id', 'id', 'id');
    }
}
