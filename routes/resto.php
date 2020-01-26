<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::name('resto.')->group(function () {
    
    Route::namespace('Auth')->group(function () {
	
		Route::get('/', 'LoginController@showRestaurantLoginForm')->name('login');
		Route::post('/', 'LoginController@restaurantLogin');
		
	    Route::get('/home', 'HomeController@showRestaurantHome')->name('home');

	    // For all other routes coming from vue
	    Route::get('/{any}', 'HomeController@showAdminHome');

		Route::group(['middleware' => ['auth:restaurant']], function () {
		    
		    Route::post('/logout', 'LoginController@restaurantLogout')->name('logout');
		    
		});
	});

	Route::namespace('Web')->group(function () {

		Route::group(['middleware' => ['auth:restaurant']], function () {
		    
		    Route::get('/api/profile', 'ProfileController@showAdminProfile')->name('profile');
		    Route::get('/profile', 'ProfileController@showRestaurantProfile');
		});
	});
});

