<?php

namespace App\Http\Middleware;

use Illuminate\Support\Str;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {        // for non-json request
            
            $name = \Route::currentRouteName();

            if (Str::contains($name, 'admin.')) {
                return route('admin.login');
            }

            elseif (Str::contains($name, 'owner.')) {
                return route('owner.login');
            }

            elseif (Str::contains($name, 'merchant.')) {
                return route('merchant.login');
            }
            
            else
                return route('login');
            
        }
    }
}
