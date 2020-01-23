<?php

namespace App\Models;

use App\Traits\Uploadable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
   	use Notifiable, Uploadable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id' 
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
    **/
    
    /* 
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    */
   
    public function setProfilePictureAttribute(UploadedFile $uploadedFile=null)
    {
        if ($uploadedFile) {
            
            $this->attributes['profile_picture'] = $this->uploadImage($uploadedFile, $this->id, 'uploads/admin');
        }
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
