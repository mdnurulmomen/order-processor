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
	    
	    // For all other routes coming from vue
    	Route::get('/{any}/{id?}', 'HomeController@showRestaurantHome')->where('id', '[0-9]+');

		Route::group(['middleware' => ['auth:restaurant']], function () {
		    
		    Route::post('/logout', 'LoginController@restaurantLogout')->name('logout');

		});
	});

	Route::namespace('Web')->group(function () {

		Route::group(['middleware' => ['auth:restaurant']], function () {
		    
		    Route::get('/api/profile', 'ProfileController@showAdminProfile');
		    Route::post('/profile', 'ProfileController@updateAdminProfile');

		    // Route::get('/api/settings', 'SettingController@showAllSettings');
		    // Route::post('/payment-settings', 'SettingController@updatePaymentSettings');
		    // Route::post('/contact-settings', 'SettingController@updateContactSettings');
		    // Route::post('/delivery-settings', 'SettingController@updateDeliverySettings');
		    // Route::post('/other-settings', 'SettingController@updateOtherSettings');

		    Route::post('/password', 'PasswordController@updateAdminPassword');

		    Route::get('/restaurant-meals/{restaurant}/{perPage}', 'RestaurantController@showRestaurantAllMeals');
		    Route::get('/restaurant-meals/{restaurant}/{search}/{perPage}', 'RestaurantController@searchRestaurantAllMeals');

		    Route::get('/restaurant-cuisines/{restaurant}/{perPage}', 'RestaurantController@showRestaurantAllCuisines');
		    Route::get('/restaurant-cuisines/{restaurant}/{search}/{perPage}', 'RestaurantController@searchRestaurantAllCuisines');

		    Route::get('/api/menu-items/{restaurant}/{perPage?}', 'RestaurantController@showRestaurantAllMenuItems');
		    Route::put('/menu-items/{restaurant}/{perPage}', 'RestaurantController@updateRestaurantMenuItemStock');
		    Route::get('/menu-items/{restaurant}/{search}/{perPage}', 'RestaurantController@searchRestaurantAllMenuItems');

		    Route::get('/api/restaurant-menu-categories/{restaurant}/{perPage?}', 'RestaurantController@showRestaurantAllMenuCategories');
		    Route::get('/restaurant-menu-categories/{restaurant}/{search}/{perPage}', 'RestaurantController@searchRestaurantAllMenuCategories');
		    // Route::put('/restaurant-menu-categories/{restaurant}/{perPage}', 'RestaurantController@updateRestaurantMenuCategoryStock');

		    Route::get('/api/my-kitchen/{restaurant}', 'RestaurantController@showRestaurantKitchen');
		    // Route::put('/my-kitchen/{restaurant}', 'RestaurantController@updateRestaurantKitchen');
		    // Route::put('/my-kitchen/{restaurant}', 'RestaurantController@updateRestaurantMenuItemStock');

		    Route::get('/api/cancelation-reasons/{perPage?}', 'CancelationController@showAllReasons');

		    Route::get('/orders/{restaurant}/{perPage?}', 'OrderController@showAllRestaurantOrders');
		    Route::post('/orders/{order}/{perPage}', 'OrderController@confirmRestaurantOrder');
		    Route::put('/orders/{order}/{perPage}', 'OrderController@cancelRestaurantOrder');
		    // Route::get('/api/orders/search/{search}/{perPage}', 'OrderController@searchAllOrders');

		    Route::get('/restaurant-waiters/{restaurant}/{perPage}', 'RestaurantController@showRestaurantAllWaiters');
		    Route::get('/restaurant-waiters/{restaurant}/{search}/{perPage}', 'RestaurantController@searchRestaurantAllWaiters');

		});
	});
});

