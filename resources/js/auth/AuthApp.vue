<template>
    <div class="auth-app">
        <div v-if="! isSmallScreen">
            <transition name="fade">
                <router-view v-if="! isLoading"></router-view>
            </transition>
            <Loading v-if="isLoading"></Loading>
        </div>
    </div>
</template>

<style lang="scss">
    .fade-enter-active, .fade-leave-active {
        transition: opacity 2s;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
        opacity: 0;
    }

</style>

<script>
import Loading from './pages/Loading.vue'
import jsCookie from 'js-cookie';

export default {
    name: 'AuthApp',

    data: () => ({
        isSmallScreen: false,
    }),

    computed: {
        isLoading() {
            return this.$store.state.isLoading
        }
    },

    methods: {
        checkWidowWidth() {
            if (window.innerWidth < 900) {
                this.isSmallScreen = true;
            } else {
                this.isSmallScreen = false;
            }
        },
    },

    beforeMount() {
        this.isSmallScreen = window.innerWidth < 900;
        window.addEventListener('resize', this.checkWidowWidth);
    },

    mounted() {
        const supportdLanguages = ['en', 'ar'];
        let lang = jsCookie.get('lang');
        if (! supportdLanguages.includes(lang))
            lang = 'en';
        this.$store.commit('SET_LANG', lang)
    },

    beforeUnmount() {
        window.removeEventListener('resize', this.checkWidowWidth);
    },

    components: {
        Loading
    },
}
</script>