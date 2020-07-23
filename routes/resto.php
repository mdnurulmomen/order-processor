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

		    Route::get('/api/restaurant-meals/{restaurant}/{perPage}', 'RestaurantController@showRestaurantAllMeals');
		    Route::get('/api/restaurant-meals/{restaurant}/{search}/{perPage}', 'RestaurantController@searchRestaurantAllMeals');

		    Route::get('/api/restaurant-cuisines/{restaurant}/{perPage}', 'RestaurantController@showRestaurantAllCuisines');
		    Route::get('/api/restaurant-cuisines/{restaurant}/{search}/{perPage}', 'RestaurantController@searchRestaurantAllCuisines');

		    Route::get('/api/menu-items/{restaurant}/{perPage?}', 'RestaurantController@showRestaurantAllMenuItems');
		    Route::put('/menu-items/{restaurant}/{perPage}', 'RestaurantController@updateRestaurantMenuItemStock');
		    Route::get('/menu-items/{restaurant}/{search}/{perPage}', 'RestaurantController@searchRestaurantAllMenuItems');

		    Route::get('/api/cancelation-reasons/{perPage?}', 'CancelationController@showAllReasons');

		    Route::get('/orders/{restaurant}/{perPage?}', 'OrderController@showAllRestaurantOrders');
		    Route::post('/orders/{order}/{perPage}', 'OrderController@confirmRestaurantOrder');
		    Route::put('/orders/{order}/{perPage}', 'OrderController@cancelRestaurantOrder');
		    // Route::get('/api/orders/search/{search}/{perPage}', 'OrderController@searchAllOrders');

		    // Route::get('/api/restaurant-waiters/{perPage?}', 'RestaurantController@showAllRestaurantWaiters');
		    // Route::get('/api/restaurant-waiters/search/{search}/{perPage}', 'RestaurantController@searchAllRestaurantWaiters');
		    // Route::post('/restaurant-waiters/{perPage?}', 'RestaurantController@createRestaurantWaiter');
		    // Route::put('/restaurant-waiters/{restaurantWaiter}/{perPage}', 'RestaurantController@updateRestaurantWaiter');
		    // Route::delete('/restaurant-waiters/{restaurantWaiter}/{perPage}', 'RestaurantController@deleteRestaurantWaiter');
		    // Route::patch('/restaurant-waiters/{restaurantWaiter}/{perPage}', 'RestaurantController@restoreRestaurantWaiter');

		    // Route::get('/api/restaurant-kitchens/{perPage?}', 'RestaurantController@showAllRestaurantKitchens');
		    // Route::get('/api/restaurant-kitchens/search/{search}/{perPage}', 'RestaurantController@searchAllRestaurantKitchens');
		    // Route::post('/restaurant-kitchens/{perPage?}', 'RestaurantController@createRestaurantKitchen');
		    // Route::put('/restaurant-kitchens/{restaurantKitchen}/{perPage}', 'RestaurantController@updateRestaurantKitchen');
		    // Route::delete('/restaurant-kitchens/{restaurantKitchen}/{perPage}', 'RestaurantController@deleteRestaurantKitchen');
		    // Route::patch('/restaurant-kitchens/{restaurantKitchen}/{perPage}', 'RestaurantController@restoreRestaurantKitchen');

		});
	});
});

