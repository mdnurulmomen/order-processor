/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
import App from './views/App.vue';
import Home from './components/admin/HomeComponent.vue';
import Dashboard2 from './components/admin/Dashboard2.vue';
import Dashboard3 from './components/admin/Dashboard3.vue';
import Profile from './components/admin/ProfileComponent.vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);


const router = new VueRouter({
	mode : 'history',
	routes : [
		{
			path: '/home',
            name: 'admin.home',
            component: Home
		},
		{
			path: '/dashboard2',
            name: 'admin.dashboard2',
            component: Dashboard2
		},
		{
			path: '/dashboard3',
            name: 'admin.dashboard3',
            component: Dashboard3
		},
		{
			path: '/profile',
            name: 'admin.profile',
            component: Profile
		},
	],
});


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
