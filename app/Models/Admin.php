<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Intervention\Image\ImageManagerStatic as ImageIntervention;

class Admin extends Authenticatable
{
   	use Notifiable;

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

    public function setProfilePictureAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/admin/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.$this->id.'.jpg');

            $this->attributes['profile_picture'] = $directory.$this->id.'.jpg';
        }
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
