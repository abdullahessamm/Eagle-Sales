import { initState } from './state';

export default {

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

    SET_SIGNUP_SELECTED_PLACE: (state, place) => {
        state.selectedPlace = { ...state.selectedPlace, ...place }
    },
    
    SET_SIGNUP_USER_DATA_STATE: (state, userData) => {
        state.userData = { ...state.userData, ...userData }
    },

    SET_SIGNUP_SUPPLIER_INFO_STATE: (state, supplierInfo) => {
        state.supplierInfo = { ...state.supplierInfo, ...supplierInfo }
    },

    SET_SIGNUP_AVAILABLE_PLACES: (state, availablePlaces) => {
        state.availablePlaces = availablePlaces
    },

    SET_SIGNUP_AVAILABLE_CUSTOMER_CATEGORIES: (state, customerCategories) => {
        state.availableCustomerCategories = customerCategories
    },

    SET_SIGNUP_CUSTOMER_INFO_STATE: (state, customerInfo) => {
        state.customerInfo = { ...state.customerInfo, ...customerInfo }
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