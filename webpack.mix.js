const mix = require('laravel-mix');
require('laravel-mix-svg-vue');


// Auth app
mix.js('resources/js/auth.js', 'public/assets/js')
    .svgVue()
    .options({
        svgVue: {
            svgPath: 'resources/svg',
        }
    });

mix.sass('resources/sass/auth.scss', 'public/assets/css');

// Store UI
mix.js('resources/js/store.js', 'public/assets/js')
    .vue();

mix.sass('resources/sass/store.scss', 'public/assets/css');

// Suppliers dashboard UI
mix.js('resources/js/dashboards/supplier.js', 'public/assets/js')
    .vue();

mix.sass('resources/sass/dashboards/supplier.scss', 'public/assets/css');

// Customers dashboard UI
mix.js('resources/js/dashboards/customer.js', 'public/assets/js')
    .vue();

mix.sass('resources/sass/dashboards/customer.scss', 'public/assets/css');

// Admins dashboard UI
mix.js('resources/js/dashboards/admin.js', 'public/assets/js')
    .vue();

mix.sass('resources/sass/dashboards/admin.scss', 'public/assets/css');