export default {
    
    TOGGLE_AUTHED_STATE: state => {
        state.isAuthed = ! state.isAuthed
    },

    TOGGLE_LOGIN_LOADING_STATE: state => {
        state.isLoading = ! state.isLoading
    },

    CHANGE_SERIAL_ACCESS_TOKEN_STATE: (state, token) => {
        state.serialAccessToken = token
    },

    CHANGE_TOKEN_STATE: (state, token) => {
        state.token = token
    },

    CHANGE_SERIAL_STATE: (state, serial) => {
        state.serial = serial
    },

    CHANGE_JOB_STATE: (state, job) => {
        state.job = job
    }
}