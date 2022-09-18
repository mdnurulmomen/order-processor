<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantDeal extends Model
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
        'delivery_fee_addition' => 'boolean',
    ];

    public function merchant()
    {
    	return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
}
