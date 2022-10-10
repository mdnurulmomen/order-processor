<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\ImageManagerStatic as ImageIntervention;

class ApplicationService extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id' 
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public $timestamps = false;

    public function setLogoIconAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/application/services/";

            if(!File::isDirectory($directory)){
                File::makeDirectory($directory, 0777, true, true);
            }

            try 
            {
                $img = ImageIntervention::make($encodedImageFile);
            }
            catch(NotReadableException $e)
            {
                // If error, stop and return
                return;
            }

            // $imageObject = $img->resize(300, 300);  // when facebook uses 180*180
            $img->save($directory.$this->code.'-logo.png', 100);

            $this->attributes['logo'] = $directory.$this->code.'-logo.png';
        }
    }
}
