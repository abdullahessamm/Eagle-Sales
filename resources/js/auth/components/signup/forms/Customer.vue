<template>
    <div id="supplier-info" class="user-info">
        <div :class="`input-container full-width ${errors.shop_name ? 'error' : ''}`">
            <input type="text" class="eagle-sales-input" name="shop_name" :placeholder="__.placeholders.shop_name" v-model="shop_name">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.shop_name }} </div>
        </div>
        <div :class="`input-container full-width text-before-input ${errors.phone ? 'error' : ''}`">
            <span>{{ phonePrefix }}</span>
            <input type="text" class="eagle-sales-input" name="phone" :placeholder="__.placeholders.phone" v-model="phone">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.phone }} </div>
        </div>
        <div :class="`input-container ${errors.category ? 'error' : ''}`">
            <b-form-select :options="selectboxOptions" v-model="category"></b-form-select>
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.category }} </div>
        </div>
        <div :class="`input-container text-before-input ${errors.vat_no ? 'error' : ''}`">
            <span style="direction: ltr">{{ vatPrefix }}</span>
            <input type="text" class="eagle-sales-input" name="vat_no" :placeholder="__.placeholders.vat" v-model="vat_no">
            <div class="error-msg"> <i class="fa-solid fa-circle-exclamation"></i> {{ errors.vat_no }} </div>
        </div>
        <div class="input-container">
            <button :class="`eagle-sales-btn ${readyForNext && !checkingUniques ? '' : 'disabled'}`" @click="goToNext">
                <LoadingAnimation  :style="{color: '#fff'}" v-if="checkingUniques"/>
                <span v-else> {{__.buttons.next}} </span>
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
    props: ['__'],

    data: () => ({
        checkingUniques: false,

        shop_name: '',
        vat_no: '',
        phone: '',
        category: 0,

        errors: {
            shop_name: '',
            vat_no: '',
            phone: '',
            category: '',
        },

        allReady: [
            false,
            false,
            false,
            false
        ],

        readyForNext: false,
    }),

    computed: {
        apiUrl: () => window.location.apiUrl,

        refactoredPhone: function () {
            const phonePrefix = this.phonePrefix;
            let phone = this.phone.replace(phonePrefix, '');

            if (phonePrefix === '+20') {
                phone = phone.replace(/^0/, '');
            }


            return phonePrefix + phone;
        },
        
        phonePrefix: function () {
            const userCountry = this.$store.state.signup.selectedPlace.country;
            return this.$store.state.signup.availablePlaces.find(place => place.iso_code === userCountry).code;
        },

        vatPrefix: function () {
            const userCountry = this.$store.state.signup.selectedPlace.country;
            return this.$store.state.signup.availablePlaces.find(place => place.iso_code === userCountry).iso_code;
        },

        refactoredVat: function () {
            const vatPrefix = this.vatPrefix;
            return vatPrefix + this.vat_no;
        },

        selectboxOptions() {
            const categories = this.$store.state.signup.availableCustomerCategories;
            const options = [
                {
                    text: this.__.placeholders.category,
                    value: 0,
                    disabled: true,
                },
            ];

            categories.forEach(category => {
                options.push({
                    text: this.$store.state.lang === 'en' ? category.category_name : category.category_name_ar,
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

        vat_no: function (val) {
            // VAT pattern
            const pattern = /^\d{3,18}$/;

            if (val.length === 0)
                this.errors.vat_no = this.__.errors.vat.required;
            else if (! pattern.test(val))
                this.errors.vat_no = this.__.errors.vat.regex;
            else
                this.errors.vat_no = '';

            this.allReady[1] = this.errors.vat_no === '';
        },

        phone: function (val) {
            const pattern = /^[0-9]{8,15}$/;

            if (val.length === 0)
                this.errors.phone = this.__.errors.phone.required;
            else if (! pattern.test(val))
                this.errors.phone = this.__.errors.phone.regex;
            else
                this.errors.phone = '';

            this.allReady[2] = this.errors.phone === '';
        },

        category: function (val) {
            if (val === 0)
                this.errors.category = this.__.errors.category.required;
            else
                this.errors.category = '';

            this.allReady[3] = this.errors.category === '';
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
                    vat_no: this.refactoredVat,
                    category_id: this.category,
                }
                this.$store.commit('SET_SIGNUP_CUSTOMER_INFO_STATE', customerInfo);
                this.$store.commit('SET_SIGNUP_USER_DATA_STATE', {phone: this.refactoredPhone});
                this.$store.commit('INCREASE_SIGNUP_STEP_STATE')
            }

        },

        checkUniques: async function () {
            const url = `${this.apiUrl}/accounts/register/check-unique/customers`;

            let data = {
                vat_no: this.refactoredVat,
                phone: this.refactoredPhone,
            };

            await axios.post(url, data)
            .catch(err => {
                if (err.response) {
                    if (err.response.status === 422) {
                        
                        if (err.response.data.message.vat_no) {
                            this.errors.vat_no = this.__.errors.vat.duplicate;
                            this.allReady[1] = false;
                        }

                        if (err.response.data.message.phone) {
                            this.errors.phone = this.__.errors.phone.duplicate;
                            this.allReady[2] = false;
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
    },

    components: {
        LoadingAnimation,
    }

}
</script>