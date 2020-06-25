/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// Registering component globally
Vue.component('pagination', require('./components/admin/PaginationComponent.vue').default);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Importing empty scss file for webpack 4 bug
import appScss from './../sass/emptyScssForWebpackBug.scss';

import App from './views/App.vue';

// import Home from './components/admin/HomeComponent.vue';
// import Dashboard2 from './components/admin/Dashboard2.vue';
// import Dashboard3 from './components/admin/Dashboard3.vue';
// import Profile from './components/admin/ProfileComponent.vue';
// import Setting from './components/admin/SettingComponent.vue';
// import RestaurantIndexComponent from './components/admin/RestaurantIndexComponent.vue';
// import MealIndexComponent from './components/admin/MealIndexComponent.vue';
// import MenuCategoryIndexComponent from './components/admin/MenuCategoryIndexComponent.vue';
// import CuisineIndexComponent from './components/admin/CuisineIndexComponent.vue';
// import RestaurantAdminIndexComponent from './components/admin/RestaurantAdminIndexComponent.vue';
// import NotFoundComponent from './components/admin/NotFoundComponent.vue';
// import DiscountIndexComponent from './components/admin/DiscountIndexComponent.vue';
// import AddonIndexComponent from './components/admin/AddonIndexComponent.vue';
// import ItemVariationIndexComponent from './components/admin/ItemVariationIndexComponent.vue';
// import KitchenIndexComponent from './components/admin/KitchenIndexComponent.vue';
// import WaiterIndexComponent from './components/admin/WaiterIndexComponent.vue';
// import DeliveryMenIndexComponent from './components/admin/DeliveryMenIndexComponent.vue';
// import RestaurantDealIndexComponent from './components/admin/RestaurantDealIndexComponent.vue';
// import RestaurantMealIndexComponent from './components/admin/RestaurantMealIndexComponent.vue';
// import RestaurantMenuCategoryIndexComponent from './components/admin/RestaurantMenuCategoryIndexComponent.vue';
// import RestaurantCuisineIndexComponent from './components/admin/RestaurantCuisineIndexComponent.vue';
// import RestaurantMenuItemIndexComponent from './components/admin/RestaurantMenuItemIndexComponent.vue';
// import RestaurantMenuCategoryDetailIndexComponent from './components/admin/RestaurantMenuCategoryDetailIndexComponent.vue';
// import NotificationIndexComponent from './components/admin/NotificationIndexComponent.vue';
// import CouponIndexComponent from './components/admin/CouponIndexComponent.vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

const router = new VueRouter({
	mode : 'history',
	routes : [
		{
			path: '/home',
            name: 'admin.home',
            component: () => import(/* webpackChunkName : "js/home-component" */ './components/admin/HomeComponent.vue')
		},
		{
			path: '/dashboard2',
            name: 'admin.dashboard2',
            component: () => import(/* webpackChunkName : "js/dashboard2" */ './components/admin/Dashboard2.vue')
		},
		{
			path: '/dashboard3',
            name: 'admin.dashboard3',
            component: () => import(/* webpackChunkName : "js/dashboard3" */ './components/admin/Dashboard3.vue')
		},
		{
			path: '/profile',
            name: 'admin.profile',
            component: () => import(/* webpackChunkName : "js/profile" */ './components/admin/ProfileComponent.vue')
		},
		{
			path: '/settings',
            name: 'admin.setting',
            component: () => import(/* webpackChunkName : "js/settings" */ './components/admin/SettingComponent.vue')
		},
		{
			path: '/restaurants',
            name: 'admin.restaurants.index',
            component: () => import(/* webpackChunkName : "js/restaurant-list" */ './components/admin/RestaurantIndexComponent.vue')
		},
		{
			path: '/meals',
            name: 'admin.meals.index',
            component: () => import(/* webpackChunkName : "js/meal-list" */ './components/admin/MealIndexComponent.vue')
		},
		{
			path: '/menu-categories',
            name: 'admin.menuCategories.index',
            component: () => import(/* webpackChunkName : "js/menu-category-list" */ './components/admin/MenuCategoryIndexComponent.vue')
		},
		{
			path: '/cuisines',
            name: 'admin.cuisines.index',
            component: () => import(/* webpackChunkName : "js/cuisine-list" */ './components/admin/CuisineIndexComponent.vue')
		},
		{
			path: '/restaurant-admins',
            name: 'admin.restaurantAdmins.index',
            component: () => import(/* webpackChunkName : "js/restaurant-admin-list" */ './components/admin/RestaurantAdminIndexComponent.vue')
		},
		{
			path: '/discounts',
            name: 'admin.discounts.index',
            component: () => import(/* webpackChunkName : "js/discount-list" */ './components/admin/DiscountIndexComponent.vue')
		},
		{
			path: '/add-ons',
            name: 'admin.addons.index',
            component: () => import(/* webpackChunkName : "js/addon-list" */ './components/admin/AddonIndexComponent.vue')
		},
		{
			path: '/item-variations',
            name: 'admin.itemVariations.index',
            component: () => import(/* webpackChunkName : "js/item-variation-list" */ './components/admin/ItemVariationIndexComponent.vue')
		},
		{
			path: '/kitchens',
            name: 'admin.kitchens.index',
            component: () => import(/* webpackChunkName : "js/kitchen-list" */ './components/admin/KitchenIndexComponent.vue')
		},
		{
			path: '/waiters',
            name: 'admin.waiters.index',
            component: () => import(/* webpackChunkName : "js/waiter-list" */ './components/admin/WaiterIndexComponent.vue')
		},
		{
			path: '/restaurant-deals',
            name: 'admin.restaurantDeals.index',
            component: () => import(/* webpackChunkName : "js/restaurant-deal-list" */ './components/admin/RestaurantDealIndexComponent.vue')
		},
		{
			path: '/restaurant-meals',
            name: 'admin.restaurantMeals.index',
            component: () => import(/* webpackChunkName : "js/restaurant-meal-list" */ './components/admin/RestaurantMealIndexComponent.vue')
		},
		{
			path: '/restaurant-menu-categories',
            name: 'admin.restaurantMenuCategories.index',
            component: () => import(/* webpackChunkName : "js/restaurant-menu-category-list" */ './components/admin/RestaurantMenuCategoryIndexComponent.vue')
		},
		{
			path: '/restaurant-cuisines',
            name: 'admin.restaurantCuisines.index',
            component: () => import(/* webpackChunkName : "js/restaurant-cuisine-list" */ './components/admin/RestaurantCuisineIndexComponent.vue')
		},
		{
			path: '/restaurant-menu-items/:restaurant',
		    name: 'admin.restaurantMenuItem.index',
		    component: () => import(/* webpackChunkName : "js/restaurant-menu-item-list" */ './components/admin/RestaurantMenuItemIndexComponent.vue'),
		},
		{
			path: '/restaurant-menu-category-details/:restaurant',
		    name: 'admin.restaurantMenuCategoryDetail.index',
		    component: () => import(/* webpackChunkName : "js/restaurant-menu-category-detail-list" */ './components/admin/RestaurantMenuCategoryDetailIndexComponent.vue'),
		},
		{
			path: '/delivery-men',
            name: 'admin.deliveryMen.index',
            component: () => import(/* webpackChunkName : "js/delivery-man-list" */ './components/admin/DeliveryMenIndexComponent.vue')
		},
		{
			path: '/notifications',
            name: 'admin.notifications.index',
            component: () => import(/* webpackChunkName : "js/notification-list" */ './components/admin/NotificationIndexComponent.vue')
		},
		{
			path: '/coupons',
            name: 'admin.coupons.index',
            component: () => import(/* webpackChunkName : "js/coupon-list" */ './components/admin/CouponIndexComponent.vue')
		},
		{
			path: '/cancelation-reasons',
            name: 'admin.cancelationReasons.index',
            component: () => import(/* webpackChunkName : "js/cancelation-reason-list" */ './components/admin/CancelationReasonIndexComponent.vue')
		},
		{
			path: '/api-list',
            name: 'admin.apiList.index',
            component: () => import(/* webpackChunkName : "js/api-list" */ './components/admin/ApiIndexComponent.vue')
		},
		{
			path: '/restaurant-menu-items',
		    name: 'admin.expectedRestaurantForMenuItem.index',
		    component: () => import(/* webpackChunkName : "js/expected-restaurant-name-for-menu-item-list" */ './components/admin/ExpectedRestaurantNameForMenuItemComponent.vue'),
		},
		{
			path: '/orders',
            name: 'admin.orders.index',
            component: () => import(/* webpackChunkName : "js/order-list" */ './components/admin/OrderIndexComponent.vue')
		},

		
		{
			path: '/404',
            name: 'admin.routeNotFound',
            component: () => import(/* webpackChunkName : "js/not-found-component" */ './components/admin/NotFoundComponent.vue')
		},
		{
			path: '*',
            redirect : '/404'
		},
	],
});

window.showHomeComponent = function() {
    // Manually navigate to the route
    router.push('/home');
};

window.showProfileComponent = function() {
    // Manually navigate to the route
    router.push('/profile');
};

window.showSettingComponent = function() {
    // Manually navigate to the route
    router.push('/settings');
};

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    render: h => h(App),
});
