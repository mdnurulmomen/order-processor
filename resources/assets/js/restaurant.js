/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// Registering component globally
Vue.component('pagination', require('./pages/PaginationComponent.vue').default);

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

import App from './views/RestaurantSideMenuBar.vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

const router = new VueRouter({
	mode : 'history',
	routes : [
		{
			path: '/home',
            name: 'restaurant.home',
            component: () => import(/* webpackChunkName : "js/home-component" */ './components/restaurant/HomeComponent.vue')
		},
		{
			path: '/profile',
            name: 'restaurant.profile',
            component: () => import(/* webpackChunkName : "js/profile" */ './components/restaurant/ProfileComponent.vue')
		},
		{
			path: '/settings',
            name: 'restaurant.setting',
            component: () => import(/* webpackChunkName : "js/settings" */ './components/restaurant/SettingComponent.vue')
		},


		{
			path: '/my-cuisines',
            name: 'restaurant.myCuisines.index',
            component: () => import(/* webpackChunkName : "js/kitchen-list" */ './components/restaurant/CuisineIndexComponent.vue')
		},
		{
			path: '/my-meals',
            name: 'restaurant.myMeals.index',
            component: () => import(/* webpackChunkName : "js/kitchen-list" */ './components/restaurant/MealIndexComponent.vue')
		},
		{
			path: '/my-menu-items',
            name: 'restaurant.myMenuItems.index',
            component: () => import(/* webpackChunkName : "js/kitchen-list" */ './components/restaurant/MenuItemIndexComponent.vue')
		},
		

		{
			path: '/my-kitchen',
            name: 'restaurant.myKitchen',
            component: () => import(/* webpackChunkName : "js/kitchen-list" */ './components/restaurant/MyKitchenComponent.vue')
		},
		{
			path: '/my-waiters',
            name: 'restaurant.myWaiters.index',
            component: () => import(/* webpackChunkName : "js/waiter-list" */ './components/restaurant/WaiterIndexComponent.vue')
		},
		{
			path: '/orders',
            name: 'restaurant.orders.index',
            component: () => import(/* webpackChunkName : "js/order-list" */ './components/restaurant/OrderIndexComponent.vue')
		},

		
		{
			path: '/404',
            name: 'restaurant.routeNotFound',
            component: () => import(/* webpackChunkName : "js/not-found-component" */ './pages/NotFoundComponent.vue')
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
