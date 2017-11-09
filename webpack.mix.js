let mix = require('laravel-mix');

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

mix.autoload({
    jquery: ['jquery', 'jQuery', '$', 'window.jQuery'],
    'popper.js': ['Popper', 'window.Popper'],
}).js('resources/assets/js/app.js', 'public/js').extract([
    'jquery', 'popper.js', 'bootstrap', 'axios', 'lodash', 'vue', 'vue-notification', 'laravel-echo', 'pusher-js'
])
    .sass('resources/assets/sass/app.scss', 'public/css');

if (mix.inProduction()) {
    mix.version();
}
