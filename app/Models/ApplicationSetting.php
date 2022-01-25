<?php

namespace App\Models;

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

            $directory = "uploads/application/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.'logo.png');

            $this->attributes['logo'] = $directory.'logo.png';
        }
    }

    public function setFaviconIconAttribute($encodedImageFile)
    {
        if ($encodedImageFile) {

            $directory = "uploads/application/";

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }
            
            $imageObject = ImageIntervention::make($encodedImageFile);
            $imageObject->save($directory.'favicon.png');

            $this->attributes['favicon'] = $directory.'favicon.png';
        }
    }

    public function setWelcomeGreetingsAttribute($greetingArray)
    {
        if (count($greetingArray)) {
            
            WelcomeGreeting::truncate();

            foreach ($greetingArray as $greetingKey => $greeting) {
                
                $welcomeNewGreeting = new WelcomeGreeting();

                $welcomeNewGreeting->title = $greeting->title;

                if ($greeting->preview) {
                    
                    $directory = "uploads/application/welcome/";

                    if (!file_exists($directory)) {
                        mkdir($directory, 0777, true);
                    }
                    
                    $imageObject = ImageIntervention::make($greeting->preview);
                    $imageObject->save($directory.'welcome-greeting-'.$greetingKey.'.png');

                    $welcomeNewGreeting->preview = $directory.'welcome-greeting-'.$greetingKey.'.png';
                }

                $welcomeNewGreeting->paragraph = $greeting->paragraph;
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

            $thanksNewGreeting->title = $accomplishmentGreeting->title;

            if ($accomplishmentGreeting->preview) {
                
                $directory = "uploads/application/thanksgiving/";

                if (!file_exists($directory)) {
                    mkdir($directory, 0777, true);
                }
                
                $imageObject = ImageIntervention::make($accomplishmentGreeting->preview);
                $imageObject->save($directory.'thanks-greeting.png');

                $thanksNewGreeting->preview = $directory.'thanks-greeting.png';
            }

            $thanksNewGreeting->paragraph = $accomplishmentGreeting->paragraph;
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

                    if (!file_exists($directory)) {
                        mkdir($directory, 0777, true);
                    }
                    
                    $imageObject = ImageIntervention::make($promotional->preview);
                    $imageObject->save($directory.'promotional-'.$promotionalKey.'.png');

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

                $paymentNewMethod->name = $paymentMethod->name;

                if ($paymentMethod->logo) {
                    
                    $directory = "uploads/application/payments/";

                    if (!file_exists($directory)) {
                        mkdir($directory, 0777, true);
                    }
                    
                    $imageObject = ImageIntervention::make($paymentMethod->logo);
                    $imageObject->save($directory.'payment-method-'.$paymentMethodKey.'.png');

                    $paymentNewMethod->logo = $directory.'payment-method-'.$paymentMethodKey.'.png';
                }

                $paymentNewMethod->status = $paymentMethod->status ?? false;

                $paymentNewMethod->save();

            }

        }
    }
}
