<template>
    <div id="step-1" class="signup-step">
        <div :class="`input-container ${errors.f_name ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="f_name" :placeholder="__.placeholders.f_name" v-model="f_name">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.f_name }} </div>
        </div>
        <div :class="`input-container ${errors.l_name ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="l_name" :placeholder="__.placeholders.l_name" v-model="l_name">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.l_name }} </div>
        </div>
        <div :class="`input-container full-width ${errors.email ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="email" :placeholder="__.placeholders.email" v-model="email">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.email }} </div>
        </div>
        <div :class="`input-container full-width ${errors.password ? 'error' : ''}`">
            <div class="with-popover">
                <transition name="slide">
                    <div class="popover-element bottom" v-if="showPasswordIndicator">
                        <span class="popover-content">
                            {{ __.errors.password.pattern }}
                        </span>
                    </div>
                </transition>
                <input id ="password" type="password" class="eagle-sales-input" name="password" :placeholder="__.placeholders.password" v-model="password">
            </div>
        </div>
        <div class="input-container gender-container text-left d-inline-flex">
            <label class="d-inline-block me-2"> {{ __.placeholders.gender.title }}: </label>
            <b-form-group>
                <b-form-radio-group
                    v-model="gender"
                    :options="[{value: 'male', text: __.placeholders.gender.male}, {value: 'female', text: __.placeholders.gender.female}]"
                    name="gender"
                ></b-form-radio-group>
            </b-form-group>
            <label class="d-inline-block me-2" style="color: #888"> ({{__.placeholders.gender.required}}) </label>
        </div>
        
        <div class="input-container">
            <button :class="`eagle-sales-btn circle ${readyForNext && !checkingUniques ? '' : 'disabled'}`" @click="gotoNextStep">
                <LoadingAnimation  :style="{color: '#fff'}" v-if="checkingUniques"/>
                <span v-else> {{ __.buttons.next }} </span>
            </button>
        </div>

        <div class="quick-fill-social" v-if="true">
            <div class="text-center mt-3 mb-3">
                <span class="text-muted">
                    {{ __.or}}
                </span>
            </div>
            <div class="text-center">
                <div class="d-inline-block m-auto">
                    <div id="google-btn"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="css" scoped>
    /* animation */
    .slide-enter-from {
        opacity: 0;
        transform: translateY(20px);
    }
    .slide-enter-to {
        opacity: 1;
        transform: translateY(0);
    }
    .slide-enter-active {
        transition: all .4s ease-in-out;
    }
    .slide-leave-from {
        opacity: 1;
        transform: translateY(0);
    }
    .slide-leave-to {
        opacity: 0;
        transform: translateY(20px);
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

    props: ['__'],

    data: () => ({
        checkingUniques: false,
        showPasswordIndicator: false,

        f_name: '',
        l_name: '',
        email: '',
        password: '',
        gender: '',

        errors: {
            f_name: null,
            l_name: null,
            email: null,
            password: null,
            gender: null,
        },

        allReady: [
            false,
            false,
            false,
            false,
            false,
        ],

        readyForNext: false,
    }),

    computed: {
        apiUrl: () => window.location.apiUrl,
    },

    watch: {
        f_name: function (val) {
            const pattern = /^[a-zأ-ي]+$/i
            
            if (val === '') {
                this.errors.f_name = this.__.errors.f_name.required;
                this.allReady[0] = false
            }
            else if (! pattern.test(val)) {
                this.errors.f_name = this.__.errors.f_name.regex;
                this.allReady[0] = false
            }
            else if (val.length < 2) {
                this.errors.f_name = this.__.errors.f_name.minlength;
                this.allReady[0] = false
            }
            else if (val.length > 20) {
                this.errors.f_name = this.__.errors.f_name.maxlength;
                this.allReady[0] = false
            }
            else {
                this.errors.f_name = null
                this.allReady[0] = true
            }
        },

        l_name: function (val) {
            const pattern = /^[a-zأ-ي]+$/i

            if (val === '') {
                this.errors.l_name = this.__.errors.l_name.required;
                this.allReady[1] = false
            }
            else if (! pattern.test(val)) {
                this.errors.l_name = this.__.errors.l_name.regex;
                this.allReady[1] = false
            }
            else if (val.length < 2) {
                this.errors.l_name = this.__.errors.l_name.minlength;
                this.allReady[1] = false
            }
            else if (val.length > 20) {
                this.errors.l_name = this.__.errors.l_name.maxlength;
                this.allReady[1] = false
            }
            else {
                this.errors.l_name = null
                this.allReady[1] = true
            }
        },

        email: function (val) {
            const pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

            if (val === '') {
                this.errors.email = this.__.errors.email.required;
                this.allReady[2] = false
            }
            else if (! pattern.test(val)) {
                this.errors.email = this.__.errors.email.regex;
                this.allReady[2] = false
            }
            else {
                this.errors.email = null
                this.allReady[2] = true
            }
        },

        password: function (val) {
            const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@$!%*?&#.]{8,80}$/

            if (val === '') {
                this.errors.password = this.__.errors.password.required;
                this.allReady[3] = false
                this.showPasswordIndicator = false
            }
            else if (! pattern.test(val)) {
                this.errors.password = this.__.errors.password.regex;
                this.allReady[3] = false
                this.showPasswordIndicator = true
            }
            else {
                this.errors.password = null
                this.allReady[3] = true
                this.showPasswordIndicator = false
            }
        },

        gender: function (val) {
            if (val === '') {
                this.errors.gender = true
                this.allReady[4] = false
            } else {
                this.errors.gender = false
                this.allReady[4] = true
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
                    l_name: this.l_name,
                    email: this.email,
                    password: this.password,
                    gender: this.gender,
                }
                this.$store.commit('SET_SIGNUP_USER_DATA_STATE', userData)
                this.$store.commit('INCREASE_SIGNUP_STEP_STATE')
            }
        },

        checkUniques: async function () {
            const url = `${this.apiUrl}/accounts/register/check-unique/users`;

            await axios.post(url, {
                email: this.email,
            })
            .catch(err => {
                if (err.response) {
                    if (err.response.status === 422) {
                        if (err.response.data.message.email) {
                            this.errors.email = this.__.errors.email.duplicate;
                            this.allReady[2] = false
                        }
                    }
                }
            })
        },

        fillWithGoogle: function (response) {
            const jwt = response.credential;
            axios.get(`https://oauth2.googleapis.com/tokeninfo?id_token=${jwt}`)
            .then(res => this.handleGoogleResponse(res.data))
            .catch(err => console.log(err))
        },

        handleGoogleResponse: function (response) {
            this.f_name = response.given_name;
            this.l_name = response.family_name;
            this.email = response.email;
            
            document.getElementById('password').focus();
        },
    },

    mounted() {
        // assign all userData from store to component state
        this.f_name = this.$store.state.signup.userData.f_name;
        this.l_name = this.$store.state.signup.userData.l_name;
        this.email = this.$store.state.signup.userData.email;
        this.password = this.$store.state.signup.userData.password;
        this.gender = this.$store.state.signup.userData.gender;

        // fill with google
        google.accounts.id.initialize({
            client_id: '1027306294925-brhrg0uvdseqoimerpgmo14mmqnahqpj.apps.googleusercontent.com',
            callback: this.fillWithGoogle
        })

        const googleBtn = document.getElementById('google-btn');
        google.accounts.id.renderButton(googleBtn, {
            theme: 'filled_blue',
            size: "large",
            text: 'continue_with',
            shape: 'circle',
            width: '200'
        })
    },

    components: {
        LoadingAnimation
    }
}
</script>