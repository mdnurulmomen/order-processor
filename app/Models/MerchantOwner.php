<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MerchantOwner extends Authenticatable
{
    use Notifiable, SoftDeletes;
    
   	protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function merchants()
    {
        return $this->hasMany(Merchant::class, 'merchant_owner_id', 'id')->withTrashed();
    }
}
