<?php

namespace App\Models;

use App\Models\RiderEvaluation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Intervention\Image\ImageManagerStatic as ImageIntervention;

class Rider extends Model
{
   	use SoftDeletes;
    
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_engaged' => 'boolean',
        'is_approved' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function orders()            // all order-requests
    {
      return $this->hasMany(RiderDelivery::class, 'rider_id', 'id');
    }

    public function evaluation() 
    {
        return $this->hasOne(RiderEvaluation::class, 'rider_id', 'id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function setProfilePictureAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/rider/profile/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.$this->user_name.'.jpg');

            $this->attributes['profile_pic_preview'] = $directory.$this->user_name.'.jpg';
        }
    }

    public function setNidFrontPreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/delivery_man/national_id/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.$this->user_name.'_front_preview.jpg');

            $this->attributes['nid_front_preview'] = $directory.$this->user_name.'_front_preview.jpg';
        }
    }

    public function setNidBackPreviewAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/delivery_man/national_id/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.$this->user_name.'_back_preview.jpg');

            $this->attributes['nid_back_preview'] = $directory.$this->user_name.'_back_preview.jpg';
        }
    }
}
