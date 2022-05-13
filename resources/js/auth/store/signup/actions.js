import axios from 'axios';

const apiUrl = window.location.protocol + '//api.' + window.location.hostname.replace(/^www\./, '');

export default {
    fetchCustomerCategorys: ({ commit }) => {
        commit('SET_SIGNUP_LOADING_STATE', true);
        commit('REMOVE_SIGNUP_ERROR', 'customer_category');
        axios.get(`${apiUrl}/accounts/customers/categories/get`)
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

    initSignup: ({ commit }) => {
        commit('SET_SIGNUP_LOADING_STATE', true);

        // get user location
        axios.get(`${apiUrl}/ip-location`)
        .then(response => {
            const { data } = response.data
            commit('SET_IP_LOCATION', data)
        })
        .catch(error => {
            commit('ADD_SIGNUP_ERROR', {
                type: 'ipLocation',
                message: 'Failed to get location'
            })
        }).finally(() => commit('SET_SIGNUP_LOADING_STATE', false))

        // get available countries
        
    },
}