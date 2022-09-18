/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

require('./custom');

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

// Registering component globally
Vue.component('pagination', require('./pages/Pagination.vue').default);
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
			path: '/merchants',
            name: 'merchants',
            component: () => import(/* webpackChunkName : "js/merchant-list" */ './pages/admin/MerchantIndex.vue')
		},
		{
			path: '/meals',
            name: 'meals',
            component: () => import(/* webpackChunkName : "js/meal-list" */ './pages/admin/MealIndex.vue')
		},
		{
			path: '/product-categories',
            name: 'product-categories',
            component: () => import(/* webpackChunkName : "js/product-category-list" */ './pages/admin/ProductCategoryIndex.vue')
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
			path: '/variations',
            name: 'variations',
            component: () => import(/* webpackChunkName : "js/variation-list" */ './pages/admin/VariationIndex.vue')
		},
		{
			path: '/kitchens',
            name: 'kitchens',
            component: () => import(/* webpackChunkName : "js/kitchen-list" */ './pages/admin/KitchenIndex.vue')
		},
		{
			path: '/agents',
            name: 'agents',
            component: () => import(/* webpackChunkName : "js/agent-list" */ './pages/admin/MerchantAgentIndex.vue')
		},
		
		// {
			// path: '/discounts',
            // name: 'discounts',
            // component: () => import(/* webpackChunkName : "js/discount-list" */ './pages/admin/DiscountIndex.vue')
		// },
		
		{
			path: '/merchant-admins',
            name: 'merchant-admins',
            component: () => import(/* webpackChunkName : "js/merchant-admin-list" */ './pages/admin/MerchantOwnerIndex.vue')
		},
		{
			path: '/merchant-deals',
            name: 'merchant-deals',
            component: () => import(/* webpackChunkName : "js/merchant-deal-list" */ './pages/admin/MerchantDealIndex.vue')
		},
		{
			path: '/merchant-meals',
            name: 'merchant-meals',
            component: () => import(/* webpackChunkName : "js/merchant-meal-list" */ './pages/admin/MerchantMealIndex.vue')
		},
		{
			path: '/merchant-cuisines',
            name: 'merchant-cuisines',
            component: () => import(/* webpackChunkName : "js/merchant-cuisine-list" */ './pages/admin/MerchantCuisineIndex.vue')
		},
		{
			path: '/merchant-product-categories',
            name: 'merchant-product-categories',
            component: () => import(/* webpackChunkName : "js/merchant-product-category-list" */ './pages/admin/AllMerchantProductCategoryIndex.vue')
		},
		{
			path: '/merchant-product-categories/:merchantId',
		    name: 'merchant-all-product-categories',
		    component: () => import(/* webpackChunkName : "js/merchant-product-category-detail-list" */ './pages/admin/MerchantAllProductCategoryIndex.vue'),
			props: true,
			beforeEnter: (to, from, next) => {
                if (to.params.merchantId && to.params.merchantName) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/merchant-product-categories');
                }
            }
		},
		{
			path: '/merchant-products',
		    name: 'merchant-products',
		    component: () => import(/* webpackChunkName : "js/expected-merchant-name-for-product-list" */ './pages/admin/SearchingMerchantIndex.vue'),
		},
		{
			path: '/merchant-products/:merchantId',
		    name: 'merchant-all-products',
		    component: () => import(/* webpackChunkName : "js/merchant-product-list" */ './pages/admin/MerchantProductIndex.vue'),
			props: true,
			beforeEnter: (to, from, next) => {
                if (to.params.merchantId && to.params.merchantName) {
                    next(); // <-- everything good, proceed
                }
                else {
                    next('/merchant-products'); // name: 'expected-merchant-products',
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
			path: '/agent-orders',
            name: 'agent-orders',
            component: () => import(/* webpackChunkName : "js/agent-order-list" */ './pages/agent/OrderIndex.vue')
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
