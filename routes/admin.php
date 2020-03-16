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


		    Route::get('/api/discounts/{perPage?}', 'RestaurantController@showAllDiscounts');
		    Route::get('/api/discounts/search/{search}/{perPage}', 'RestaurantController@searchAllDiscounts');
		    Route::post('/discounts/{perPage?}', 'RestaurantController@createNewDiscount');
		    Route::put('/discounts/{discount}/{perPage}', 'RestaurantController@updateDiscount');
		    Route::delete('/discounts/{discount}/{perPage}', 'RestaurantController@deleteDiscount');
		    Route::patch('/discounts/{discount}/{perPage}', 'RestaurantController@restoreDiscount');


		    Route::get('/api/cuisines/{perPage?}', 'FoodController@showAllCuisines');
		    Route::get('/api/cuisines/search/{search}/{perPage}', 'FoodController@searchAllCuisines');
		    Route::post('/cuisines/{perPage?}', 'FoodController@createNewCuisine');
		    Route::put('/cuisines/{cuisine}/{perPage}', 'FoodController@updateCuisine');
		    Route::delete('/cuisines/{cuisine}/{perPage}', 'FoodController@deleteCuisine');
		    Route::patch('/cuisines/{cuisine}/{perPage}', 'FoodController@restoreCuisine');


		    Route::get('/api/menu-categories/{perPage?}', 'FoodController@showAllMenuCategories');
		    Route::get('/api/menu-categories/search/{search}/{perPage}', 'FoodController@searchAllMenuCategories');
		    Route::post('/menu-categories/{perPage?}', 'FoodController@createNewMenuCategory');
		    Route::put('/menu-categories/{meal}/{perPage}', 'FoodController@updateMenuCategory');
		    Route::delete('/menu-categories/{meal}/{perPage}', 'FoodController@deleteMenuCategory');
		    Route::patch('/menu-categories/{meal}/{perPage}', 'FoodController@restoreMenuCategory');


		    Route::get('/api/meals/{perPage?}', 'FoodController@showAllMeals');
		    Route::get('/api/meals/search/{search}/{perPage}', 'FoodController@searchAllMeals');
		    Route::post('/meals/{perPage?}', 'FoodController@createNewMeal');
		    Route::put('/meals/{meal}/{perPage}', 'FoodController@updateMeal');
		    Route::delete('/meals/{meal}/{perPage}', 'FoodController@deleteMeal');
		    Route::patch('/meals/{meal}/{perPage}', 'FoodController@restoreMeal');

 
		    Route::get('/api/restaurant-admins/{perPage?}', 'RestaurantController@showAllRestaurantAdmins');
		    Route::get('/api/restaurant-admins/search/{search}/{perPage}', 'RestaurantController@searchAllRestaurantAdmins');
		    Route::post('/restaurant-admins/{perPage?}', 'RestaurantController@createRestaurantAdmin');
		    Route::put('/restaurant-admins/{restaurantAdmin}/{perPage}', 'RestaurantController@updateRestaurantAdmin');
		    Route::delete('/restaurant-admins/{restaurantAdmin}/{perPage}', 'RestaurantController@deleteRestaurantAdmin');
		    Route::patch('/restaurant-admins/{restaurantAdmin}/{perPage}', 'RestaurantController@restoreRestaurantAdmin');


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