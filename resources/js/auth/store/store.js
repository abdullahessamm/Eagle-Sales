import { createStore } from 'vuex';
import login from './login/login';
import signup from './signup/signup';
import jsCookie from 'js-cookie';

export default createStore({
    state: {
        isLoading: false,
        lang: 'en'
    },
    mutations: {
        TOGGLE_LOADING_STATE: state => {
            state.isLoading = ! state.isLoading
        },
        SET_LANG: (state, lang) => {
            state.lang = lang
        },
    },
    actions: {
        async changeLanguage({ commit }, lang) {
            await jsCookie.set('lang', lang, { expires: 365*60, domain: `.${window.location.hostname.replace('www.', '')}` });
            commit('SET_LANG', lang);
        }
    },
    modules: {
        login, signup
    },
});