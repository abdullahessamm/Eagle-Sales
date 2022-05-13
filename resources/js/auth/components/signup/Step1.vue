<template>
    <div id="step-1" class="signup-step">
        <div :class="`input-container ${errors.f_name ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="f_name" placeholder="First name in English (required)" v-model="f_name">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.f_name }} </div>
        </div>
        <div :class="`input-container ${errors.f_name_ar ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="f_name_ar" placeholder="First name in Arabic (required)" v-model="f_name_ar">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{errors.f_name_ar}} </div>
        </div>
        <div :class="`input-container ${errors.l_name ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="l_name" placeholder="Last name in English (required)" v-model="l_name">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.l_name }} </div>
        </div>
        <div :class="`input-container ${errors.l_name_ar ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="l_name_ar" placeholder="Last name in Arabic (required)" v-model="l_name_ar">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.l_name_ar }} </div>
        </div>
        <div :class="`input-container ${errors.email ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="email" placeholder="Email (required)" v-model="email">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.email }} </div>
        </div>
        <div :class="`input-container ${errors.username ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="username" placeholder="Username (required)" v-model="username">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.username }} </div>
        </div>
        <div :class="`input-container ${errors.password ? 'error' : ''}`">
            <div class="with-popover">
                <transition name="slide">
                    <div class="popover-element left" v-if="showPasswordIndicator">
                        <span class="popover-content">
                            Password must be at least 8 characters long and contain at least one number, one uppercase letter, one lowercase letter and one special character.
                        </span>
                    </div>
                </transition>
                <input type="password" class="eagle-sales-input" name="password" placeholder="Password (required)" v-model="password">
            </div>
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.password }} </div>
        </div>
        <div :class="`input-container ${errors.repeatPassword ? 'error' : ''}`">
            <input type="password" class="eagle-sales-input" name="repeat-password" placeholder="Repeat password (required)" v-model="repeatPassword">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.repeatPassword }} </div>
        </div>
        <div class="input-container">
            <input type="text" class="eagle-sales-input" name="country" :value="`Country: ${country}`" disabled>
        </div>
        <div :class="`input-container ${errors.city ? 'error' : ''}`">
            <b-form-select v-model="city" :options="cities"></b-form-select>
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.city }} </div>
        </div>
        <div class="input-container gender-container text-left d-inline-flex">
            <label class="d-inline-block me-2"> Gender: </label>
            <b-form-group>
                <b-form-radio-group
                    v-model="gender"
                    :options="[{text: 'Male', value: 'male'}, {text: 'Female', value: 'female'}]"
                    name="gender"
                ></b-form-radio-group>
            </b-form-group>
            <label class="d-inline-block me-2" style="color: #888"> (required) </label>
        </div>
        
        <div class="input-container">
            <button :class="`eagle-sales-btn ${readyForNext && !checkingUniques ? '' : 'disabled'}`" @click="gotoNextStep">
                <LoadingAnimation  :style="{color: '#fff'}" v-if="checkingUniques"/>
                <span v-else> Next </span>
            </button>
        </div>
    </div>
</template>

<style lang="css" scoped>
    /* animation */
    .slide-enter-from {
        opacity: 0;
        transform: translateX(-20px);
    }
    .slide-enter-to {
        opacity: 1;
        transform: translateX(0);
    }
    .slide-enter-active {
        transition: all .4s ease-in-out;
    }
    .slide-leave-from {
        opacity: 1;
        transform: translateX(0);
    }
    .slide-leave-to {
        opacity: 0;
        transform: translateX(-20px);
    }
    .slide-leave-active {
        transition: all .4s ease-in-out;
    }

    .disabled {
        background-color: #ccc;
        cursor: not-allowed;
        border-color: #ccc;
    }
</style>

<script>
import axios from 'axios';
import LoadingAnimation from '../LoadingAnimation.vue';

export default {
    name: 'Step1',

    data: () => ({
        checkingUniques: false,
        showPasswordIndicator: false,

        f_name: '',
        f_name_ar: '',
        l_name: '',
        l_name_ar: '',
        email: '',
        username: '',
        password: '',
        repeatPassword: '',
        country: 'SA',
        city: '',
        gender: '',

        errors: {
            f_name: null,
            f_name_ar: null,
            l_name: null,
            l_name_ar: null,
            email: null,
            username: null,
            password: null,
            repeatPassword: null,
            city: null,
            gender: null,
        },

        allReady: [
            false,
            false,
            false,
            false,
            false,
            false,
            false,
            false,
            false,
            false
        ],

        readyForNext: false,
    }),

    computed: {
        apiUrl: () => window.location.apiUrl,
        country: function () {
            return this.$store.state.signup.ipLocation.iso_code;
        },
        cities: function () {
            let cities = [
                {
                    text: 'Select city',
                    value: 0,
                    disabled: true
                },
            ]

            this.$store.state.signup.availableCities.forEach(city => {
                cities.push({
                    text: city.name,
                    value: city.id,
                })
            })
        },
    },

    watch: {
        f_name: function (val) {
            const pattern = /^[a-z]+$/i
            
            if (val === '') {
                this.errors.f_name = 'First name is required'
                this.allReady[0] = false
            }
            else if (! pattern.test(val)) {
                this.errors.f_name = 'First name must be only letters'
                this.allReady[0] = false
            }
            else if (val.length < 2) {
                this.errors.f_name = 'First name must be at least 2 characters'
                this.allReady[0] = false
            }
            else if (val.length > 20) {
                this.errors.f_name = 'First name must be less than 20 characters'
                this.allReady[0] = false
            }
            else {
                this.errors.f_name = null
                this.allReady[0] = true
            }
        },

        f_name_ar: function (val) {
            const pattern = /^[أ-ي]+$/i

            if (val === '') {
                this.errors.f_name_ar = 'First name is required'
                this.allReady[1] = false
            }
            else if (! pattern.test(val)) {
                this.errors.f_name_ar = 'First name must be only Arabic letters'
                this.allReady[1] = false
            }
            else if (val.length < 2) {
                this.errors.f_name_ar = 'First name must be at least 2 characters'
                this.allReady[1] = false
            }
            else if (val.length > 20) {
                this.errors.f_name_ar = 'First name must be less than 20 characters'
                this.allReady[1] = false
            }
            else {
                this.errors.f_name_ar = null
                this.allReady[1] = true
            }
        },

        l_name: function (val) {
            const pattern = /^[a-z]+$/i

            if (val === '') {
                this.errors.l_name = 'Last name is required'
                this.allReady[2] = false
            }
            else if (! pattern.test(val)) {
                this.errors.l_name = 'Last name must be only letters'
                this.allReady[2] = false
            }
            else if (val.length < 2) {
                this.errors.l_name = 'Last name must be at least 2 characters'
                this.allReady[2] = false
            }
            else if (val.length > 20) {
                this.errors.l_name = 'Last name must be less than 20 characters'
                this.allReady[2] = false
            }
            else {
                this.errors.l_name = null
                this.allReady[2] = true
            }
        },

        l_name_ar: function (val) {
            const pattern = /^[أ-ي]+$/i

            if (val === '') {
                this.errors.l_name_ar = 'Last name is required'
                this.allReady[3] = false
            }
            else if (! pattern.test(val)) {
                this.errors.l_name_ar = 'Last name must be only Arabic letters'
                this.allReady[3] = false
            }
            else if (val.length < 2) {
                this.errors.l_name_ar = 'Last name must be at least 2 characters'
                this.allReady[3] = false
            }
            else if (val.length > 20) {
                this.errors.l_name_ar = 'Last name must be less than 20 characters'
                this.allReady[3] = false
            }
            else {
                this.errors.l_name_ar = null
                this.allReady[3] = true
            }
        },

        email: function (val) {
            const pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

            if (val === '') {
                this.errors.email = 'Email is required'
                this.allReady[4] = false
            }
            else if (! pattern.test(val)) {
                this.errors.email = 'Email is not valid'
                this.allReady[4] = false
            }
            else {
                this.errors.email = null
                this.allReady[4] = true
            }
        },

        username: function (val) {
            const pattern = /^[a-zA-Z0-9]+$/

            if (val === '') {
                this.errors.username = 'Username is required'
                this.allReady[5] = false
            }
            else if (! pattern.test(val)) {
                this.errors.username = 'Username must be only letters and numbers'
                this.allReady[5] = false
            }
            else if (val.length < 4) {
                this.errors.username = 'Username must be at least 4 characters'
                this.allReady[5] = false
            }
            else if (val.length > 50) {
                this.errors.username = 'Username must be less than 50 characters'
                this.allReady[5] = false
            }
            else {
                this.errors.username = null
                this.allReady[5] = true
            }
        },

        password: function (val) {
            const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@$!%*?&#.]{8,80}$/

            if (val === '') {
                this.errors.password = 'Password is required'
                this.allReady[6] = false
                this.showPasswordIndicator = false
            }
            else if (! pattern.test(val)) {
                this.errors.password = 'Invalid password'
                this.allReady[6] = false
                this.showPasswordIndicator = true
            }
            else {
                this.errors.password = null
                this.allReady[6] = true
                this.showPasswordIndicator = false
            }

            if (this.repeatPassword !== '' && this.repeatPassword !== val)
                this.errors.repeatPassword = 'Passwords do not match'
            else
                this.errors.repeatPassword = null
        },

        repeatPassword: function (val) {
            if (val === '') {
                this.errors.repeatPassword = 'Repeat password is required';
                this.allReady[7] = false;
            }
            else if (val !== this.password) {
                this.errors.repeatPassword = 'Passwords do not match';
                this.allReady[7] = false;
            }
            else {
                this.errors.repeatPassword = null;
                this.allReady[7] = true;
            }
        },

        city: function (val) {
            const pattern = /^[a-z]+$/i

            if (val === '') {
                this.errors.city = 'City is required'
                this.allReady[8] = false
            }
            else if (! pattern.test(val)) {
                this.errors.city = 'City must be only letters'
                this.allReady[8] = false
            }
            else if (val.length < 3) {
                this.errors.city = 'City must be at least 3 characters'
                this.allReady[8] = false
            }
            else if (val.length > 20) {
                this.errors.city = 'City must be less than 20 characters'
                this.allReady[8] = false
            }
            else {
                this.errors.city = null
                this.allReady[8] = true
            }
        },

        gender: function (val) {
            if (val === '') {
                this.errors.gender = true
                this.allReady[9] = false
            } else {
                this.errors.gender = false
                this.allReady[9] = true
            }
        },

        allReady: {
            handler: function (val) {
                this.readyForNext = val.every(Boolean);
            },
            deep: true
        }
    },

    methods: {
        gotoNextStep: async function () {
            if (this.checkingUniques)
                return;

            if (! this.readyForNext)
                return;
            
            // check unique fields
            this.checkingUniques = true;
            await this.checkUniques()
            this.checkingUniques = false;

            if (this.readyForNext) {
                const userData = {
                    f_name: this.f_name,
                    f_name_ar: this.f_name_ar,
                    l_name: this.l_name,
                    l_name_ar: this.l_name_ar,
                    email: this.email,
                    username: this.username,
                    password: this.password,
                    country: this.country,
                    city: this.city,
                    gender: this.gender,
                    phone: this.$store.state.signup.userData.phone
                }
                this.$store.commit('SET_SIGNUP_USER_DATA_STATE', userData)
                this.$store.commit('INCREASE_SIGNUP_STEP_STATE')
            }
        },

        checkUniques: async function () {
            const url = `${this.apiUrl}/accounts/register/check-unique/users`;

            await axios.post(url, {
                email: this.email,
                username: this.username
            })
            .catch(err => {
                if (err.response) {
                    if (err.response.status === 422) {
                        if (err.response.data.message.email) {
                            this.errors.email = 'Email is already taken'
                            this.allReady[4] = false
                        }

                        if (err.response.data.message.username) {
                            this.errors.username = 'Username is already taken'
                            this.allReady[5] = false
                        }
                    }
                }
            })
        },
    },

    mounted() {
        // assign all userData from store to component state
        this.f_name = this.$store.state.signup.userData.f_name;
        this.f_name_ar = this.$store.state.signup.userData.f_name_ar;
        this.l_name = this.$store.state.signup.userData.l_name;
        this.l_name_ar = this.$store.state.signup.userData.l_name_ar;
        this.email = this.$store.state.signup.userData.email;
        this.username = this.$store.state.signup.userData.username;
        this.password = this.$store.state.signup.userData.password;
        this.country = this.$store.state.signup.userData.country;
        this.city = this.$store.state.signup.userData.city;
        this.gender = this.$store.state.signup.userData.gender;
    },

    components: {
        LoadingAnimation
    }
}
</script>