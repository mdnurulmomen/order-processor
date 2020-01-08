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

	Route::group(['as' => 'admin.'], function () {
	    
	    Route::namespace('Auth')->group(function () {
		
			Route::get('/', 'LoginController@showAdminLoginForm')->name('login');
			Route::post('/', 'LoginController@submitAdminLoginForm');

			Route::group(['middleware' => ['auth:admin']], function () {
			    
			    Route::get('/home', 'HomeController@showAdminHome')->name('home');
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

	Route::group(['as' => 'resto.'], function () {
	    
	    Route::namespace('Auth')->group(function () {
		
			Route::get('/', 'LoginController@showRestaurantLoginForm')->name('login');
			Route::post('/', 'LoginController@submitRestaurantLoginForm');

			Route::group(['middleware' => ['auth:restaurant']], function () {
			    
			    Route::get('/home', 'HomeController@showRestaurantHome')->name('home');
			});
		});

		Route::namespace('Web')->group(function () {

			Route::group(['middleware' => ['auth:restaurant']], function () {
			    
			    Route::get('/profile', 'ProfileController@showRestaurantProfile')->name('profile');
			});
		});
	});
});


Route::view('/', 'welcome')->name('home');
Route::get('/home', 'Auth\HomeController@index')->name('customer.home');
Route::fallback(function () {
    Route::view('/', 'welcome')->name('customer.home');
});


