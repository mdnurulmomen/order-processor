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

Auth::routes();

Route::domain('admin.localhost')->group(function () {

	Route::name('admin.')->group(function () {
	    
	    Route::namespace('Auth')->group(function () {
		
			Route::get('/', 'LoginController@showAdminLoginForm')->name('login');
			Route::post('/', 'LoginController@adminLogin');
		    Route::get('/home', 'HomeController@showAdminHome')->name('home');

			Route::group(['middleware' => ['auth:admin']], function () {
			    
			    Route::post('/logout', 'LoginController@adminLogout')->name('logout');
			    
			});
		});

		Route::namespace('Web')->group(function () {

			Route::group(['middleware' => ['auth:admin']], function () {
			    
			    Route::get('/profile', 'ProfileController@showAdminProfile')->name('profile');
			});
		});
	});
});

Route::domain('resto.localhost')->group(function () {

	Route::name('resto.')->group(function () {
	    
	    Route::namespace('Auth')->group(function () {
		
			Route::get('/', 'LoginController@showRestaurantLoginForm')->name('login');
			Route::post('/', 'LoginController@restaurantLogin');
		    Route::get('/home', 'HomeController@showRestaurantHome')->name('home');

			Route::group(['middleware' => ['auth:restaurant']], function () {
			    
			    Route::post('/logout', 'LoginController@restaurantLogout')->name('logout');
			    
			});
		});

		Route::namespace('Web')->group(function () {

			Route::group(['middleware' => ['auth:restaurant']], function () {
			    
			    Route::get('/profile', 'ProfileController@showRestaurantProfile')->name('profile');
			});
		});
	});
});

Route::view('/', 'welcome')->name('website');

Route::get('/home', 'Auth\HomeController@index')->name('customer.home');

Route::fallback(function () {
    Route::view('/', 'welcome');
});


