import AuthApp from './auth/AuthApp.vue';
import Router from './auth/router.js';
import store from './auth/store/store';
import SvgVue from 'svg-vue3';
import { BootstrapVue3 } from 'bootstrap-vue-3';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css';

// assign subdomains urls to window object
const protocol = window.location.protocol.substring(0, window.location.protocol.length - 1).toLowerCase();
const host = window.location.hostname.replace('www.', '');
window.location.apiUrl = `${protocol}://api.${host}`; // api
window.suppliersDashboardUrl = `${protocol}://suppliers.${host}`; // suppliers dashboard url
window.customersDashboardUrl = `${protocol}://customers.${host}`; // customers dashboard url
window.adminsDashboardUrl    = `${protocol}://admins.${host}`; // admins dashboard url

// init app
const Vue = require('vue');
    
window.Vue = Vue;

document.getElementById('auth-app').innerHTML = `<auth-app></auth-app>`;

const app = Vue.createApp({
    components: {
        AuthApp
    },
});
app.use(Router);
app.use(store);
app.use(BootstrapVue3);
app.use(SvgVue);
app.mount('#auth-app');

// prevent zooming with keyboard
window.onload = function() {
    document.addEventListener('keydown', e => {
        if (e.ctrlKey==true && (e.which == '61' || e.which == '107' || e.which == '173' || e.which == '109'  || e.which == '187'  || e.which == '189'  ) ) {
            e.preventDefault();
         }
    });
}

