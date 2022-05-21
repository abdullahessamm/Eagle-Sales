<template>
    <div id="supplier-info" class="user-info">
        <div :class="`input-container full-width ${errors.shop_name ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="shop_name" :placeholder="__.placeholders.shop_name" v-model="shop_name">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.shop_name }} </div>
        </div>
        <div :class="`input-container text-before-input ${errors.phone ? 'error' : ''}`">
            <span>{{ phonePrefix }}</span>
            <input type="text" class="eagle-sales-input" name="phone" :placeholder="__.placeholders.phone" v-model="phone">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.phone }} </div>
        </div>
        <div :class="`input-container text-before-input ${errors.whatsapp_no ? 'error' : ''}`">
            <span>{{ phonePrefix }}</span>
            <input type="text" class="eagle-sales-input" name="whatsapp_no" :placeholder="__.placeholders.whatsapp" v-model="whatsapp_no">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.whatsapp_no }} </div>
        </div>
        <div :class="`input-container ${errors.fb_page ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="fb_page" :placeholder="__.placeholders.facebook" v-model="fb_page">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.fb_page }} </div>
        </div>
        <div :class="`input-container ${errors.website_domain ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="website_domain" :placeholder="__.placeholders.website" v-model="website_domain">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.website_domain }} </div>
        </div>
        <div class="input-container">
            <button :class="`eagle-sales-btn ${readyForNext && !checkingUniques ? '' : 'disabled'}`" @click="goToNext">
                <LoadingAnimation  :style="{color: '#fff'}" v-if="checkingUniques"/>
                <span v-else> {{ __.buttons.next }} </span>
            </button>
        </div>
    </div>
</template>

<style lang="css" scoped>
    .disabled {
        background-color: #ccc;
        cursor: not-allowed;
        border-color: #ccc;
    }
</style>

<script>
import axios from 'axios';
import LoadingAnimation from '../../LoadingAnimation.vue'

export default {
    name: 'Supplier',
    props: ['__'],

    data: () => ({
        checkingUniques: false,

        shop_name: '',
        phone: '',
        whatsapp_no: '',
        fb_page: '',
        website_domain: '',

        errors: {
            shop_name: '',
            phone: '',
            whatsapp_no: '',
            fb_page: '',
            website_domain: '',
        },

        allReady: [
            false,
            false,
            true,
            true,
            true,
        ],

        readyForNext: false,
    }),

    computed: {
        apiUrl: () => window.location.apiUrl,
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

        refactoredWhatsappNo: function () {
            const phonePrefix = this.phonePrefix;
            let whatsappNo = this.whatsapp_no.replace(phonePrefix, '');

            if (phonePrefix === '+20') {
                whatsappNo = whatsappNo.replace(/^0/, '');
            }

            return phonePrefix + whatsappNo;
        },
    },

    watch: {
        shop_name: function (val) {
            const pattern = /^[a-zA-Z0-9أ-ي\s]+$/;

            if (val.match(/^\s/))
                this.errors.shop_name = this.__.errors.shop_name.invalid;
            else if (val.length === 0)
                this.errors.shop_name = this.__.errors.shop_name.required;
            else if (! pattern.test(val))
                this.errors.shop_name = this.__.errors.shop_name.regex;
            else if (val.length < 3)
                this.errors.shop_name = this.__.errors.shop_name.minlength;
            else if (val.length > 50)
                this.errors.shop_name = this.__.errors.shop_name.maxlength;
            else
                this.errors.shop_name = '';

            this.allReady[0] = this.errors.shop_name === '';
        },

        phone: function (val) {
            const pattern = /^[0-9]{8,15}$/;

            if (! pattern.test(val))
                this.errors.phone = this.__.errors.phone.regex;
            else
                this.errors.phone = '';

            this.allReady[1] = this.errors.phone === '';
        },

        whatsapp_no: function (val) {
            const pattern = /^[0-9]{8,15}$/;

            if (val.length !== 0) {
                if (! pattern.test(val))
                    this.errors.whatsapp_no = this.__.errors.whatsapp.regex;
                else
                    this.errors.whatsapp_no = '';
            } else
                this.errors.whatsapp_no = '';

            this.allReady[2] = this.errors.whatsapp_no === '';
        },

        fb_page: function (val) {
            const pattern = /^(https?:\/\/)?(www\.)?(m\.)?(fb)?(facebook)?(\.com)(\/[\w\D]+\/?)+$/;

            if (val.length !== 0) {
                if (! pattern.test(val))
                    this.errors.fb_page = this.__.errors.facebook.regex;
                else if (val.length > 100)
                    this.errors.fb_page = this.__.errors.facebook.regex;
                else
                    this.errors.fb_page = '';
            } else
                this.errors.fb_page = '';

            this.allReady[3] = this.errors.fb_page === '';
        },

        website_domain: function (val) {
            const pattern = /^(https?:\/\/)?(([\da-z])+\.)?[\d\w\-]+\.[a-z]{2,3}$/;

            if (val.length !== 0) {
                if (! pattern.test(val))
                    this.errors.website_domain = this.__.errors.website.regex;
                else if (val.length > 100)
                    this.errors.website_domain = this.__.errors.website.regex;
                else
                    this.errors.website_domain = '';
            } else
                this.errors.website_domain = '';

            this.allReady[4] = this.errors.website_domain === '';
        },

        allReady: {
            handler: function (val) {
                this.readyForNext = val.every(Boolean);
            },
            deep: true
        },
    },

    methods: {
        goToNext: async function () {
            
            if (this.checkingUniques)
                return;

            if (! this.readyForNext)
                return;

            // check unique fields
            this.checkingUniques = true;
            await this.checkUniques()
            this.checkingUniques = false;
            
            if (this.readyForNext) {
                const supplierInfo = {
                    shop_name: this.shop_name,
                    whatsapp_no: this.whatsapp_no ? this.refactoredWhatsappNo : '',
                    fb_page: this.fb_page,
                    website_domain: this.website_domain,
                }
                this.$store.commit('SET_SIGNUP_SUPPLIER_INFO_STATE', supplierInfo);
                this.$store.commit('SET_SIGNUP_USER_DATA_STATE', {phone: this.refactoredPhone});
                this.$store.commit('INCREASE_SIGNUP_STEP_STATE')
            }

        },

        checkUniques: async function () {
            const url = `${this.apiUrl}/accounts/register/check-unique/suppliers`;

            let data = {
                phone: this.refactoredPhone,
            };

            this.whatsapp_no ? data.whatsapp_no = this.refactoredWhatsappNo : null;
            this.fb_page ? data.fb_page = this.fb_page : null;
            this.website_domain ? data.website_domain = this.website_domain : null;

            await axios.post(url, data)
            .catch(err => {
                if (err.response) {
                    if (err.response.status === 422) {

                        if (err.response.data.message.phone) {
                            this.errors.phone = this.__.errors.phone.duplicate;
                            this.allReady[1] = false;
                        }
                        
                        if (err.response.data.message.whatsapp_no) {
                            this.errors.whatsapp_no = this.__.errors.whatsapp.duplicate;
                            this.allReady[2] = false;
                        }

                        if (err.response.data.message.fb_page) {
                            this.errors.fb_page = this.__.errors.facebook.duplicate;
                            this.allReady[3] = false;
                        }

                        if (err.response.data.message.website_domain) {
                            this.errors.website_domain = this.__.errors.website.duplicate;
                            this.allReady[4] = false;
                        }
                    }
                }
            })
        },
    },

    mounted: function () {
        this.shop_name = this.$store.state.signup.supplierInfo.shop_name;
        this.phone = this.$store.state.signup.userData.phone;
        this.whatsapp_no = this.$store.state.signup.supplierInfo.whatsapp_no;
        this.fb_page = this.$store.state.signup.supplierInfo.fb_page;
        this.website_domain = this.$store.state.signup.supplierInfo.website_domain;
    },

    components: {
        LoadingAnimation
    }
}
</script>