const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js('resources/js/app.js', 'public/js')
//     .postCss('resources/css/app.css', 'public/css', [
//         //
//     ]);

// Authenticatation app
mix.js('resources/js/auth.js', 'public/assets/js')
    .vue();

mix.sass('resources/sass/auth.scss', 'public/assets/css');

// Store app
mix.js('resources/js/store.js', 'public/assets/js')
    .vue();

mix.sass('resources/sass/store.scss', 'public/assets/css');

// Suppliers app
mix.js('resources/js/dashboards/suppliers.js', 'public/assets/js')
    .vue();

mix.sass('resources/sass/dashboards/suppliers.scss', 'public/assets/css');

// Customers app
mix.js('resources/js/dashboards/customers.js', 'public/assets/js')
    .vue();

mix.sass('resources/sass/dashboards/customers.scss', 'public/assets/css');

// Backoffice users
mix.js('resources/js/dashboards/backoffice.js', 'public/assets/js')
    .vue();

mix.sass('resources/sass/dashboards/backoffice.scss', 'public/assets/css');