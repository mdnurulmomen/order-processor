const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

/*
mix.babelConfig({
  	plugins: ['@babel/plugin-syntax-dynamic-import'],
});
*/

mix.webpackConfig(webpack => {
    return {
        output: {
            publicPath: '/',
            filename: '[name].js', 
            chunkFilename: 'js/[name].js',
        },
    };
});

mix.js('resources/assets/js/admin.js', 'public/js')
   .js('resources/assets/js/restaurant.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');
