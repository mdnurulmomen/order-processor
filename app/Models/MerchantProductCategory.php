<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantProductCategory extends Model
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

    public function merchantProducts()
    {
      return $this->hasMany(MerchantProduct::class, 'merchant_product_category_id', 'id');
    }

   	public function merchant()
    {
      return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function productCategory()
   	{
   		return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id');
   	}
}
