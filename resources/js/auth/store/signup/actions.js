import axios from 'axios';
import en from '../../../translation/auth/en.json';
import ar from '../../../translation/auth/ar.json';

const apiUrl = window.location.protocol + '//api.' + window.location.hostname.replace(/^www\./, '');

export default {
    fetchCustomerCategorys: async ({ commit }, loadingStateControl=true) => {
        commit('SET_SIGNUP_LOADING_STATE', loadingStateControl);
        commit('REMOVE_SIGNUP_ERROR', 'customer_category');
        await axios.get(`${apiUrl}/accounts/customers/categories/get`)
            .then(response => {
                const { data } = response.data;
                commit('SET_SIGNUP_AVAILABLE_CUSTOMER_CATEGORIES', data);
            })
            .catch(error => {
                commit('ADD_SIGNUP_ERROR', {
                    type: 'customerCategories',
                    message: 'Failed to get customer categories'
                });
            })
            .finally(() => {
                commit('SET_SIGNUP_LOADING_STATE', false);
            })
    },

    initSignup: async ({ commit, dispatch, state }) => {

        commit('SET_SIGNUP_LOADING_STATE', true);


        // get user location
        await axios.get(`${apiUrl}/location/ip-location`)
        .then(response => {
            const { data } = response.data
            commit('SET_IP_LOCATION', data)
        })
        .catch(error => {
            commit('ADD_SIGNUP_ERROR', {
                type: 'ipLocation',
                message: 'Failed to get location'
            })
        })

        // get available countries
        await axios.get(`${apiUrl}/places`)
        .then(response => {
            commit('SET_SIGNUP_AVAILABLE_PLACES', response.data.message)
        })
        .catch(error => {
            commit('ADD_SIGNUP_ERROR', {
                type: 'availablePlaces',
                message: 'Failed to get available places'
            })
        })

        if (state.job === '3')
            await dispatch('fetchCustomerCategorys', false);

        commit('SET_SIGNUP_LOADING_STATE', false);
    },
}