<template>
    <div id="signup-page">
        <Background></Background>
        <SignupLayout :__="__"></SignupLayout>
    </div>
</template>

<script>
import en from '../../translation/auth/en.json';
import ar from '../../translation/auth/ar.json';
import SignupLayout from '../layouts/SignupLayout.vue';
import Background from '../components/signup/Background.vue';

export default {
    name: 'Signup',

    computed: {
        job() {
            return this.$store.state.signup.job
        },

        __() {
            if (this.$store.state.lang === 'en') {
                return en.signup;
            } else {
                return ar.signup;
            }
        }
    },

    watch: {
        job: function (val) {
            if (val === '3')
                this.$store.dispatch('fetchCustomerCategorys');
        },
    },

    beforeMount() {
        this.$store.commit('RESET_SIGNUP_ALL_STATE');
        // add google identity script tag to the body
        const script = document.createElement('script');
        script.src = 'https://accounts.google.com/gsi/client';
        script.async = true;
        script.defer = true;
        script.id = 'gsi-script';
        document.body.appendChild(script);
        // init sign up form
        this.$store.dispatch('initSignup');
        this.$store.commit('SET_SIGNUP_USER_DATA_STATE', {lang: this.$store.state.lang});
    },

    beforeDestroy() {
        // remove script tag from the body
        const script = document.getElementById('gsi-script');
        document.body.removeChild(script);
    },

    mounted() {
        let title = document.querySelector('title');
        let appName = title.innerText.slice(0, title.innerText.indexOf(' | '));

        title.innerText = appName + ' | sign up'
    },

    components: {
        SignupLayout,
        Background,
    },
}
</script>