<template>
    <div class="tick-container w-100 h-100 position-relative">
        <div id="tick-animate" :class="animate ? 'animate' : ''"></div>
    </div>
</template>

<style lang="scss" scoped>
    @import '../../../sass/auth/colors.scss';

    #tick-animate {
        width: 0;
        height: 0;
        border-bottom: 3px solid #fff;
        border-right: 3px solid #fff;
        transition: width .3s ease-out, height .5s ease-out .3s;
        transform: rotate(45deg);
        position: absolute;
        bottom: 50%;
        left: 20%;
        transform-origin: bottom left;

        &.animate {
            width: 50px;
            height: 100px;
        }
    }

</style>

<script>
export default {
    name: 'TickAnimation',
    
    data: () => ({
        animate: false,
    }),

    mounted() {
        setTimeout(() => {
            this.animate = true;
        }, 300);
    },
}
</script>