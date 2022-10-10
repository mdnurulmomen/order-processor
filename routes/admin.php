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

		Route::group(['middleware' => ['auth:admin']], function () {
		    
		    Route::post('/logout', 'LoginController@adminLogout')->name('logout');

		});

		// For all other routes coming from vue
    	Route::get('/{any}/{id?}', 'HomeController@showAdminHome')->where('id', '[0-9]+');	
	});


	Route::namespace('Web')->group(function () {

		Route::group(['middleware' => ['auth:admin']], function () {
		    
		    Route::get('/api/profile', 'ProfileController@showAdminProfile');
		    Route::post('/profile', 'ProfileController@updateAdminProfile');

		    Route::get('/api/settings', 'SettingController@showAllSettings');
		    Route::post('/payment-settings', 'SettingController@updatePaymentSettings');
		    Route::post('/contact-settings', 'SettingController@updateContactSettings');
		    Route::post('/application-settings', 'SettingController@updateApplicationSettings');
		    Route::post('/service-settings', 'SettingController@updateServiceSettings');
		    Route::post('/other-settings', 'SettingController@updateOtherSettings');

		    Route::post('/password', 'PasswordController@updateAdminPassword');

		    // Merchant-Cuisines
		    Route::get('/api/merchant-cuisines/{perPage?}', 'ProductController@showAllMerchantCuisines');
		    Route::get('/api/merchant-cuisines/search/{search}/{perPage}', 'ProductController@searchAllMerchantCuisines');
		    Route::post('/merchant-cuisines/{perPage?}', 'ProductController@createMerchantCuisine');
		    Route::put('/merchant-cuisines/{merchant}/{perPage}', 'ProductController@updateMerchantCuisine');
		    Route::delete('/merchant-cuisines/{merchant}/{perPage}', 'ProductController@deleteMerchantCuisine');

		    // Merchant-Meals
		    Route::get('/api/merchant-meals/{perPage?}', 'ProductController@showAllMerchantMeals');
		    Route::get('/api/merchant-meals/search/{search}/{perPage}', 'ProductController@searchAllMerchantMeals');
		    Route::post('/merchant-meals/{perPage?}', 'ProductController@createMerchantMeal');
		    Route::put('/merchant-meals/{merchant}/{perPage}', 'ProductController@updateMerchantMeal');
		    Route::delete('/merchant-meals/{merchant}/{perPage}', 'ProductController@deleteMerchantMeal');

		    // Merchant-Product-Categories
		    Route::get('/api/merchant-product-categories/{perPage?}', 'ProductController@showAllMerchantProductCategories');
		    Route::get('/api/search-merchant-product-categories/{search}/{perPage}', 'ProductController@searchAllMerchantProductCategories');
		    
		    // Merchant-All-Product-Categories
		    Route::get('/api/merchant-product-categories/{merchant}/{perPage?}', 'ProductController@showMerchantAllProductCategories');
		    Route::get('/api/search-merchant-product-categories/{merchant}/{search}/{perPage}', 'ProductController@searchMerchantAllProductCategories');
		    Route::post('/merchant-product-categories/{perPage?}', 'ProductController@createMerchantProductCategory');
		    Route::put('/merchant-product-categories/{productCategory}/{perPage}', 'ProductController@updateMerchantProductCategory');
		    Route::delete('/merchant-product-categories/{productCategory}/{perPage}', 'ProductController@deleteMerchantProductCategory');
		    Route::patch('/merchant-product-categories/{productCategory}/{perPage}', 'ProductController@restoreMerchantProductCategory');

		    // Merchant-Products
		    Route::get('/api/merchant-products/{merchant}/{perPage?}', 'ProductController@showMerchantAllProducts');
		    Route::get('/api/merchant-products/search/{merchant}/{search}/{perPage}', 'ProductController@searchMerchantAllProducts');
		    Route::post('/merchant-products/{perPage?}', 'ProductController@createMerchantProduct');
		    Route::put('/merchant-products/{merchantProduct}/{perPage}', 'ProductController@updateMerchantProduct');
		    Route::delete('/merchant-products/{merchant}/{merchantProduct}/{perPage}', 'ProductController@deleteMerchantProduct');
		    Route::patch('/merchant-products/{merchant}/{merchantProduct}/{perPage}', 'ProductController@restoreMerchantProduct');

		    // Orders
		    Route::get('/api/orders/{perPage?}', 'OrderController@showAllOrders');
		    Route::get('/orders/{order}/show', 'OrderController@showOrderDetail');
		    Route::post('/orders/{perPage}', 'OrderController@confirmNewOrder');
		    Route::put('/orders/{order}/{perPage}', 'OrderController@cancelNewOrder');
		    Route::get('/api/orders/search/{search}/{perPage}', 'OrderController@searchAllOrders');

		    // Rider-Orders
		    Route::get('/api/rider-orders/{rider?}/{perPage?}', 'OrderController@showAllRiderOrders');
		    Route::post('/delivery-order-confirmations/{order}/{perPage?}', 'OrderController@confirmRiderDeliveryOrder');

		    // Agent-Orders
		    Route::get('/api/agent-orders/{merchant}/{perPage?}', 'OrderController@showAgentAllOrders');
		    Route::post('/order-serve-confirmations/{order}/{perPage?}', 'OrderController@confirmAgentOrder');

		    // Cancellation-Reasons
		    Route::get('/api/cancellation-reasons/{perPage?}', 'CancellationController@showAllReasons');
		    Route::get('/api/cancellation-reasons/search/{search}/{perPage}', 'CancellationController@searchAllReasons');
		    Route::post('/cancellation-reasons/{perPage?}', 'CancellationController@createCancellationReason');
		    Route::put('/cancellation-reasons/{reason}/{perPage}', 'CancellationController@updateCancellationReason');
		    Route::delete('/cancellation-reasons/{reason}/{perPage}', 'CancellationController@deleteCancellationReason');
		    Route::patch('/cancellation-reasons/{reason}/{perPage}', 'CancellationController@restoreCancellationReason');

		    // Merchant-Deals
		    Route::get('/api/merchant-deals/{perPage?}', 'MerchantController@showAllMerchantDeals');
		    Route::get('/api/merchant-deals/search/{search}/{perPage}', 'MerchantController@searchAllMerchantDeals');
		    Route::post('/merchant-deals/{perPage?}', 'MerchantController@createMerchantDeal');
		    Route::put('/merchant-deals/{deal}/{perPage}', 'MerchantController@updateMerchantDeal');
		    Route::delete('/merchant-deals/{deal}/{perPage}', 'MerchantController@deleteMerchantDeal');
		    Route::patch('/merchant-deals/{deal}/{perPage}', 'MerchantController@restoreMerchantDeal');

		    // Merchant-Agents
		    Route::get('/api/merchant-agents/{perPage?}', 'MerchantController@showAllMerchantAgents');
		    Route::get('/api/merchant-agents/search/{search}/{perPage}', 'MerchantController@searchAllMerchantAgents');
		    Route::post('/merchant-agents/{perPage?}', 'MerchantController@createMerchantAgent');
		    Route::put('/merchant-agents/{agent}/{perPage}', 'MerchantController@updateMerchantAgent');
		    Route::delete('/merchant-agents/{agent}/{perPage}', 'MerchantController@deleteMerchantAgent');
		    Route::patch('/merchant-agents/{agent}/{perPage}', 'MerchantController@restoreMerchantAgent');

		    // Merchant-Kitchens
		    Route::get('/api/merchant-kitchens/{perPage?}', 'MerchantController@showAllMerchantKitchens');
		    Route::get('/api/merchant-kitchens/search/{search}/{perPage}', 'MerchantController@searchAllMerchantKitchens');
		    Route::post('/merchant-kitchens/{perPage?}', 'MerchantController@createMerchantKitchen');
		    Route::put('/merchant-kitchens/{kitchen}/{perPage}', 'MerchantController@updateMerchantKitchen');
		    Route::delete('/merchant-kitchens/{kitchen}/{perPage}', 'MerchantController@deleteMerchantKitchen');
		    Route::patch('/merchant-kitchens/{kitchen}/{perPage}', 'MerchantController@restoreMerchantKitchen');

		    // Add-ons
		    Route::get('/api/add-ons/{perPage?}', 'AssetController@showAllAddons');
		    Route::get('/api/add-ons/search/{search}/{perPage}', 'AssetController@searchAllAddons');
		    Route::post('/add-ons/{perPage?}', 'AssetController@createNewAddon');
		    Route::put('/add-ons/{addon}/{perPage}', 'AssetController@updateAddon');
		    Route::delete('/add-ons/{addon}/{perPage}', 'AssetController@deleteAddon');
		    Route::patch('/add-ons/{addon}/{perPage}', 'AssetController@restoreAddon');

		    // Variations
		    Route::get('/api/variations/{perPage?}', 'AssetController@showAllVariations');
		    Route::get('/api/variations/search/{search}/{perPage}', 'AssetController@searchAllVariations');
		    Route::post('/variations/{perPage?}', 'AssetController@createNewVariation');
		    Route::put('/variations/{item}/{perPage}', 'AssetController@updateVariation');
		    Route::delete('/variations/{item}/{perPage}', 'AssetController@deleteVariation');
		    Route::patch('/variations/{item}/{perPage}', 'AssetController@restoreVariation');

		    // Discounts
		    Route::get('/api/discounts/{perPage?}', 'DiscountController@showAllDiscounts');
		    Route::get('/api/discounts/search/{search}/{perPage}', 'DiscountController@searchAllDiscounts');
		    Route::post('/discounts/{perPage?}', 'DiscountController@createNewDiscount');
		    Route::put('/discounts/{discount}/{perPage}', 'DiscountController@updateDiscount');

		    // Cuisines
		    Route::get('/api/cuisines/{perPage?}', 'AssetController@showAllCuisines');
		    Route::get('/api/cuisines/search/{search}/{perPage}', 'AssetController@searchAllCuisines');
		    Route::post('/cuisines/{perPage?}', 'AssetController@createNewCuisine');
		    Route::put('/cuisines/{cuisine}/{perPage}', 'AssetController@updateCuisine');
		    Route::delete('/cuisines/{cuisine}/{perPage}', 'AssetController@deleteCuisine');
		    Route::patch('/cuisines/{cuisine}/{perPage}', 'AssetController@restoreCuisine');

		    // Product-Categories
		    Route::get('/api/product-categories/{perPage?}', 'AssetController@showAllProductCategories');
		    Route::get('/api/product-categories/search/{search}/{perPage}', 'AssetController@searchAllProductCategories');
		    Route::post('/product-categories/{perPage?}', 'AssetController@createNewProductCategory');
		    Route::put('/product-categories/{meal}/{perPage}', 'AssetController@updateProductCategory');
		    Route::delete('/product-categories/{meal}/{perPage}', 'AssetController@deleteProductCategory');
		    Route::patch('/product-categories/{meal}/{perPage}', 'AssetController@restoreProductCategory');

		    // Meals
		    Route::get('/api/meals/{perPage?}', 'AssetController@showAllMeals');
		    Route::get('/api/meals/search/{search}/{perPage}', 'AssetController@searchAllMeals');
		    Route::post('/meals/{perPage?}', 'AssetController@createNewMeal');
		    Route::put('/meals/{meal}/{perPage}', 'AssetController@updateMeal');
		    Route::delete('/meals/{meal}/{perPage}', 'AssetController@deleteMeal');
		    Route::patch('/meals/{meal}/{perPage}', 'AssetController@restoreMeal');

 			// Merchant-Owners
		    Route::get('/api/merchant-owners/{perPage?}', 'MerchantController@showAllMerchantOwners');
		    Route::get('/api/merchant-owners/search/{search}/{perPage}', 'MerchantController@searchAllMerchantOwners');
		    Route::post('/merchant-owners/{perPage?}', 'MerchantController@createMerchantOwner');
		    Route::put('/merchant-owners/{admin}/{perPage}', 'MerchantController@updateMerchantOwner');
		    Route::delete('/merchant-owners/{admin}/{perPage}', 'MerchantController@deleteMerchantOwner');
		    Route::patch('/merchant-owners/{admin}/{perPage}', 'MerchantController@restoreMerchantOwner');

		    // Delivery-Men
		    Route::get('/api/delivery-men/{perPage?}', 'DeliveryController@showAllDeliveryMen');
		    Route::get('/api/delivery-men/search/{search}/{perPage}', 'DeliveryController@searchAllDeliveryMen');
		    Route::post('/delivery-men/{perPage?}', 'DeliveryController@createDeliveryMan');
		    Route::put('/delivery-men/{deliveryMan}/{perPage}', 'DeliveryController@updateDeliveryMan');
		    Route::delete('/delivery-men/{deliveryMan}/{perPage}', 'DeliveryController@deleteDeliveryMan');
		    Route::patch('/delivery-men/{deliveryMan}/{perPage}', 'DeliveryController@restoreDeliveryMan');

		    // Notifications
		    Route::get('/api/notifications/{perPage?}', 'NotificationController@showAllNotifications');
		    Route::get('/api/notifications/search/{search}/{perPage}', 'NotificationController@searchAllNotifications');
		    Route::post('/notifications/{perPage?}', 'NotificationController@createNotification');
		    Route::put('/notifications/{notification}/{perPage}', 'NotificationController@updateNotification');
		    Route::delete('/notifications/{notification}/{perPage}', 'NotificationController@deleteNotification');
		    // Route::patch('/notifications/{notification}/{perPage}', 'NotificationController@restoreNotification');

		    // Coupons
		    Route::get('/api/coupons/{perPage?}', 'DiscountController@showAllCoupons');
		    Route::get('/api/coupons/search/{search}/{perPage}', 'DiscountController@searchAllCoupons');
		    Route::post('/coupons/{perPage?}', 'DiscountController@createNewCoupon');
		    Route::put('/coupons/{coupon}/{perPage}', 'DiscountController@updateCoupon');
		    Route::delete('/coupons/{coupon}/{perPage}', 'DiscountController@deleteCoupon');
		    // Route::patch('/coupons/{coupon}/{perPage}', 'DiscountController@restoreCoupon');

		    // Merchants
		    Route::get('/api/merchants/{perPage?}', 'MerchantController@showAllMerchants');
		    Route::get('/api/merchants/search/{search}/{perPage}', 'MerchantController@searchAllMerchants');
		    Route::post('/merchants/{perPage}', 'MerchantController@createNewMerchant');
		    Route::put('/merchants/{merchant}/{perPage}', 'MerchantController@updateMerchant');
		    Route::delete('/merchants/{merchant}/{perPage}', 'MerchantController@deleteMerchant');
		    Route::patch('/merchants/{merchant}/{perPage}', 'MerchantController@restoreMerchant');
		});
	});
});

Route::fallback(function () {
    Route::view('/home', 'layouts.admin');
});