import {createRouter, createWebHashHistory} from 'vue-router';
import guestMiddleware from './middlewares/guest';

window.jsCookie = require('js-cookie');

const routes = [
    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('./pages/Login.vue'),
        strict: true,
        beforeEnter: guestMiddleware
    },
    {
        path: '/signup',
        name: 'signup',
        component: () => import('./pages/Signup.vue'),
        strict: true,
        beforeEnter: guestMiddleware
    },

    {
        path: '/:param(.*)',
        redirect: '/login',
    }
];

export default createRouter({
    routes,
    history: createWebHashHistory('/auth/'),
    linkActiveClass: 'active-link',
    linkExactActiveClass: 'exact-active-link',
})