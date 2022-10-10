<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as ImageIntervention;

class ApplicationSetting extends Model
{
   	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id' 
    ];

    public function setLogoIconAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/system/";

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

            $img = $img->resize(300, 300);  // when facebook uses 180*180
            $img->save($directory.'logo.png');

            $this->attributes['logo'] = $directory.'logo.png';
        }
    }

    public function setFaviconIconAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/system/";

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

            $img = $img->resize(300, 300);  // when facebook uses 180*180
            $img->save($directory.'favicon.png');

            $this->attributes['favicon'] = $directory.'favicon.png';
            
        }
    }

    public function setWelcomeGreetingsAttribute($greetingArray)
    {
        if (count($greetingArray)) {
            
            WelcomeGreeting::truncate();

            foreach ($greetingArray as $greetingKey => $greeting) {
                
                $welcomeNewGreeting = new WelcomeGreeting();

                $welcomeNewGreeting->title = strtolower($greeting->title);

                if ($greeting->preview) {
                    
                    $directory = "uploads/application/welcome/";

                    if(!File::isDirectory($directory)){
		                File::makeDirectory($directory, 0777, true, true);
		            }

		            try 
		            {
		                $img = ImageIntervention::make($greeting->preview);
		            }
		            catch(NotReadableException $e)
		            {
		                // If error, stop and return
		                return;
		            }

		            // $img = $img->resize(300, 300);  // when facebook uses 180*180
		            $img->save($directory.'welcome-greeting-'.$greetingKey.'.png');

		            $welcomeNewGreeting->preview = $directory.'welcome-greeting-'.$greetingKey.'.png';
                    
                }

                $welcomeNewGreeting->paragraph = strtolower($greeting->paragraph);
                $welcomeNewGreeting->status = $greeting->status ?? false;

                $welcomeNewGreeting->save();

            }

        }
    }

    public function setThanksGreetingAttribute($accomplishmentGreeting)
    {
        if ($accomplishmentGreeting) {
            
            ThanksGreeting::truncate();
    
            $thanksNewGreeting = new ThanksGreeting();

            $thanksNewGreeting->title = strtolower($accomplishmentGreeting->title);

            if ($accomplishmentGreeting->preview) {

	            $directory = "uploads/application/thanksgiving/";

	            if(!File::isDirectory($directory)){
	                File::makeDirectory($directory, 0777, true, true);
	            }

	            try 
	            {
	                $img = ImageIntervention::make($accomplishmentGreeting->preview);
	            }
	            catch(NotReadableException $e)
	            {
	                // If error, stop and return
	                return;
	            }

	            // $img = $img->resize(300, 300);  // when facebook uses 180*180
	            $img->save($directory.'thanks-greeting.png');

	            $thanksNewGreeting->preview = $directory.'thanks-greeting.png';
	            
	        }

            $thanksNewGreeting->paragraph = strtolower($accomplishmentGreeting->paragraph);
            // $thanksNewGreeting->status = $accomplishmentGreeting->status ?? true;

            $thanksNewGreeting->save();

        }
    }

    public function setPromotionalSlidersAttribute($promotionalArray)
    {
        if (count($promotionalArray)) {
            
            PromotionalSlider::truncate();

            foreach ($promotionalArray as $promotionalKey => $promotional) {
                
                $promotionalNewSlider = new PromotionalSlider();

                if ($promotional->preview) {

		            $directory = "uploads/application/promotional/";

		            if(!File::isDirectory($directory)){
		                File::makeDirectory($directory, 0777, true, true);
		            }

		            try 
		            {
		                $img = ImageIntervention::make($promotional->preview);
		            }
		            catch(NotReadableException $e)
		            {
		                // If error, stop and return
		                return;
		            }

		            // $img = $img->resize(300, 300);  // when facebook uses 180*180
		            $img->save($directory.'promotional-'.$promotionalKey.'.png');

		            $promotionalNewSlider->preview = $directory.'promotional-'.$promotionalKey.'.png';
		            
		        }

                $promotionalNewSlider->status = $promotional->status ?? false;

                $promotionalNewSlider->save();

            }

        }
    }

    public function setPaymentMethodsAttribute($paymentMethodArray)
    {
        if (count($paymentMethodArray)) {
            
            ApplicationPaymentMethod::truncate();

            foreach ($paymentMethodArray as $paymentMethodKey => $paymentMethod) {
                
                $paymentNewMethod = new ApplicationPaymentMethod();

                $paymentNewMethod->name = strtolower($paymentMethod->name);

                if ($paymentMethod->logo) {

		            $directory = "uploads/application/payments/";

		            if(!File::isDirectory($directory)){
		                File::makeDirectory($directory, 0777, true, true);
		            }

		            try 
		            {
		                $img = ImageIntervention::make($paymentMethod->logo);
		            }
		            catch(NotReadableException $e)
		            {
		                // If error, stop and return
		                return;
		            }

		            // $img = $img->resize(300, 300);  // when facebook uses 180*180
		            $img->save($directory.'payment-method-'.$paymentMethodKey.'.png');

		            $paymentNewMethod->logo = $directory.'payment-method-'.$paymentMethodKey.'.png';
		            
		        }

                $paymentNewMethod->status = $paymentMethod->status ?? false;

                $paymentNewMethod->save();

            }

        }
    }
}
