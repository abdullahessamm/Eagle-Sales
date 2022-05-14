import { initState } from './state';

export default {
    // TOGGLE_SIGNUP_LOADING_STATE: state => {
    //     state.isLoading = ! state.isLoading
    // },

    SET_SIGNUP_LOADING_STATE: (state, payload) => {
        state.isLoading = payload
    },

    SET_SIGNUP_JOB_STATE: (state, job) => {
        state.job = job
    },

    INCREASE_SIGNUP_STEP_STATE: (state) => {
        state.step++
    },

    DECREASE_SIGNUP_STEP_STATE: (state) => {
        state.step--
    },

    CHANGE_SIGNUP_STEP_STATE: (state, step) => {
        state.step = step
    },
    
    SET_SIGNUP_USER_DATA_STATE: (state, userData) => {
        state.userData = userData
    },

    CHANGE_USERDATA_PROPERTY: (state, payload) => {
        state.userData[payload.property] = payload.value
    },

    SET_SIGNUP_PHONE: (state, phone) => {
        state.userData = {...state.userData, phone}
    },

    SET_SIGNUP_SUPPLIER_INFO_STATE: (state, supplierInfo) => {
        state.supplierInfo = supplierInfo
    },

    SET_SUPPLIER_LOCATION: (state, location) => {
        state.supplierInfo = {...state.supplierInfo, location_coords: location}
    },

    SET_SIGNUP_AVAILABLE_PLACES: (state, availablePlaces) => {
        state.availablePlaces = availablePlaces
    },

    SET_SIGNUP_AVAILABLE_CUSTOMER_CATEGORIES: (state, customerCategories) => {
        state.availableCustomerCategories = customerCategories
    },

    SET_SIGNUP_CUSTOMER_INFO_STATE: (state, customerInfo) => {
        state.customerInfo = customerInfo
    },

    SET_CUSTOMER_LOCATION: (state, location) => {
        state.customerInfo = {...state.customerInfo, location_coords: location}
    },

    SET_SIGNUP_ONLINE_CLIENT_INFO_STATE: (state, onlineClientInfo) => {
        state.onlineClientInfo = onlineClientInfo
    },

    SET_ONLINE_CLIENT_LOCATION: (state, location) => {
        state.onlineClientInfo = {...state.onlineClientInfo, location_coords: location}
    },

    TOGGLE_SIGNUP_IS_ERROS_STATE: state => {
        state.isErrors = ! state.isErrors
    },

    SET_SIGNUP_IS_ERROS_STATE: (state, isError) => {
        state.isErrors = isError
    },

    ADD_SIGNUP_ERROR: (state, error) => {
        state.errors.push(error)
    },

    REMOVE_SIGNUP_ERROR: (state, type) => {
        state.errors = state.errors.filter(error => error.type !== type)
    },

    SET_IP_LOCATION: (state, ipLocation) => {
        state.ipLocation = ipLocation
    },

    RESET_SIGNUP_ALL_STATE: (state) => {
        for (let key in state) {
            state[key] = initState[key]
        }
    },
}