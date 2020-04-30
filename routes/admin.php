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
    	Route::get('/{any}/{id?}', 'HomeController@showAdminHome')->where('id', '[0-9]+');

		Route::group(['middleware' => ['auth:admin']], function () {
		    
		    Route::post('/logout', 'LoginController@adminLogout')->name('logout');
		});
	});


	Route::namespace('Web')->group(function () {

		Route::group(['middleware' => ['auth:admin']], function () {
		    
		    Route::get('/api/profile', 'ProfileController@showAdminProfile');
		    Route::post('/profile', 'ProfileController@updateAdminProfile');

		    Route::get('/api/settings', 'SettingController@showAllSettings');
		    Route::post('/payment-settings', 'SettingController@updatePaymentSettings');
		    Route::post('/contact-settings', 'SettingController@updateContactSettings');
		    Route::post('/delivery-settings', 'SettingController@updateDeliverySettings');
		    Route::post('/other-settings', 'SettingController@updateOtherSettings');


		    Route::post('/password', 'PasswordController@updateAdminPassword');

		    
		    Route::get('/api/restaurant-menu-items/{restaurant}/{perPage?}', 'RestaurantController@showRestaurantAllMenuItems');
		    Route::get('/api/restaurant-menu-items/search/{restaurant}/{search}/{perPage}', 'RestaurantController@searchRestaurantAllMenuItems');
		    Route::post('/restaurant-menu-items/{perPage?}', 'RestaurantController@createRestaurantMenuItem');
		    Route::put('/restaurant-menu-items/{menuItem}/{perPage}', 'RestaurantController@updateRestaurantMenuItem');
		    Route::delete('/restaurant-menu-items/{restaurant}/{menuItem}/{perPage}', 'RestaurantController@deleteRestaurantMenuItem');
		    Route::patch('/restaurant-menu-items/{restaurant}/{menuItem}/{perPage}', 'RestaurantController@restoreRestaurantMenuItem');


		    Route::get('/api/restaurant-cuisines/{perPage?}', 'RestaurantController@showAllRestaurantCuisines');
		    Route::get('/api/restaurant-cuisines/search/{search}/{perPage}', 'RestaurantController@searchAllRestaurantCuisines');
		    Route::post('/restaurant-cuisines/{perPage?}', 'RestaurantController@createRestaurantCuisine');
		    Route::put('/restaurant-cuisines/{restaurant}/{perPage}', 'RestaurantController@updateRestaurantCuisine');
		    Route::delete('/restaurant-cuisines/{restaurant}/{perPage}', 'RestaurantController@deleteRestaurantCuisine');


		    Route::get('/api/restaurant-meals/{perPage?}', 'RestaurantController@showAllRestaurantMeals');
		    Route::get('/api/restaurant-meals/search/{search}/{perPage}', 'RestaurantController@searchAllRestaurantMeals');
		    Route::post('/restaurant-meals/{perPage?}', 'RestaurantController@createRestaurantMeal');
		    Route::put('/restaurant-meals/{restaurant}/{perPage}', 'RestaurantController@updateRestaurantMeal');
		    Route::delete('/restaurant-meals/{restaurant}/{perPage}', 'RestaurantController@deleteRestaurantMeal');


		    Route::get('/api/restaurant-menu-categories/{perPage?}', 'RestaurantController@showAllRestaurantMenuCategories');
		    Route::get('/api/restaurant-menu-categories/search/{search}/{perPage}', 'RestaurantController@searchAllRestaurantMenuCategories');
		    

		    Route::get('/api/restaurant-menu-categories/{restaurant}/{perPage?}', 'RestaurantController@showRestaurantAllMenuCategories');
		    Route::get('/api/restaurant-menu-categories/search/{restaurant}/{search}/{perPage}', 'RestaurantController@searchRestaurantAllMenuCategories');
		    Route::post('/restaurant-menu-categories/{perPage?}', 'RestaurantController@createRestaurantMenuCategory');
		    Route::put('/restaurant-menu-categories/{menuCategory}/{perPage}', 'RestaurantController@updateRestaurantMenuCategory');
		    Route::delete('/restaurant-menu-categories/{menuCategory}/{perPage}', 'RestaurantController@deleteRestaurantMenuCategory');
		    Route::patch('/restaurant-menu-categories/{menuCategory}/{perPage}', 'RestaurantController@restoreRestaurantMenuCategory');


		    Route::get('/api/restaurant-deals/{perPage?}', 'RestaurantController@showAllRestaurantDeals');
		    Route::get('/api/restaurant-deals/search/{search}/{perPage}', 'RestaurantController@searchAllRestaurantDeals');
		    Route::post('/restaurant-deals/{perPage?}', 'RestaurantController@createRestaurantDeal');
		    Route::put('/restaurant-deals/{restaurantDeal}/{perPage}', 'RestaurantController@updateRestaurantDeal');
		    Route::delete('/restaurant-deals/{restaurantDeal}/{perPage}', 'RestaurantController@deleteRestaurantDeal');
		    Route::patch('/restaurant-deals/{restaurantDeal}/{perPage}', 'RestaurantController@restoreRestaurantDeal');


		    Route::get('/api/restaurant-waiters/{perPage?}', 'RestaurantController@showAllRestaurantWaiters');
		    Route::get('/api/restaurant-waiters/search/{search}/{perPage}', 'RestaurantController@searchAllRestaurantWaiters');
		    Route::post('/restaurant-waiters/{perPage?}', 'RestaurantController@createRestaurantWaiter');
		    Route::put('/restaurant-waiters/{restaurantWaiter}/{perPage}', 'RestaurantController@updateRestaurantWaiter');
		    Route::delete('/restaurant-waiters/{restaurantWaiter}/{perPage}', 'RestaurantController@deleteRestaurantWaiter');
		    Route::patch('/restaurant-waiters/{restaurantWaiter}/{perPage}', 'RestaurantController@restoreRestaurantWaiter');


		    Route::get('/api/restaurant-kitchens/{perPage?}', 'RestaurantController@showAllRestaurantKitchens');
		    Route::get('/api/restaurant-kitchens/search/{search}/{perPage}', 'RestaurantController@searchAllRestaurantKitchens');
		    Route::post('/restaurant-kitchens/{perPage?}', 'RestaurantController@createRestaurantKitchen');
		    Route::put('/restaurant-kitchens/{restaurantKitchen}/{perPage}', 'RestaurantController@updateRestaurantKitchen');
		    Route::delete('/restaurant-kitchens/{restaurantKitchen}/{perPage}', 'RestaurantController@deleteRestaurantKitchen');
		    Route::patch('/restaurant-kitchens/{restaurantKitchen}/{perPage}', 'RestaurantController@restoreRestaurantKitchen');


		    Route::get('/api/add-ons/{perPage?}', 'FoodController@showAllAddons');
		    Route::get('/api/add-ons/search/{search}/{perPage}', 'FoodController@searchAllAddons');
		    Route::post('/add-ons/{perPage?}', 'FoodController@createNewAddon');
		    Route::put('/add-ons/{addon}/{perPage}', 'FoodController@updateAddon');
		    Route::delete('/add-ons/{addon}/{perPage}', 'FoodController@deleteAddon');
		    Route::patch('/add-ons/{addon}/{perPage}', 'FoodController@restoreAddon');


		    Route::get('/api/item-variations/{perPage?}', 'FoodController@showAllVariations');
		    Route::get('/api/item-variations/search/{search}/{perPage}', 'FoodController@searchAllVariations');
		    Route::post('/item-variations/{perPage?}', 'FoodController@createNewVariation');
		    Route::put('/item-variations/{item}/{perPage}', 'FoodController@updateVariation');
		    Route::delete('/item-variations/{item}/{perPage}', 'FoodController@deleteVariation');
		    Route::patch('/item-variations/{item}/{perPage}', 'FoodController@restoreVariation');


		    Route::get('/api/discounts/{perPage?}', 'DiscountController@showAllDiscounts');
		    Route::get('/api/discounts/search/{search}/{perPage}', 'DiscountController@searchAllDiscounts');
		    Route::post('/discounts/{perPage?}', 'DiscountController@createNewDiscount');
		    Route::put('/discounts/{discount}/{perPage}', 'DiscountController@updateDiscount');


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


		    Route::get('/api/delivery-men/{perPage?}', 'DeliveryController@showAllDeliveryMen');
		    Route::get('/api/delivery-men/search/{search}/{perPage}', 'DeliveryController@searchAllDeliveryMen');
		    Route::post('/delivery-men/{perPage?}', 'DeliveryController@createDeliveryMan');
		    Route::put('/delivery-men/{deliveryMan}/{perPage}', 'DeliveryController@updateDeliveryMan');
		    Route::delete('/delivery-men/{deliveryMan}/{perPage}', 'DeliveryController@deleteDeliveryMan');
		    Route::patch('/delivery-men/{deliveryMan}/{perPage}', 'DeliveryController@restoreDeliveryMan');


		    Route::get('/api/notifications/{perPage?}', 'NotificationController@showAllNotifications');
		    Route::get('/api/notifications/search/{search}/{perPage}', 'NotificationController@searchAllNotifications');
		    Route::post('/notifications/{perPage?}', 'NotificationController@createNotification');
		    Route::put('/notifications/{notification}/{perPage}', 'NotificationController@updateNotification');
		    Route::delete('/notifications/{notification}/{perPage}', 'NotificationController@deleteNotification');
		    // Route::patch('/notifications/{notification}/{perPage}', 'NotificationController@restoreNotification');


		    Route::get('/api/restaurants/{perPage?}', 'RestaurantController@showAllRestaurants');
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