<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get all of the address's.
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id', 'id');
    }

    public function coupons()
    {
        return $this->hasMany(CustomerCoupon::class, 'customer_id', 'id');
    }

    /**
     * Get all of the order's.
     */
    public function orders()
    {
        return $this->morphMany(Order::class, 'orderer');
    }
}
