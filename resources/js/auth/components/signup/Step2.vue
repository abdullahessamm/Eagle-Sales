<template>
    <div id="step-2" class="signup-step">
        <div class="forms-container">
            <SupplierForm v-if="job === '0'"></SupplierForm>
            <CustomerForm v-if="job === '3'"></CustomerForm>
            <OnlineClientForm v-if="job === '5'"></OnlineClientForm>
        </div>
    </div>
</template>

<script>
import SupplierForm from './forms/Supplier.vue';
import CustomerForm from './forms/Customer.vue';
import OnlineClientForm from './forms/OnlineClient.vue';

export default {
    name: 'Step2',

    computed: {
        job() {
            return this.$store.state.signup.job;
        }
    },

    components: {
        SupplierForm,
        CustomerForm,
        OnlineClientForm,
    },
}
</script>