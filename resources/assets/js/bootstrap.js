window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    // window.$ = window.jQuery = require('jquery');

    // require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// apply interceptor on response
axios.interceptors.response.use(
    
    response => response,
    
    error => {

        // check for errorHandle config
        // if( error.config.hasOwnProperty('errorHandle') && error.config.errorHandle === false ) {
        //     return Promise.reject(error);
        // }

        // if the request was made and the server responded with a status code that falls out of the range of 2xx
        if (error.response) {

            if (error.response.status === 401) {
                window.localStorage.removeItem('roles');
                window.localStorage.removeItem('permissions');
                window.location.href = "/login";
            }
            else if (error.response.status === 403) {
                location.reload();
                // window.location.href = "/403"
            }

        }

        return Promise.reject(error);

    }

);

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true,
    // forceTLS: true
});
