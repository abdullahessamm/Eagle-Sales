<template>
    <section id="loading-page" @mousewheel="e => preventZoom(e)">
        <img :src="`${window.location.protocol}//${window.location.host}/assets/images/logo/full_logo_colored.png`" alt="logo">
        <LoadingAnimation :style="animationStyle"></LoadingAnimation>
        <transition name="fade">
            <p v-if="showHint"> slow connection, please wait... </p>
        </transition>
    </section>
</template>

<style lang="scss" scoped>

#loading-page {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: #fff;
    z-index: 999;

    img {
        width: 90px;
    }

    p {
        color: #497096;
        margin-top: 5px;
        max-width: 70%;
    }

    .fade-enter-active {
        transition: opacity .5s;
    }

    .fade-enter {
        opacity: 0;
    }

    .fade-enter-to {
        opacity: 1;
    }
}

</style>
<script>
import LoadingAnimation from '../components/LoadingAnimation.vue';

export default {
    name: 'loadingPage',

    data: () => ({
        animationStyle: {
            "margin-top": "30px",
            width: "30px",
            height: "30px",
        },

        showHint: false
    }),

    computed: {
        window() {
            return window;
        }
    },

    methods: {
        preventZoom(e) {
            if (e.ctrlKey)
                e.preventDefault();
        }
    },

    mounted() {
        setTimeout(() => {
            this.showHint = true;
        }, 5000);
    },

    components: {
        LoadingAnimation
    }
}
</script>