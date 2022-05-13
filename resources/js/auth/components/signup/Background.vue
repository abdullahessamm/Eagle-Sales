<template>
    <div id="signup-background">
        <div :class="`circle circle-1 ${animate ? 'animate' : ''}`"></div>
        <div :class="`circle circle-2 ${animate ? 'animate' : ''}`"></div>
        <div :class="`circle circle-3 ${animate ? 'animate' : ''}`"></div>
        <div :class="`circle circle-4 ${animate ? 'animate' : ''}`"></div>
    </div>
</template>

<style lang="scss" scoped>
$main-color: #247f9c;
$second-color: #45ba91;

#signup-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;

    .circle {
        background: linear-gradient(to right, $main-color, $second-color);
        position: absolute;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        transform: scale(0);
        transition: all 0.5s ease;

        &:last-of-type {
            transform: translate(-120px, 120px);
            &.animate {
                transform: translate(0, 0);
            }
        }

        &:nth-child(2) {
            transform: translate(40px, -40px);
            &.animate {
                transform: translate(0, 0);
            }
        }

        &:nth-child(3) {
            transform: translateX(65px);
            &.animate {
                transform: translate(0, 0);
            }
        }

        &.animate{
            transform: scale(1);
        }
    }

    .circle-1 {
        top: 13%;
        left: 30%;
        width: 50px;
        height: 50px;
        transition-delay: 0s;
    }

    .circle-2 {
        top: -20px;
        right: -20px;
        width: 60px;
        height: 60px;
        transition-delay: 0.5s;
    }

    .circle-3 {
        top: calc(80% - 90px);
        right: -55px;
        width: 120px;
        height: 120px;
        transition-delay: 0.5s;
    }

    .circle-4 {
        bottom: -50px;
        left: -50px;
        width: 170px;
        height: 170px;
        transition-delay: 0.5s;
    }
}
</style>

<script>
export default {
    name: 'SignupBackground',
    data: () => ({
        animate: false
    }),

    mounted() {
        this.animate = true
    }
}
</script>