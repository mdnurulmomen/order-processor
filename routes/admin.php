<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

Route::name('admin.')->group(function () {
    
    Route::namespace('Auth')->group(function () {
	
		Route::get('/', 'LoginController@showAdminLoginForm')->name('login');
		Route::post('/', 'LoginController@adminLogin');
	    
	    Route::get('/home', 'HomeController@showAdminHome')->name('home');
	    
	    // For all other routes coming from vue
    	Route::get('/{any}', 'HomeController@showAdminHome');

		Route::group(['middleware' => ['auth:admin']], function () {
		    
		    Route::post('/logout', 'LoginController@adminLogout')->name('logout');
		});
	});


	Route::namespace('Web')->group(function () {

		Route::group(['middleware' => ['auth:admin']], function () {
		    
		    Route::get('/api/profile', 'ProfileController@showAdminProfile');
		    Route::post('/profile', 'ProfileController@updateAdminProfile');
		    Route::post('/password', 'PasswordController@updateAdminPassword');
		});
	});
});