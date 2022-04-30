<template>
    <b-spinner label="Loading..." :style="animationStyle"></b-spinner>
</template>

<script>
export default {
    name: "loading-animation",
    props: ['style'],

    computed: {
        animationStyle() {
            let style = {
                "color": "#497096",
                "font-size": "10px",
                "animation-duration": ".5s",
                "border-top-color": "transparent",
                width: "16px",
                height: "16px",
            }

            if (this.$props.style) {
                style = {
                    ...style,
                    ...this.$props.style
                }
            }

            return style
        },
    }
}
</script>