<template>
    <div id="signup-page">
        <Background></Background>
        <SignupLayout></SignupLayout>
    </div>
</template>

<script>
import SignupLayout from '../layouts/SignupLayout.vue';
import Background from '../components/signup/Background.vue';

export default {
    name: 'Signup',

    computed: {
        job() {
            return this.$store.state.signup.job
        },
    },

    beforeMount() {
        this.$store.dispatch('initSignup');
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