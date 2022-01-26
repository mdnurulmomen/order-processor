/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// Registering component globally
Vue.component('pagination', require('./pages/Pagination.vue').default);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./pages/Example.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Importing empty scss file for webpack 4 bug
import appScss from './../sass/emptyScssForWebpackBug.scss';

import App from './pages/AdminSideMenuBar.vue';

// import Home from './pages/admin/Home.vue';
// import Dashboard2 from './pages/admin/Dashboard2.vue';
// import Dashboard3 from './pages/admin/Dashboard3.vue';
// import Profile from './pages/admin/Profile.vue';
// import Setting from './pages/admin/Setting.vue';
// import RestaurantIndexComponent from './pages/admin/RestaurantIndex.vue';
// import MealIndexComponent from './pages/admin/MealIndex.vue';
// import MenuCategoryIndexComponent from './pages/admin/MenuCategoryIndex.vue';
// import CuisineIndexComponent from './pages/admin/CuisineIndex.vue';
// import RestaurantAdminIndexComponent from './pages/admin/RestaurantAdminIndex.vue';
// import NotFoundComponent from './pages/admin/NotFound.vue';
// import DiscountIndexComponent from './pages/admin/DiscountIndex.vue';
// import AddonIndexComponent from './pages/admin/AddonIndex.vue';
// import ItemVariationIndexComponent from './pages/admin/ItemVariationIndex.vue';
// import KitchenIndexComponent from './pages/admin/KitchenIndex.vue';
// import WaiterIndexComponent from './pages/admin/WaiterIndex.vue';
// import DeliveryMenIndexComponent from './pages/admin/DeliveryMenIndex.vue';
// import RestaurantDealIndexComponent from './pages/admin/RestaurantDealIndex.vue';
// import RestaurantMealIndexComponent from './pages/admin/RestaurantMealIndex.vue';
// import RestaurantMenuCategoryIndexComponent from './pages/admin/RestaurantMenuCategoryIndex.vue';
// import RestaurantCuisineIndexComponent from './pages/admin/RestaurantCuisineIndex.vue';
// import RestaurantMenuItemIndexComponent from './pages/admin/RestaurantMenuItemIndex.vue';
// import RestaurantMenuCategoryDetailIndexComponent from './pages/admin/ExpectedRestaurantMenuCategoryIndex.vue';
// import NotificationIndexComponent from './pages/admin/NotificationIndex.vue';
// import CouponIndexComponent from './pages/admin/CouponIndex.vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

const router = new VueRouter({
	mode : 'history',
	routes : [
		{
			path: '/home',
            name: 'home',
            component: () => import(/* webpackChunkName : "js/home-component" */ './pages/admin/Home.vue')
		},
		{
			path: '/dashboard2',
            name: 'dashboard2',
            component: () => import(/* webpackChunkName : "js/dashboard2" */ './pages/admin/Dashboard2.vue')
		},
		{
			path: '/dashboard3',
            name: 'dashboard3',
            component: () => import(/* webpackChunkName : "js/dashboard3" */ './pages/admin/Dashboard3.vue')
		},
		{
			path: '/profile',
            name: 'profile',
            component: () => import(/* webpackChunkName : "js/profile" */ './pages/admin/Profile.vue')
		},
		{
			path: '/settings',
            name: 'setting',
            component: () => import(/* webpackChunkName : "js/settings" */ './pages/admin/Setting.vue')
		},
		{
			path: '/restaurants',
            name: 'restaurants',
            component: () => import(/* webpackChunkName : "js/restaurant-list" */ './pages/admin/RestaurantIndex.vue')
		},
		{
			path: '/meals',
            name: 'meals',
            component: () => import(/* webpackChunkName : "js/meal-list" */ './pages/admin/MealIndex.vue')
		},
		{
			path: '/menu-categories',
            name: 'menu-categories',
            component: () => import(/* webpackChunkName : "js/menu-category-list" */ './pages/admin/MenuCategoryIndex.vue')
		},
		{
			path: '/cuisines',
            name: 'cuisines',
            component: () => import(/* webpackChunkName : "js/cuisine-list" */ './pages/admin/CuisineIndex.vue')
		},
		{
			path: '/addons',
            name: 'addons',
            component: () => import(/* webpackChunkName : "js/addon-list" */ './pages/admin/AddonIndex.vue')
		},
		{
			path: '/item-variations',
            name: 'item-variations',
            component: () => import(/* webpackChunkName : "js/item-variation-list" */ './pages/admin/ItemVariationIndex.vue')
		},
		{
			path: '/kitchens',
            name: 'kitchens',
            component: () => import(/* webpackChunkName : "js/kitchen-list" */ './pages/admin/KitchenIndex.vue')
		},
		{
			path: '/waiters',
            name: 'waiters',
            component: () => import(/* webpackChunkName : "js/waiter-list" */ './pages/admin/WaiterIndex.vue')
		},
		
		// {
			// path: '/discounts',
            // name: 'discounts',
            // component: () => import(/* webpackChunkName : "js/discount-list" */ './pages/admin/DiscountIndex.vue')
		// },
		
		{
			path: '/restaurant-admins',
            name: 'restaurant-admins',
            component: () => import(/* webpackChunkName : "js/restaurant-admin-list" */ './pages/admin/RestaurantAdminIndex.vue')
		},
		{
			path: '/restaurant-deals',
            name: 'restaurant-deals',
            component: () => import(/* webpackChunkName : "js/restaurant-deal-list" */ './pages/admin/RestaurantDealIndex.vue')
		},
		{
			path: '/restaurant-meals',
            name: 'restaurant-meals',
            component: () => import(/* webpackChunkName : "js/restaurant-meal-list" */ './pages/admin/RestaurantMealIndex.vue')
		},
		{
			path: '/restaurant-cuisines',
            name: 'restaurant-cuisines',
            component: () => import(/* webpackChunkName : "js/restaurant-cuisine-list" */ './pages/admin/RestaurantCuisineIndex.vue')
		},
		{
			path: '/restaurant-menu-categories',
            name: 'restaurant-menu-categories',
            component: () => import(/* webpackChunkName : "js/restaurant-menu-category-list" */ './pages/admin/RestaurantMenuCategoryIndex.vue')
		},
		{
			path: '/restaurant-menu-categories/:restaurantId',
		    name: 'expected-restaurant-menu-categories',
		    component: () => import(/* webpackChunkName : "js/restaurant-menu-category-detail-list" */ './pages/admin/ExpectedRestaurantMenuCategoryIndex.vue'),
			props: true,
			beforeEnter: (to, from, next) => {
                if (to.params.restaurantId && to.params.restaurantName) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/restaurant-menu-categories');
                }
            }
		},
		{
			path: '/restaurant-menu-items',
		    name: 'expected-restaurant-menu-items',
		    component: () => import(/* webpackChunkName : "js/expected-restaurant-name-for-menu-item-list" */ './pages/admin/ExpectedRestaurantNameForMenuItem.vue'),
		},
		{
			path: '/restaurant-menu-items/:restaurantId',
		    name: 'restaurant-menu-items',
		    component: () => import(/* webpackChunkName : "js/restaurant-menu-item-list" */ './pages/admin/RestaurantMenuItemIndex.vue'),
			props: true,
			beforeEnter: (to, from, next) => {
                if (to.params.restaurantId && to.params.restaurantName) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/restaurant-menu-items');
                }
            }
		},
		{
			path: '/delivery-men',
            name: 'delivery-men',
            component: () => import(/* webpackChunkName : "js/delivery-man-list" */ './pages/admin/DeliveryMenIndex.vue')
		},
		{
			path: '/notifications',
            name: 'notifications',
            component: () => import(/* webpackChunkName : "js/notification-list" */ './pages/admin/NotificationIndex.vue')
		},
		{
			path: '/coupons',
            name: 'coupons',
            component: () => import(/* webpackChunkName : "js/coupon-list" */ './pages/admin/CouponIndex.vue')
		},
		{
			path: '/cancelation-reasons',
            name: 'cancelation-reasons',
            component: () => import(/* webpackChunkName : "js/cancelation-reason-list" */ './pages/admin/CancelationReasonIndex.vue')
		},
		{
			path: '/api-list',
            name: 'api-list',
            component: () => import(/* webpackChunkName : "js/api-list" */ './pages/admin/ApiIndex.vue')
		},
		{
			path: '/orders',
            name: 'orders',
            component: () => import(/* webpackChunkName : "js/order-list" */ './pages/admin/OrderIndex.vue')
		},
		{
			path: '/rider-orders',
            name: 'rider-orders',
            component: () => import(/* webpackChunkName : "js/rider-order-list" */ './pages/rider/OrderIndex.vue')
		},
		{
			path: '/waiter-orders',
            name: 'waiter-orders',
            component: () => import(/* webpackChunkName : "js/waiter-order-list" */ './pages/waiter/OrderIndex.vue')
		},

		
		{
			path: '/404',
            name: 'routeNotFound',
            component: () => import(/* webpackChunkName : "js/not-found-component" */ './pages/NotFound.vue')
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
