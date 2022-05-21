<template>
    <div id="step-2" class="signup-step">
        <div class="forms-container">
            <SupplierForm v-if="job === '0'" :__="__"></SupplierForm>
            <CustomerForm v-if="job === '3'" :__="__"></CustomerForm>
        </div>
    </div>
</template>

<script>
import SupplierForm from './forms/Supplier.vue';
import CustomerForm from './forms/Customer.vue';

export default {
    name: 'Step3',
    props: ['__'],

    computed: {
        job() {
            return this.$store.state.signup.job;
        }
    },

    components: {
        SupplierForm,
        CustomerForm,
    },
}
</script>