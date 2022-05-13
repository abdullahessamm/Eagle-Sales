<template>
    <div id="supplier-info" class="user-info">
        <div :class="`input-container ${errors.shop_name ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="shop_name" placeholder="Shop name (required)" v-model="shop_name">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.shop_name }} </div>
        </div>
        <div :class="`input-container ${errors.category ? 'error' : ''}`">
            <b-form-select :options="selectboxOptions" v-model="category"></b-form-select>
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.category }} </div>
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
    .full-width {width: 81%}
    .disabled {
        background-color: #ccc;
        cursor: not-allowed;
        border-color: #ccc;
    }
</style>

<script>
import LoadingAnimation from '../../LoadingAnimation.vue';
import axios from 'axios';

export default {
    name: 'Supplier',

    data: () => ({
        checkingUniques: false,

        shop_name: '',
        vat_no: '',
        phone: '',
        l1_address: '',
        l1_address_ar: '',
        l2_address: '',
        l2_address_ar: '',
        category: 0,

        errors: {
            shop_name: '',
            vat_no: '',
            phone: '',
            l1_address: '',
            l1_address_ar: '',
            l2_address: '',
            l2_address_ar: '',
            category: '',
        },

        allReady: [
            false,
            false,
            false,
            false,
            false,
            true,
            true,
            false,
        ],

        readyForNext: false,
    }),

    computed: {
        apiUrl: () => window.location.apiUrl,

        selectboxOptions() {
            const categories = this.$store.state.signup.availableCustomerCategories;
            // console.log(categories)
            const options = [
                {
                    text: 'Select Category (required)',
                    value: 0,
                    disabled: true,
                },
            ];

            categories.forEach(category => {
                options.push({
                    text: category.category_name,
                    value: category.id,
                });
            });

            return options;
        },
    },

    watch: {
        shop_name: function (val) {
            const pattern = /^[a-zA-Z0-9أ-ي\s]+$/;

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

            this.allReady[3] = this.errors.l1_address === '';
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

            this.allReady[4] = this.errors.l1_address_ar === '';
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

            this.allReady[5] = this.errors.l2_address === '';
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

            this.allReady[6] = this.errors.l2_address_ar === '';
        },

        category: function (val) {
            if (val === 0)
                this.errors.category = 'Category is required';
            else
                this.errors.category = '';

            this.allReady[7] = this.errors.category === '';
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
                const customerInfo = {
                    shop_name: this.shop_name,
                    vat_no: this.vat_no,
                    category_id: this.category,
                    l1_address: this.l1_address,
                    l1_address_ar: this.l1_address_ar,
                    l2_address: this.l2_address,
                    l2_address_ar: this.l2_address_ar,
                    location_coords: this.$store.state.signup.customerInfo.location_coords,
                }
                this.$store.commit('SET_SIGNUP_CUSTOMER_INFO_STATE', customerInfo);
                this.$store.commit('SET_SIGNUP_PHONE', this.phone);
                this.$store.commit('INCREASE_SIGNUP_STEP_STATE')
            }

        },

        checkUniques: async function () {
            const url = `${this.apiUrl}/accounts/register/check-unique/customers`;

            let data = {
                vat_no: 'SA' + this.vat_no,
                phone: '+966' + this.phone,
                l1_address: this.l1_address,
                l1_address_ar: this.l1_address_ar,
            };

            this.l2_address ? data.l2_address = this.l2_address : null;
            this.l2_address_ar ? data.l2_address_ar = this.l2_address_ar : null;

            await axios.post(url, data)
            .catch(err => {
                if (err.response) {
                    if (err.response.status === 422) {
                        
                        if (err.response.data.message.vat_no) {
                            this.errors.vat_no = 'VAT number is already taken';
                            this.allReady[1] = false;
                        }

                        if (err.response.data.message.phone) {
                            this.errors.phone = 'Phone number is already taken';
                            this.allReady[2] = false;
                        }

                        if (err.response.data.message.l1_address) {
                            this.errors.l1_address = 'Address is already taken';
                            this.allReady[3] = false;
                        }

                        if (err.response.data.message.l1_address_ar) {
                            this.errors.l1_address_ar = 'Address is already taken';
                            this.allReady[4] = false;
                        }

                        if (err.response.data.message.l2_address) {
                            this.errors.l2_address = 'Address is already taken';
                            this.allReady[5] = false;
                        }

                        if (err.response.data.message.l2_address_ar) {
                            this.errors.l2_address_ar = 'Address is already taken';
                            this.allReady[6] = false;
                        }
                    }
                }
            });
        },
    },

    mounted: function () {
        this.shop_name = this.$store.state.signup.customerInfo.shop_name;
        this.vat_no = this.$store.state.signup.customerInfo.vat_no;
        this.phone = this.$store.state.signup.userData.phone;
        this.l1_address = this.$store.state.signup.customerInfo.l1_address;
        this.l1_address_ar = this.$store.state.signup.customerInfo.l1_address_ar;
        this.l2_address = this.$store.state.signup.customerInfo.l2_address;
        this.l2_address_ar = this.$store.state.signup.customerInfo.l2_address_ar;
    },

    components: {
        LoadingAnimation,
    }

}
</script>