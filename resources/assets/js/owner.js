/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

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

import App from './pages/MerchantSideMenuBar.vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

// Registering component globally
Vue.component('pagination', require('./pages/Pagination.vue').default);

const router = new VueRouter({
	mode : 'history',
	routes : [
		{
			path: '/home',
            name: 'home',
            component: () => import(/* webpackChunkName : "js/home-component" */ './pages/owner/Home.vue')
		},
		{
			path: '/profile',
            name: 'profile',
            component: () => import(/* webpackChunkName : "js/profile" */ './pages/owner/Profile.vue')
		},
		{
			path: '/settings',
            name: 'setting',
            component: () => import(/* webpackChunkName : "js/settings" */ './pages/owner/Setting.vue')
		},

		{
			path: '/my-meals',
            name: 'my-meals',
            component: () => import(/* webpackChunkName : "js/my-meals-list" */ './pages/owner/MealIndex.vue')
		},
		{
			path: '/my-cuisines',
            name: 'my-cuisines',
            component: () => import(/* webpackChunkName : "js/my-cuisines-list" */ './pages/owner/CuisineIndex.vue')
		},
		{
			path: '/my-products',
            name: 'my-products',
            component: () => import(/* webpackChunkName : "js/my-products-list" */ './pages/owner/MyProductIndex.vue')
		},
		{
			path: '/my-product-categories',
            name: 'my-product-categories',
            component: () => import(/* webpackChunkName : "js/my-product-categories-list" */ './pages/owner/ProductCategoryIndex')
		},
		

		{
			path: '/my-kitchen',
            name: 'my-kitchen',
            component: () => import(/* webpackChunkName : "js/my-kitchen" */ './pages/owner/MyKitchen.vue')
		},
		{
			path: '/my-agents',
            name: 'my-agents',
            component: () => import(/* webpackChunkName : "js/my-agents-list" */ './pages/owner/AgentIndex.vue')
		},
		{
			path: '/orders',
            name: 'orders',
            component: () => import(/* webpackChunkName : "js/order-list" */ './pages/owner/OrderIndex.vue')
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