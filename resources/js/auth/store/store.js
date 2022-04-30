import { createStore } from 'vuex';
import login from './login/login';
import signup from './signup/signup';

export default createStore({
    state: {
        isLoading: false,
    },
    mutations: {
        TOGGLE_LOADING_STATE: state => {
            state.isLoading = ! state.isLoading
        }
    },
    modules: {
        login, signup
    },
});