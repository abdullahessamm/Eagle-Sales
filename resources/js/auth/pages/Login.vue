<template>
    <div id="login-page">
        <div class="container">
            <div class="header">
                <div class="row">
                    <div class="col-6 left-side">
                        <img src="assets/images/logo/h_colored_logo.png" alt="logo" draggable="false">
                    </div>
                    <div class="col-6">
                        <div class="right-side h-100">
                            <div class="h-100 d-flex align-items-center justify-content-end">
                                <div class="animated-btn">
                                    <span>العربية</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="body">
                <div class="row h-100">
                    <div class="col-md-4 d-none d-md-block">
                        <div class="left-side h-100">
                            <div class="bg-circles">
                                <div class="circle circle-1"></div>
                                <div class="circle circle-2"></div>
                                <div class="circle circle-3"></div>
                            </div>
                            <div class="overlay w-100 h-100 d-flex justify-content-center align-items-center flex-column">
                                <div class="logo mb-4">
                                    <img src="assets/images/logo/white_300.png" alt="white logo" width="150px">
                                </div>
                                <h4 class="pb-3"> Welcome Back! </h4>
                                <p class="text-center pt-2">
                                    To keep connected with us, <br>
                                    please login with your personal info
                                </p>
                                
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-5 d-flex justify-content-center align-content-center flex-column">
                                            <div class="line"></div>
                                        </div>
                                        <div class="col-2 text-center" style="color: #eee; font-size: 20px;">OR</div>
                                        <div class="col-5 d-flex justify-content-center align-content-center flex-column">
                                            <div class="line"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="animated-btn left white mt-4" @click="$router.push('/signup')">
                                    <span> Sign up </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="right-side text-center w-100 h-100">
                            <form @submit.prevent="login" class="d-flex flex-column justify-content-center align-items-center">
                                <h3 class="mb-5"> <i class="fa-solid fa-right-to-bracket"></i> Login </h3>
                                <div class="input-g mb-3">
                                    <div :class="usernameError ? 'error position-relative' : 'position-relative'">
                                        <i class="fa-solid fa-user" v-if="isUsername"></i>
                                        <i class="fa-solid fa-at" v-if="! isUsername"></i>
                                        <input type="text" name="username-email" v-model="usernameOrEmail" id="username-email" placeholder="Username or email...">
                                    </div>
                                    <div class="error-msg text-left" v-if="usernameError"> <i class="fa-solid fa-circle-exclamation"></i> {{ usernameError }} </div>
                                </div>
                                <div class="input-g mb-3">
                                    <div :class="passwordError ? 'error position-relative' : 'position-relative'">
                                        <i class="fa-solid fa-lock"></i>
                                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password" v-model="password" placeholder="Password...">
                                        <i class="fa-solid fa-eye" v-if="!showPassword" style="cursor:pointer" @click="showPassword = !showPassword"></i>
                                        <i class="fa-solid fa-eye-slash" v-if="showPassword" style="cursor:pointer" @click="showPassword = !showPassword"></i>
                                    </div>
                                    <div class="error-msg text-left" v-if="passwordError"> <i class="fa-solid fa-circle-exclamation"></i> {{ passwordError }} </div>
                                </div>
                                <div class="input-g mb-3">
                                    <div class="forgot-password">
                                        <a href="#"> Forgot password? </a>
                                    </div>
                                </div>
                                <div class="input-g">
                                    <button type="submit" :class="isLoading ? 'disabled' : ''">
                                        <LoadingAnimation v-if="isLoading"></LoadingAnimation> Login
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

        .header {
            margin: 20px 0 30px 0;
            height: 51.42px;

            .left-side {
                img {
                    width: 100%;
                    max-width: 200px;
                }
            }
        }
        
        .body {
            height: 660px;
            border: 1px solid $second-color;
            border-radius: 20px;
            overflow: hidden;

            .row {
                --bs-gutter-x: 0;
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
                            transition: all 0.3s ease-in-out;

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
                            letter-spacing: 5px;
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
        isLoading() {
            return this.$store.state.login.isLoading;
        },

        serialAccessToken() {
            return this.$store.state.login.serialAccessToken
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
                    this.usernameError = 'Username must be at least 4 characters long';
                } else if (val.length > 50) {
                    this.usernameError = 'Username must be less than 50 characters long';
                } else if (!usernamePattern.test(val)) {
                    this.usernameError = 'Username must contain only letters and numbers';
                } else {
                    this.usernameError = null;
                }
            }
        },

        password: function(val) {
            let strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@$!%*?&#.]{8,80}$/;

            if (val.length < 8) {
                this.passwordError = 'Password must be at least 8 characters long';
            } else if (val.length > 80) {
                this.passwordError = 'Password must be less than 80 characters long';
            } else if (!strongPasswordPattern.test(val)) {
                this.passwordError = 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character';
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
                    this.usernameError = 'Username or email is required';
                
                if (this.password.length === 0)
                    this.passwordError = 'Password is required';
                
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
                    this.passwordError = 'Invalid password';
                } else {
                    this.passwordError = 'An error occurred';
                }
            })
        },

        test: function() {
            this.$router.push('/');
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