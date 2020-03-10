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

		    Route::get('/api/restaurant-admins', 'RestaurantController@showAllRestaurantAdmins');
		    Route::post('/restaurant-admins', 'RestaurantController@createRestaurantAdmin');

		    Route::get('/api/restaurant-cuisines/{perPage?}', 'FoodController@showAllRestaurantCuisines');
		    Route::post('/restaurant-cuisines/{perPage?}', 'FoodController@createRestaurantCuisine');

		    Route::get('/api/menu-categories{perPage?}', 'FoodController@showAllMenuCategories');
		    Route::post('/menu-categories/{perPage?}', 'FoodController@createMenuCategory');

		    Route::get('/api/meals/{perPage?}', 'FoodController@showAllMeals');
		    Route::get('/api/meals/search/{search}/{perPage}', 'FoodController@searchAllMeals');
		    Route::post('/meals/{perPage?}', 'FoodController@createNewMeal');
		    Route::put('/meals/{meal}/{perPage}', 'FoodController@updateMeal');
		    Route::delete('/meals/{meal}/{perPage}', 'FoodController@deleteMeal');
		    Route::patch('/meals/{meal}/{perPage}', 'FoodController@restoreMeal');

		    Route::get('/api/restaurants/{perPage}', 'RestaurantController@showAllRestaurants');
		    Route::get('/api/restaurants/search/{search}/{perPage}', 'RestaurantController@searchAllRestaurants');
		    Route::post('/restaurants/{perPage}', 'RestaurantController@createNewRestaurant');
		    Route::put('/restaurants/{restaurant}/{perPage}', 'RestaurantController@updateRestaurant');
		    Route::delete('/restaurants/{restaurant}/{perPage}', 'RestaurantController@deleteRestaurant');
		    Route::patch('/restaurants/{restaurant}/{perPage}', 'RestaurantController@restoreRestaurant');
		});
	});
});

Route::fallback(function () {
    Route::view('/home', 'layouts.admin');
});