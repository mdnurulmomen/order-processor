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
        if (! $request->expectsJson()) {
            
            // $name = $request->route()->getName();
            $url = $request->url();

            if (Str::contains($url, 'admin.')) {
                return route('admin.login');
            }

            elseif (Str::contains($url, 'resto.')) {
                return route('resto.login');
            }
            
            else
                return route('login');
        }
    }
}
