<template>
    <div id="signup-layout" class="d-flex w-100 h-100 position-fixed overflow-hidden">
        <div class="art h-100 d-md-flex d-none">
            <svg-vue icon="signup_avatar" class="avatar"></svg-vue>
            <img :src="`${origin}/assets/images/logo/full_logo_colored_named.png`" alt="logo" title="Logo">
        </div>
        <div class="form h-100">
            <div class="z-header">
            <h3 class="title"> {{ __.title }} </h3>
                <div class="steps w-100" :style="$store.state.lang === 'ar' ? 'direction:rtl' : ''">
                    <StepProgress :steps="steps"></StepProgress>
                </div>
            </div>
            <div class="z-body" :style="$store.state.lang === 'ar' ? 'direction:rtl' : 'direction:ltr'">
                <div class="signup-inputs w-100 h-100" v-if="$store.state.signup.job">
                    <form class="signup-form h-100 position-relative d-flex justify-content-center" @submit.prevent>
                        <transition name="slide">
                            <Step1 v-if="$store.state.signup.step === 1 && !$store.state.signup.isLoading" :__="__"></Step1>
                        </transition>
                        <transition name="slide">
                            <Step2 v-if="$store.state.signup.step === 2 && !$store.state.signup.isLoading" :__="__"></Step2>
                        </transition>
                        <transition name="slide">
                            <Step3
                                v-if="$store.state.signup.step === 3 &&
                                     !$store.state.signup.isLoading &&
                                      $store.state.signup.job !== '5'"
                                :__="__"
                            ></Step3>
                        </transition>
                        <transition name="slide">
                            <Finish
                                v-if="!$store.state.signup.isLoading &&
                                     (
                                        ($store.state.signup.step === 4) ||
                                        ($store.state.signup.step === 3 && $store.state.signup.job === '5')
                                     )"
                                :__="__"
                            ></Finish>
                        </transition>
                        <!-- <transition tag="div" class="w-100 h-100 d-flex justify-content-center align-items-center" name="slide" v-if="$store.state.signup.isLoading">
                            <div class="animation-container">
                                <LoadingAnimation
                                    :style="{
                                        color: '#45ba91',
                                        'font-size': '14px',
                                        width: '80px',
                                        height: '80px',
                                    }"
                                />
                            </div>
                        </transition> -->
                    </form>
                </div>
                <div class="choose-user-type justify-content-center" v-if="! ($store.state.signup.job || $store.state.signup.isLoading)">
                    <div class="card-button" v-if="!isCustomer" @click="$store.commit('SET_SIGNUP_JOB_STATE', '0')"> {{ __.user_types.supplier }} </div>
                    <div class="card-button" v-if="!isCustomer" @click="isCustomer=true"> {{ __.user_types.customer }} </div>
                    <transition name="fade">
                        <div class="customers-group d-flex" v-if="isCustomer">
                            <div class="card-button" @click="$store.commit('SET_SIGNUP_JOB_STATE', '5')"> {{ __.user_types.individual }} </div>
                            <div class="card-button" @click="$store.commit('SET_SIGNUP_JOB_STATE', '3')"> {{ __.user_types.corporation }} </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    $main-color: #247f9c;
    $second-color: #45ba91;

    #signup-layout {
        .art {
            flex-grow: 1;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;

            img {
                position: absolute;
                top: 10px;
                left: 20px;
                width: 100px;

            }

            .avatar {
                width: 70%;
                min-width: 200px;
                max-width: 300px;
            }
        }

        .form {
            max-width: 65%;
            flex-grow: 4;
            text-align: center;
            overflow-y: auto;

            .z-header {
                height: 30%;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .z-body {
                height: 70%;
            }

            .title {
                font-size: 3.2rem;
                font-weight: 400;
                margin-bottom: 20px;
                color: $second-color;
                cursor: default;

                &::selection {background: transparent}
            }
        }

        .choose-user-type {
            display: flex;
            margin-top: 50px;
            
            .card-button {
                margin: auto 10px;
                width: 170px;
                height: 140px;
                border-radius: 8px;
                background-color: #fff;
                border: 1px solid $second-color;
                color: $second-color;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 1.3rem;
                cursor: pointer;
                transition: all 0.2s ease-in-out;

                &:hover {
                    background-color: $second-color;
                    color: #fff;
                }
            }
        }
    }

    // animate
    .slide-enter-from {
        opacity: 0;
        transform: scale(0.7);
    }
    .slide-enter-to {
        opacity: 1;
        transform: scale(1);
    }
    .slide-enter-active {
        transition: all 0.3s ease-in-out;
        transition-delay: 0.2s;
    }
    .slide-leave-from {
        opacity: 1;
        transform: scale(1);
    }
    .slide-leave-to {
        opacity: 0;
        transform: scale(0.7);
    }
    .slide-leave-active {
        transition: all 0.3s ease-in-out;
    }

</style>

<script>
import StepProgress from '../components/signup/StepProgress.vue';
import Step1 from '../components/signup/Step1.vue';
import Step2 from '../components/signup/Step2.vue';
import Step3 from '../components/signup/Step3.vue';
import Finish from '../components/signup/Finish.vue';
import LoadingAnimation from '../components/LoadingAnimation.vue';

export default {
    name: 'SignupLayout',
    props: ['__'],

    computed: {
        origin: () => window.location.origin,

        job () {
            return this.$store.state.signup.job
        }
    },

    data: () => ({
        isCustomer: false,
        steps: [],
    }),

    watch: {
        job (val) {
            if (val !== '5') {
                this.steps[2].title = this.__.steps.user_info;
                this.steps.push({
                    num: 4,
                    title: this.__.steps.finish,
                });
            } else {
                this.steps[2].title = this.__.steps.finish;
                this.steps.splice(3, 1);
            }
        }
    },

    beforeMount () {
        this.steps = [
            {
                num: 1,
                title: this.__.steps.account_info,
            },
            {
                num: 2,
                title: this.__.steps.select_location,
            },
            {
                num: 3,
                title: this.__.steps.finish,
            },
        ];
    },

    components: {
        StepProgress,
        Step1,
        Step2,
        Step3,
        Finish,
        LoadingAnimation,
    },
}
</script>