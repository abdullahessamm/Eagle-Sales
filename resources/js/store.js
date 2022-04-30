require('./bootstrap');
let Vue = require('vue');
window.Vue = Vue;

store.innerHTML = "<store-index />";

const app = Vue.createApp({
    components: {
        'store-index': require('./store/storeIndex.vue').default,
    },
});

app.mount('#store');