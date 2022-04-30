import axios from "axios"
import Cookie from 'js-cookie';

function saveUserToCookie(token, serial, job) {
    Cookie.set('_tn', token, { expires: 365 * 60, domain: `.${window.location.hostname.replace('www.', '')}` });
    Cookie.set('_sl', serial, { expires: 365 * 60, domain: `.${window.location.hostname.replace('www.', '')}` });
    Cookie.set('_jb', job, { expires: 365 * 60, domain: `.${window.location.hostname.replace('www.', '')}` });
    window.location.reload();
}

export default {
    login: ({ commit }, payload) => {
        return axios.post("/accounts/login", payload, { baseURL: window.location.apiUrl })
            .then(response => {
                commit("CHANGE_SERIAL_ACCESS_TOKEN_STATE", response.data.token)
            })
    },

    getRealToken: ({ commit, state }) => {
        if (! state.serialAccessToken) {
            throw new Error("No serial access token set in store, you must call login first")
        }
        return axios.get(`/accounts/get-serial/${state.serialAccessToken}`, { baseURL: window.location.apiUrl })
            .then(response => {
                commit("CHANGE_TOKEN_STATE", response.data.token)
                commit("CHANGE_SERIAL_STATE", response.data.serial)
                commit("CHANGE_JOB_STATE", response.data.user.job)
                commit("TOGGLE_AUTHED_STATE")
                commit("CHANGE_SERIAL_ACCESS_TOKEN_STATE", null)
                saveUserToCookie(response.data.token, response.data.serial, response.data.user.job)
            })
    },
}