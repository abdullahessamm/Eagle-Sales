<template>
    <div id="step-progress" class="d-flex w-100">
        <div class="progress-container m-auto position-relative d-flex justify-content-between align-items-center">
            <div :class="`bar text-left position-absolute`"></div>
            <div class="progress-level" :style="progressLevelStyle"></div>
            <div v-for="step in steps" :key="step.num" :class="`step ${currentStep === step.num ? isLoading ? 'current loading' : 'current' : currentStep > step.num ? 'completed' : ''}`" @click="() => changeStep(step.num)">
                <div class="circle-animation-container">
                    <div class="circle-animate circle-1"></div>
                    <div class="circle-animate circle-2"></div>
                    <div class="circle-animate circle-3"></div>
                    <div class="circle-animate circle-4"></div>
                </div>
                {{ step.num }}
                <div class="title"> {{ step.title }} </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    $main-color: #247f9c;
    $second-color: #45ba91;

    @keyframes grow {
        from {
            opacity: 0.3;
            transform: scale(1.1);
        }
        to {
            opacity: 0.8;
            transform: scale(1.2);
        }
    }

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    #step-progress {
        .progress-container {width: 60%;}
        margin-bottom: 40px;
        .bar {
            width: 100%;
            height: 3px;
            background: #ccc;
            border-radius: 5px;
            position: absolute;
            z-index: -1;
        }

        .progress-level {
                content: '';
                position: absolute;
                width: 0;
                height: 3px;
                background: $second-color;
                transition: width 0.7s linear;
            }

        .step {
            font-size: 1.2rem;
            font-weight: 400;
            color: #aaa;
            cursor: default;
            width: 50px;
            height: 50px;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            border: 1px solid #aaa;
            position: relative;
            transition: background-color 0.5s ease-in-out;
            cursor: not-allowed;

            &.current {
                color: $second-color;
                border-color: $second-color;
                position: relative;
                cursor: default;

                &::before {
                    content: '';
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    background: $second-color;
                    border-radius: 50%;
                    opacity: 0.5;
                    z-index: -1;
                    animation: grow 1s linear infinite alternate;
                }

                .title {
                    color: $second-color;
                    font-weight: bold;
                }
            }

            &.completed {
                color: #fff;
                background-color: $second-color;
                border-color: $second-color;
                cursor: pointer;

                .title {
                    color: $second-color;
                    font-weight: bold;
                }
            }

            .circle-animation-container {display: none}

            &.loading {
                cursor: not-allowed;
                border-color: transparent;

                &::before {display: none}

                .circle-animation-container {
                    display: block;
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    z-index: -1;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    animation: rotate 3s linear infinite;

                    .circle-animate {
                        display: block;
                        position: absolute;
                        width: 54px;
                        height: 54px;
                        background: transparent;
                        border: 2px solid transparent;
                        border-top-color: #45ba91;
                        border-right-color: transparent;
                        border-radius: 50%;
                        opacity: 1;
                        z-index: -1;
                        -webkit-animation: rotate 1s cubic-bezier(0.3, 0, 0.3, 1) infinite;
                        animation: rotate 1.3s cubic-bezier(0.3, 0, 0.3, 1) infinite;
                        box-sizing: border-box;

                        &.circle-1 {
                            animation-delay: 0s;
                        }

                        &.circle-2 {
                            animation-delay: 0.1s;
                        }

                        &.circle-3 {
                            animation-delay: 0.2s;
                        }

                        &.circle-4 {
                            animation-delay: 0.3s;
                        }
                    }
                }
            }

            &::selection {background: transparent}

            .title {
                font-size: 15px;
                position: absolute;
                bottom: -28px;
                white-space: nowrap;
                transition: color 0.5s ease-in-out;
            }
        }
    }
</style>

<script>

export default {
    name: 'StepProgress',

    props: ['steps'],

    data: () => ({
        progressLevelStyle: {
            width: '0%',
        },
        numOfSteps: 0,
    }),

    computed: {
        currentStep() {
            return this.$store.state.signup.step;
        },

        isLoading() {
            return this.$store.state.signup.isLoading;
        },
    },

    watch: {
        currentStep(val) {
            this.progressLevelStyle.width = `${(val - 1)/(this.numOfSteps - 1) * 100}%`;
        }
    },

    methods: {
        changeStep(step) {
            if (step < this.currentStep)
                this.$store.commit('CHANGE_SIGNUP_STEP_STATE', step);
        },
    },

    mounted() {
        this.numOfSteps = this.steps.length;
        this.progressLevelStyle.width = `${(this.currentStep - 1)/(this.numOfSteps - 1) * 100}%`;
    }
}
</script>