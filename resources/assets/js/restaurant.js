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

import App from './pages/RestaurantSideMenuBar.vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

const router = new VueRouter({
	mode : 'history',
	routes : [
		{
			path: '/home',
            name: 'home',
            component: () => import(/* webpackChunkName : "js/home-component" */ './pages/restaurant/Home.vue')
		},
		{
			path: '/profile',
            name: 'profile',
            component: () => import(/* webpackChunkName : "js/profile" */ './pages/restaurant/Profile.vue')
		},
		{
			path: '/settings',
            name: 'setting',
            component: () => import(/* webpackChunkName : "js/settings" */ './pages/restaurant/Setting.vue')
		},


		{
			path: '/my-meals',
            name: 'my-meals',
            component: () => import(/* webpackChunkName : "js/my-meals-list" */ './pages/restaurant/MealIndex.vue')
		},
		{
			path: '/my-cuisines',
            name: 'my-cuisines',
            component: () => import(/* webpackChunkName : "js/my-cuisines-list" */ './pages/restaurant/CuisineIndex.vue')
		},
		{
			path: '/my-menu-items',
            name: 'my-menu-items',
            component: () => import(/* webpackChunkName : "js/my-menu-items-list" */ './pages/restaurant/MenuItemIndex.vue')
		},
		{
			path: '/my-menu-categories',
            name: 'my-menu-categories',
            component: () => import(/* webpackChunkName : "js/my-menu-categories-list" */ './pages/restaurant/MenuCategoryIndex')
		},
		

		{
			path: '/my-kitchen',
            name: 'my-kitchen',
            component: () => import(/* webpackChunkName : "js/my-kitchen" */ './pages/restaurant/MyKitchen.vue')
		},
		{
			path: '/my-waiters',
            name: 'my-waiters',
            component: () => import(/* webpackChunkName : "js/my-waiters-list" */ './pages/restaurant/WaiterIndex.vue')
		},
		{
			path: '/orders',
            name: 'orders',
            component: () => import(/* webpackChunkName : "js/order-list" */ './pages/restaurant/OrderIndex.vue')
		},

		
		{
			path: '*',
            redirect : '/404'
		},
		{
			path: '/404',
            name: 'routeNotFound',
            component: () => import(/* webpackChunkName : "js/not-found-component" */ './pages/NotFound.vue')
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
