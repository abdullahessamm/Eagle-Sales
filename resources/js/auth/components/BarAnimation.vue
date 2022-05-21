<template>
    <div id="bar-animation" class="h-100">
        <div class="track">
            <div :class="`bar ${start ? 'w-75' : ''} ${completed ? 'w-100' : ''}`"></div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
@import '../../../sass/auth/colors.scss';

    .track {
        width: 100%;
        height: 100%;
        position: relative;
        overflow: hidden;
        border-radius: 200px;
        background-color: #e6e6e6;
    }

    .bar {
        width: 0;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        background-color: $second-color;
        border-radius: 200px;
        transition: width 5s ease-out;
    }

    .w-75 {
        width: 75%;
    }

    .w-100 {
        transition-duration: 0.3s;
        width: 100%;
    }
</style>
    
<script>
export default {
    name: "BarAnimation",

    props: ['completed'],

    data: () => ({
        start: false,
    }),

    mounted() {
        setTimeout(() => {
            this.start = true;
        }, 1000);
    },
}
</script>