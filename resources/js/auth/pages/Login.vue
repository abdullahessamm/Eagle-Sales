<template>
    <div id="login-page">
        <div class="container-fluid h-100">
            <div class="body">
                <div class="row h-100">
                    <div :class="`col-md-4 d-none d-md-block animate ${$store.state.lang === 'ar' ? 'ar-right' : ''}`" style="z-index: 5">
                        <div class="left-side h-100">
                            <div class="bg-circles">
                                <div class="circle circle-1"></div>
                                <div class="circle circle-2"></div>
                                <div class="circle circle-3"></div>
                            </div>
                            <div class="overlay w-100 h-100 d-flex justify-content-center align-items-center flex-column">
                                <div class="logo mb-4">
                                    <img :src="`${window.location.protocol}//${window.location.host}/assets/images/logo/white_300.png`" alt="white logo" style="width:150px" :class="`animate ${$store.state.lang === 'ar' ? 'flip' : ''}`">
                                </div>
                                <h4 class="pb-3"> {{ __.login.welcome }} </h4>
                                <p class="text-center pt-2">
                                    {{ __.login.description_l1 }} <br>
                                    {{ __.login.description_l2 }}
                                </p>
                                
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-5 d-flex justify-content-center align-content-center flex-column">
                                            <div class="line"></div>
                                        </div>
                                        <div class="col-2 text-center" style="color: #eee; font-size: 20px;"> {{ __.login.or }} </div>
                                        <div class="col-5 d-flex justify-content-center align-content-center flex-column">
                                            <div class="line"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="animated-btn left white mt-4" @click="$router.push('/signup')">
                                    <span> {{ __.login.signup }} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div :class="`col-12 col-md-8 animate ${$store.state.lang === 'ar' ? 'ar-left' : ''}`">
                        <div class="right-side text-center w-100 h-100 position-relative">
                            <div 
                                :class="`animated-btn green right mt-4 position-absolute animate ${$store.state.lang === 'ar' ? 'left' : ''}`"
                                style="right: 10px; top: -10px"
                                :dir="$store.state.lang == 'en' ? 'rtl' : 'ltr'"
                                @click="switchLang"
                            >
                                <span>
                                    <i class="fa-solid fa-earth-asia"></i>
                                    {{ $store.state.lang == 'en' ? 'العربية' : 'English' }}
                                </span>
                            </div>
                            <form @submit.prevent="login" class="d-flex flex-column justify-content-center align-items-center">
                                <h3 class="mb-5" :style="$store.state.lang === 'ar' ? 'letter-spacing: 0 !important' : ''"> <i class="fa-solid fa-right-to-bracket"></i> {{ __.login.title }} </h3>
                                <div class="input-g mb-3">
                                    <div :class="`${usernameError ? 'error position-relative' : 'position-relative'} ${$store.state.lang === 'ar' ? 'flip' : ''}`">
                                        <i class="fa-solid fa-user" v-if="isUsername" style="z-index: 3"></i>
                                        <i :class="`fa-solid fa-at ${$store.state.lang === 'ar' ? 'flip' : ''}`" v-if="! isUsername" style="z-index: 3"></i>
                                        <input :class="$store.state.lang === 'ar' ? 'flip' : ''" type="text" name="username-email" v-model="usernameOrEmail" id="username-email" :placeholder="`${__.login.placeholders.username}`" :style="$store.state.lang === 'ar' ? 'direction: rtl !important' : ''">
                                    </div>
                                    <div :class="`error-msg ${$store.state.lang === 'ar' ? 'text-right' : 'text-left'}`" v-if="usernameError"> <i class="fa-solid fa-circle-exclamation"></i> {{ usernameError }} </div>
                                </div>
                                <div class="input-g mb-3">
                                    <div :class="`${passwordError ? 'error position-relative' : 'position-relative'} ${$store.state.lang === 'ar' ? 'flip' : ''}`">
                                        <i class="fa-solid fa-lock" style="z-index: 3;"></i>
                                        <input :class="$store.state.lang === 'ar' ? 'flip' : ''" :type="showPassword ? 'text' : 'password'" name="password" id="password" v-model="password" :placeholder="`${__.login.placeholders.password}`" :style="$store.state.lang === 'ar' ? 'direction: rtl !important' : ''">
                                        <i class="fa-solid fa-eye" v-if="!showPassword" style="cursor:pointer" @click="togglePasswordVisibility"></i>
                                        <i class="fa-solid fa-eye-slash" v-if="showPassword" style="cursor:pointer" @click="togglePasswordVisibility"></i>
                                    </div>
                                    <div :class="`error-msg ${$store.state.lang === 'ar' ? 'text-right' : 'text-left'}`" v-if="passwordError"> <i class="fa-solid fa-circle-exclamation"></i> {{ passwordError }} </div>
                                </div>
                                <div class="input-g mb-3">
                                    <div class="forgot-password">
                                        <a href="#"> {{ __.login.forgot }} </a>
                                    </div>
                                </div>
                                <div class="input-g">
                                    <button type="submit" :class="isLoading ? 'disabled' : ''">
                                        <LoadingAnimation v-if="isLoading"></LoadingAnimation> {{ __.login.submit }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    $main-color: #247f9c;
    $second-color: #45ba91;
    $circle-color: #339698;

    #login-page {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        overflow-x: hidden;
        overflow-y: auto;

        input[type="text"], input[type="password"] {
            direction: ltr !important;

            &.flip {
                direction: rtl !important;
            }
        }

        .container-fluid {padding-right: 0 !important; padding-left: 0 !important}
        
        .body {
            height: 100%;
            overflow: hidden;
            overflow-y: auto;

            .row {
                --bs-gutter-x: 0;

                .flip {
                        transform: scaleX(-1);
                }

                .animate {
                    transition: all .7s ease-in-out;

                    &.ar-left {
                        transform: translateX(-50%);
                        direction: rtl;
                    }

                    &.ar-right {
                        transform: translateX(200%);
                        direction: rtl;
                    }

                    &.left {
                        right: calc(100% - 125px) !important;
                    }
                }
            }

            .left-side {
                background: linear-gradient(to right, $main-color, $second-color);
                position: relative;
                overflow: hidden;

                .circle {
                    position: absolute;
                    border-radius: 50%;
                    background-color: $circle-color;
                }
                .circle-1 {
                    top: 381px;
                    left: 39px;
                    width: 80px;
                    height: 80px;
                }

                .circle-2 {
                    top: 287px;
                    left: 35px;
                    width: 20px;
                    height: 20px;
                }

                .circle-3 {
                    top: 23%;
                    left: 50%;
                    width: 50px;
                    height: 50px;
                }

                .overlay {
                    position: relative;
                    z-index: 1;
                    color: #fff;

                    h4 {
                        font-family: 'Orelega One', cursive;
                    }

                    .line {
                        border: 1px solid #ccc;
                    }
                }
            }

            .right-side {
                background-color: #fff;

                form {
                    width: 100%;
                    height: 100%;

                    h3 {
                        font-size: 40px;
                        color: $second-color;
                        letter-spacing: 10px;
                        font-family: 'Orelega One', cursive;
                    }

                    .input-g {
                        margin-bottom: 20px;
                        position: relative;
                        width: 70%;
                        max-width: 435px;
                        
                        div.position-relative {
                            i {
                                position: absolute;
                                top: 30%;
                                left: 17px;
                                font-size: 20px;
                                color: #6d6d6d;

                                &:nth-child(3) {
                                    right: 17px;
                                    left: auto;
                                    color: #6d6d6d !important;
                                }
                            }

                            &.error {
                                i {color: #f00}
                                input {border-color: #f00}
                            }
                        }

                        input {
                            width: 100%;
                            height: 50px;
                            border: 1px solid #ccc;
                            border-radius: 10px;
                            padding: 0 43px;
                            font-size: 16px;
                            color: #333;
                            outline: none;
                            transition: border 0.3s ease-in-out;

                            &:focus {
                                border-color: $second-color;
                            }
                        }

                        .error-msg {
                            position: relative;
                            left: 0;
                            width: 100%;
                            font-size: 14px;
                            color: #f00;
                            margin-top: 5px;
                            margin-left: 7px;
                            transition: all 0.3s ease-in-out;
                        }

                        button[type="submit"] {
                            width: 100%;
                            height: 50px;
                            border: none;
                            border-radius: 10px;
                            background-color: $second-color;
                            color: #fff;
                            font-size: 17px;
                            outline: none;
                            cursor: pointer;

                            &.disabled {
                                cursor: not-allowed;
                                opacity: 0.5;
                            }
                        }
                    }
                }
            }
        }
    }
</style>

<script>
import LoadingAnimation from '../components/LoadingAnimation.vue';
import en from '../../translation/auth/en.json';
import ar from '../../translation/auth/ar.json';

export default {
    name: 'Login',

    data: () => ({
        usernameOrEmail: '',
        isUsername: true,
        password: '',
        showPassword: false,
        usernameError: null,
        passwordError: null,
    }),

    computed: {
        window: () => window,
        isLoading() {
            return this.$store.state.login.isLoading;
        },

        serialAccessToken() {
            return this.$store.state.login.serialAccessToken
        },

        __() {
            if (this.$store.state.lang === 'en') {
                return en;
            } else {
                return ar;
            }
        },
    },

    watch: {
        usernameOrEmail: function(val) {
            let emailPattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            let usernamePattern = /^[a-zA-Z0-9]{4,50}$/;

            if (emailPattern.test(val)) {
                this.isUsername = false;
                this.usernameError = null;
            } else {
                this.isUsername = true;
                if (val.length < 4) {
                    this.usernameError = this.__.login.errors.username.minlength;
                } else if (val.length > 50) {
                    this.usernameError = this.__.login.errors.username.maxlength;
                } else if (!usernamePattern.test(val)) {
                    this.usernameError = this.__.login.errors.username.pattern;
                } else {
                    this.usernameError = null;
                }
            }
        },

        password: function(val) {
            let strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@$!%*?&#.]{8,80}$/;

            if (val.length < 8) {
                this.passwordError = this.__.login.errors.password.minlength;
            } else if (val.length > 80) {
                this.passwordError = this.__.login.errors.password.maxlength;
            } else if (!strongPasswordPattern.test(val)) {
                this.passwordError = this.__.login.errors.password.pattern;
            } else {
                this.passwordError = null;
            }
        },

        serialAccessToken: function(val) {
            if (val)
                this.$store.dispatch('getRealToken')
                .catch(err => {
                    console.log(err);
                })
                .finally(() => {
                    this.$store.commit('TOGGLE_LOGIN_LOADING_STATE');
                });
        },
    },

    methods: {
        login: function() {
            if (this.isLoading)
                return

            let data = {
                password: this.password,
            };

            if (this.usernameOrEmail.length === 0 || this.password.length === 0) {
                if (this.usernameOrEmail.length === 0)
                    this.usernameError = this.__.login.errors.username.required;
                
                if (this.password.length === 0)
                    this.passwordError = this.__.login.errors.password.required;
                
                return;
            }

            if (this.usernameError || this.passwordError) {
                return;
            }

            if (this.isUsername) {
                data.username = this.usernameOrEmail;
            } else {
                data.email = this.usernameOrEmail;
            }

            this.$store.commit('TOGGLE_LOGIN_LOADING_STATE');
            this.$store.dispatch('login', data).catch(err => {
                this.$store.commit('TOGGLE_LOGIN_LOADING_STATE');
                if (err.response.status === 401) {
                    this.passwordError = this.__.login.errors.password.invalid;
                } else {
                    this.passwordError = this.__.login.errors.password.unknown;
                }
            })
        },

        togglePasswordVisibility: function() {
            this.showPassword = !this.showPassword;
        },

        switchLang: function() {
            this.usernameError = null;
            this.passwordError = null;
            const currentLang = this.$store.state.lang;
            if (currentLang === 'en') {
                this.$store.dispatch('changeLanguage', 'ar');
            } else {
                this.$store.dispatch('changeLanguage', 'en');
            }
        },
    },

    mounted() {
        let title = document.querySelector('title');
        let appName = title.innerText.slice(0, title.innerText.indexOf(' | '));

        title.innerText = appName + ' | login'

        // focus on username-email input
        this.$nextTick(() => {
            document.querySelector('#username-email').focus();
        });
    },

    components: {
        LoadingAnimation,
    },
}
</script>