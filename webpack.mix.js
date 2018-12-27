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
mix.options({
    extractVueStyles: false,
    processCssUrls: true,
    terser: {
        terserOptions: {
            output: {
                comments: false,
            },
            ecma: 5,
            warnings: false,
            parse: {},
            compress: {},
            mangle: true,
            module: false,
            toplevel: false,
            nameCache: null,
            ie8: true,
            keep_classnames: undefined,
            keep_fnames: false,
            safari10: true,
        },
    },
    purifyCss: false,
    //purifyCss: {},
    postCss: [require('autoprefixer')],
    clearConsole: true
});

mix.autoload({
    jquery: ['jquery', 'jQuery', '$', 'window.jQuery'],
    'popper.js': ['Popper'],
}).js('resources/assets/js/app.js', 'public/js').extract([
    'jquery', 'popper.js', 'bootstrap', 'axios', 'lodash', 'vue', 'vue-notification', 'laravel-echo', 'pusher-js'
])
    .sass('resources/assets/sass/app.scss', 'public/css');

if (mix.inProduction()) {
    mix.version();
}
