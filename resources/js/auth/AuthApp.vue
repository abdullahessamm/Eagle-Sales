<template>
    <div class="auth-app">
        <router-view v-if="! isLoading"></router-view>
        <Loading v-else></Loading>
    </div>
</template>

<script>
import Loading from './pages/Loading.vue'

export default {
    name: 'AuthApp',
    computed: {
        isLoading() {
            return this.$store.state.isLoading
        }
    },

    components: {
        Loading
    },
}
</script>