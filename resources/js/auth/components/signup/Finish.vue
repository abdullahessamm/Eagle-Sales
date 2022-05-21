<template>
    <div id="finish" class="w-100 h-100 d-flex justify-content-center">
        <div class="w-100 h-100 d-flex align-items-center justify-content-center flex-column" v-if="$store.state.signup.userData.phone">
            <div class="w-100" v-if="showLoading && !response_error">
                <span class="d-inline-block mb-4"> {{ __.sending }} </span>
                <div class="bar-container m-auto">
                    <BarAnimation :completed="!sending" />
                </div>
            </div>
            <div class="w-100 h-100 d-flex justify-content-center align-items-center" v-if="!showLoading && !response_error">
                <FinishSignupAnimation :__="__" />
            </div>
            <div v-if="response_error" class="res-err">
                <i class="fa-solid fa-circle-xmark"></i> Something went wrong
            </div>
        </div>
        <div class="signup-step" v-if="!$store.state.signup.userData.phone">
            <div class="text-left input-container full-width">
                <label for="phone">Type your phone:</label>
            </div>
            <div :class="`input-container full-width text-before-input mt-2 ${errors.phone ? 'error' : ''}`">
                <span>{{ phonePrefix }}</span>
                <input id="phone" type="text" class="eagle-sales-input" name="phone" placeholder="" v-model="phone">
                <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.phone }} </div>
            </div>
            <div class="input-container">
                <button :class="`eagle-sales-btn ${readyForFinish && !checkingUniques ? '' : 'disabled'}`" @click="sendRequest">
                    <LoadingAnimation  :style="{color: '#fff'}" v-if="checkingUniques"/>
                    <span v-else> Finish </span>
                </button>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    .bar-container {
        height: 10px;
        width: 40%;
    }

    .res-err {
        font-size: 22px;
        color: #f00;
        display: flex;
        align-items: center;

        i {
            font-size: 45px;
            margin: 0 10px;
        }
    }
</style>

<script>
import axios from 'axios';
import BarAnimation from '../BarAnimation.vue';
import FinishSignupAnimation from '../FinishSignupAnimation.vue';

export default {
    name: 'Finish',
    props: ['__'],

    data: () => ({
        readyForFinish: false,
        checkingUniques: false,
        sending: false,
        showLoading: false,
        response_error: false,

        phone: '',

        errors: {
            phone: '',
        },
    }),
    
    computed: {
        apiUrl: () => window.location.apiUrl,

        job() {
            return this.$store.state.signup.job;
        },

        phonePrefix: function () {
            const userCountry = this.$store.state.signup.selectedPlace.country;
            return this.$store.state.signup.availablePlaces.find(place => place.iso_code === userCountry).code;
        },

        refactoredPhone: function () {
            const phonePrefix = this.phonePrefix;
            let phone = this.phone.replace(phonePrefix, '');

            if (phonePrefix === '+20') {
                phone = phone.replace(/^0/, '');
            }


            return phonePrefix + phone;
        },
    },

    watch: {
        phone: function (val) {
            const pattern = /^[0-9]{8,15}$/;

            if (! pattern.test(val))
                this.errors.phone = 'invalid phone number';
            else
                this.errors.phone = '';

            this.readyForFinish = this.errors.phone === '';
        },

        sending: function (val) {
            if (val === true)
                this.showLoading = true;
            else
                setTimeout(() => {
                    this.showLoading = false;
                }, 500);
        },
    },

    methods: {
        async sendRequest () {
            if (this.checkingUniques)
                return;

            if (! this.readyForFinish)
                return;

            // check unique fields
            this.checkingUniques = true;
            await this.checkUniques()
            this.checkingUniques = false;

            if (this.readyForFinish) {
                this.$store.commit('SET_SIGNUP_USER_DATA_STATE', {phone: this.refactoredPhone});
                this.sendFinishRequest();
            }
        },

        async checkUniques () {
            const url = `${this.apiUrl}/accounts/register/check-unique/users`;

            await axios.post(url, {
                phone: this.refactoredPhone,
            })
            .catch(err => {
                if (err.response) {
                    if (err.response.status === 422) {
                        if (err.response.data.message.phone)
                            this.errors.phone = 'Phone is already taken';
                    }
                    else
                        this.errors.phone = 'Something went wrong';
                } else {
                    this.errors.phone = 'Something went wrong, please check your internet connection';
                }

                this.readyForFinish = false;
            })
        },

        sendFinishRequest () {
            switch (this.job) {
                case '0':
                    this.sendSupplierForm();
                    break;
                case '3':
                    this.sendCustomerForm();
                    break;
                case '5':
                    this.sendOnlineClientForm();
                    break;
            }
        },

        sendSupplierForm () {
            this.sending = true;

            const url = `${this.apiUrl}/accounts/register/supplier`;
            const data = {
                ...this.$store.state.signup.userData,
                ...this.$store.state.signup.supplierInfo,
            };

            Object.keys(data).forEach(key => {
                if (data[key] === '')
                    delete data[key];
            });

            axios.put(url, data)
            .catch(err => {
                this.response_error = true;
            })
            .finally(() => { this.sending = false })
        },

        sendCustomerForm () {
            this.sending = true;

            const url = `${this.apiUrl}/accounts/register/customer`;
            const data = {
                ...this.$store.state.signup.userData,
                ...this.$store.state.signup.customerInfo,
            };

            axios.put(url, data)
            .catch(err => {
                this.response_error = true;
            })
            .finally(() => { this.sending = false })
        },

        sendOnlineClientForm () {
            this.sending = true;

            const url = `${this.apiUrl}/accounts/register/client`;
            const data = this.$store.state.signup.userData

            axios.put(url, data)
            .catch(err => {
                this.response_error = true;
            })
            .finally(() => { this.sending = false })
        },
    },

    mounted() {
        if (this.job !== '5')
            this.sendFinishRequest();
    },

    components: {
        BarAnimation,
        FinishSignupAnimation
    },
}
</script>