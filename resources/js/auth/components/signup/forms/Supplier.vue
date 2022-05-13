<template>
    <div id="supplier-info" class="user-info">
        <div :class="`input-container ${errors.shop_name ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="shop_name" placeholder="Shop name (required)" v-model="shop_name">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.shop_name }} </div>
        </div>
        <div :class="`input-container text-before-input ${errors.vat_no ? 'error' : ''}`">
            <span>SA</span>
            <input type="text" class="eagle-sales-input" name="vat_no" placeholder="VAT No. (required)" v-model="vat_no">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.vat_no }} </div>
        </div>
        <div :class="`input-container text-before-input ${errors.phone ? 'error' : ''}`">
            <span>+966</span>
            <input type="text" class="eagle-sales-input" name="phone" placeholder="Phone No. (required)" v-model="phone">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.phone }} </div>
        </div>
        <div :class="`input-container text-before-input ${errors.whatsapp_no ? 'error' : ''}`">
            <span>+966</span>
            <input type="text" class="eagle-sales-input" name="whatsapp_no" placeholder="Whatsapp No." v-model="whatsapp_no">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.whatsapp_no }} </div>
        </div>
        <div :class="`input-container ${errors.fb_page ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="fb_page" placeholder="Facebook page URL" v-model="fb_page">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.fb_page }} </div>
        </div>
        <div :class="`input-container ${errors.website_domain ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="website_domain" placeholder="Website URL" v-model="website_domain">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.website_domain }} </div>
        </div>
        <div :class="`input-container ${errors.l1_address ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="l1_address" placeholder="Address Line 1 (required)" v-model="l1_address">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.l1_address }} </div>
        </div>
        <div :class="`input-container ${errors.l1_address_ar ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="l1_address_ar" placeholder="Address Line 1 in Arabic (required)" v-model="l1_address_ar">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.l1_address_ar }} </div>
        </div>
        <div :class="`input-container ${errors.l2_address ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="l2_address" placeholder="Address Line 2" v-model="l2_address">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.l2_address }} </div>
        </div>
        <div :class="`input-container ${errors.l2_address_ar ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="l2_address_ar" placeholder="Address Line 2 in Arabic" v-model="l2_address_ar">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.l2_address_ar }} </div>
        </div>
        <div class="input-container">
            <button :class="`eagle-sales-btn ${readyForNext && !checkingUniques ? '' : 'disabled'}`" @click="goToNext">
                <LoadingAnimation  :style="{color: '#fff'}" v-if="checkingUniques"/>
                <span v-else> Next </span>
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

    data: () => ({
        checkingUniques: false,

        shop_name: '',
        vat_no: '',
        phone: '',
        whatsapp_no: '',
        fb_page: '',
        website_domain: '',
        l1_address: '',
        l1_address_ar: '',
        l2_address: '',
        l2_address_ar: '',
        location_coords: '',

        errors: {
            shop_name: '',
            vat_no: '',
            phone: '',
            whatsapp_no: '',
            fb_page: '',
            website_domain: '',
            l1_address: '',
            l1_address_ar: '',
            l2_address: '',
            l2_address_ar: '',
        },

        allReady: [
            false,
            false,
            false,
            true,
            true,
            true,
            false,
            false,
            true,
            true,
        ],

        readyForNext: false,
    }),

    computed: {
        apiUrl: () => window.location.apiUrl
    },

    watch: {
        shop_name: function (val) {
            const pattern = /^[a-zA-Z0-9\s]+$/;

            if (val.match(/^\s/))
                this.errors.shop_name = 'Shop name must not start with space';
            else if (val.length === 0)
                this.errors.shop_name = 'Shop name is required';
            else if (! pattern.test(val))
                this.errors.shop_name = 'Shop name must not contain special characters';
            else if (val.length < 3)
                this.errors.shop_name = 'Shop name must be at least 3 characters';
            else if (val.length > 50)
                this.errors.shop_name = 'Shop name must be less than 50 characters';
            else
                this.errors.shop_name = '';

            this.allReady[0] = this.errors.shop_name === '';
        },

        vat_no: function (val) {
            // VAT pattern
            const pattern = /^\d{3,18}$/;

            if (val.match(/^\s/))
                this.errors.vat_no = 'VAT No. must not start with space';
            else if (! pattern.test(val))
                this.errors.vat_no = 'Invalid VAT Number';
            else
                this.errors.vat_no = '';

            this.allReady[1] = this.errors.vat_no === '';
        },

        phone: function (val) {
            const pattern = /^[0-9]{8,15}$/;

            if (! pattern.test(val))
                this.errors.phone = 'invalid phone number';
            else
                this.errors.phone = '';

            this.allReady[2] = this.errors.phone === '';
        },

        whatsapp_no: function (val) {
            const pattern = /^[0-9]{8,15}$/;

            if (val.length !== 0) {
                if (! pattern.test(val))
                    this.errors.whatsapp_no = 'invalid phone number';
                else
                    this.errors.whatsapp_no = '';
            } else
                this.errors.whatsapp_no = '';

            this.allReady[3] = this.errors.whatsapp_no === '';
        },

        fb_page: function (val) {
            const pattern = /^(https?:\/\/)?(www\.)?(m\.)?(fb)?(facebook)?(\.com)(\/[\w\D]+\/?)+$/;

            if (val.length !== 0) {
                if (! pattern.test(val))
                    this.errors.fb_page = 'invalid Facebook page URL';
                else if (val.length > 100)
                    this.errors.fb_page = 'Facebook page URL must be less than 100 characters';
                else
                    this.errors.fb_page = '';
            } else
                this.errors.fb_page = '';

            this.allReady[4] = this.errors.fb_page === '';
        },

        website_domain: function (val) {
            const pattern = /^(https?:\/\/)?(([\da-z])+\.)?[\d\w\-]+\.[a-z]{2,3}$/;

            if (val.length !== 0) {
                if (! pattern.test(val))
                    this.errors.website_domain = 'invalid URL';
                else if (val.length > 100)
                    this.errors.website_domain = 'website URL must be less than 100 characters';
                else
                    this.errors.website_domain = '';
            } else
                this.errors.website_domain = '';

            this.allReady[5] = this.errors.website_domain === '';
        },

        l1_address: function (val) {
            const pattern = /^[a-zA-Z0-9\s]+$/;

            if (val.match(/^\s/))
                this.errors.l1_address = 'Address must not start with space';
            else if (val.length === 0)
                this.errors.l1_address = 'Address is required';
            else if (! pattern.test(val))
                this.errors.l1_address = 'Address must not contain special characters';
            else if (val.length < 4)
                this.errors.l1_address = 'Address must be at least 4 characters';
            else if (val.length > 100)
                this.errors.l1_address = 'Address must be less than 100 characters';
            else
                this.errors.l1_address = '';

            this.allReady[6] = this.errors.l1_address === '';
        },

        l1_address_ar: function (val) {
            const pattern = /^[أ-ي0-9\s]+$/;

            if (val.match(/^\s/))
                this.errors.l1_address_ar = 'Address must not start with space';
            else if (val.length === 0)
                this.errors.l1_address_ar = 'Address is required';
            else if (! pattern.test(val))
                this.errors.l1_address_ar = 'Address must be in Arabic';
            else if (val.length < 4)
                this.errors.l1_address_ar = 'Address must be at least 4 characters';
            else if (val.length > 100)
                this.errors.l1_address_ar = 'Address must be less than 100 characters';
            else
                this.errors.l1_address_ar = '';

            this.allReady[7] = this.errors.l1_address_ar === '';
        },

        l2_address: function (val) {
            const pattern = /^[a-zA-Z0-9\s]+$/;

            if (val.length !== 0) {
                if (val.match(/^\s/))
                    this.errors.l2_address = 'Address must not start with space';
                else if (! pattern.test(val))
                    this.errors.l2_address = 'Address must not contain special characters';
                else if (val.length < 4)
                    this.errors.l2_address = 'Address must be at least 4 characters';
                else if (val.length > 100)
                    this.errors.l2_address = 'Address must be less than 100 characters';
                else
                    this.errors.l2_address = '';
            } else
                this.errors.l2_address = '';

            this.allReady[8] = this.errors.l2_address === '';
        },

        l2_address_ar: function (val) {
            const pattern = /^[أ-ي0-9\s]+$/;

            if (val.length !== 0) {
                if (val.match(/^\s/))
                    this.errors.l2_address_ar = 'Address must not start with space';
                else if (! pattern.test(val))
                    this.errors.l2_address_ar = 'Address must be in Arabic';
                else if (val.length < 4)
                    this.errors.l2_address_ar = 'Address must be at least 4 characters';
                else if (val.length > 100)
                    this.errors.l2_address_ar = 'Address must be less than 100 characters';
                else
                    this.errors.l2_address_ar = '';
            } else
                this.errors.l2_address_ar = '';

            this.allReady[9] = this.errors.l2_address_ar === '';
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
                    vat_no: this.vat_no,
                    whatsapp_no: this.whatsapp_no,
                    fb_page: this.fb_page,
                    website_domain: this.website_domain,
                    l1_address: this.l1_address,
                    l1_address_ar: this.l1_address_ar,
                    l2_address: this.l2_address,
                    l2_address_ar: this.l2_address_ar,
                    location_coords: this.$store.state.signup.supplierInfo.location_coords,
                }
                this.$store.commit('SET_SIGNUP_SUPPLIER_INFO_STATE', supplierInfo);
                this.$store.commit('SET_SIGNUP_PHONE', this.phone);
                this.$store.commit('INCREASE_SIGNUP_STEP_STATE')
            }

        },

        checkUniques: async function () {
            const url = `${this.apiUrl}/accounts/register/check-unique/suppliers`;

            let data = {
                vat_no: 'SA' + this.vat_no,
                phone: '+966' + this.phone,
                l1_address: this.l1_address,
                l1_address_ar: this.l1_address_ar,
            };

            this.whatsapp_no ? data.whatsapp_no = '+966' + this.whatsapp_no : null;
            this.fb_page ? data.fb_page = this.fb_page : null;
            this.website_domain ? data.website_domain = this.website_domain : null;
            this.l2_address ? data.l2_address = this.l2_address : null;
            this.l2_address_ar ? data.l2_address_ar = this.l2_address_ar : null;

            await axios.post(url, data)
            .catch(err => {
                console.log(err.response.data);
                this.allReady[1] = false;
                if (err.response) {
                    if (err.response.status === 422) {
                        
                        if (err.response.data.message.vat_no) {
                            this.errors.vat_no = 'This VAT number already exists';
                            this.allReady[1] = false;
                        }

                        if (err.response.data.message.phone) {
                            this.errors.phone = 'This phone number already exists';
                            this.allReady[2] = false;
                        }
                        
                        if (err.response.data.message.whatsapp_no) {
                            this.errors.whatsapp_no = 'This WhatsApp number already exists';
                            this.allReady[3] = false;
                        }

                        if (err.response.data.message.fb_page) {
                            this.errors.fb_page = 'This Facebook page already exists';
                            this.allReady[4] = false;
                        }

                        if (err.response.data.message.website_domain) {
                            this.errors.website_domain = 'This website domain already exists';
                            this.allReady[5] = false;
                        }

                        if (err.response.data.message.l1_address) {
                            this.errors.l1_address = 'This address already exists';
                            this.allReady[6] = false;
                        }

                        if (err.response.data.message.l1_address_ar) {
                            this.errors.l1_address_ar = 'This address already exists';
                            this.allReady[7] = false;
                        }

                        if (err.response.data.message.l2_address) {
                            this.errors.l2_address = 'This address already exists';
                            this.allReady[8] = false;
                        }

                        if (err.response.data.message.l2_address_ar) {
                            this.errors.l2_address_ar = 'This address already exists';
                            this.allReady[9] = false;
                        }
                    }
                }
            })
        },
    },

    mounted: function () {
        this.shop_name = this.$store.state.signup.supplierInfo.shop_name;
        this.vat_no = this.$store.state.signup.supplierInfo.vat_no;
        this.phone = this.$store.state.signup.userData.phone;
        this.whatsapp_no = this.$store.state.signup.supplierInfo.whatsapp_no;
        this.fb_page = this.$store.state.signup.supplierInfo.fb_page;
        this.website_domain = this.$store.state.signup.supplierInfo.website_domain;
        this.l1_address = this.$store.state.signup.supplierInfo.l1_address;
        this.l1_address_ar = this.$store.state.signup.supplierInfo.l1_address_ar;
        this.l2_address = this.$store.state.signup.supplierInfo.l2_address;
        this.l2_address_ar = this.$store.state.signup.supplierInfo.l2_address_ar;
    },

    components: {
        LoadingAnimation
    }
}
</script>