<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantCuisine extends Model
{
    use SoftDeletes;

   	public $timestamps = false;

   	public function cuisine()
   	{
   		return $this->belongsTo(Cuisine::class, 'cuisine_id', 'id')->withTrashed();
   	}
}
