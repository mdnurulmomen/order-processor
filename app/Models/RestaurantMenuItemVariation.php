<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItemVariation extends Model
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


   	public function variation()
    {
        return $this->belongsTo(ItemVariation::class, 'variation_id', 'id')->withTrashed();
    }
}
