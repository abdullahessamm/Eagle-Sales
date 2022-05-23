<template>
    <div id="finish-animation" class="circle" :style="pageStyle">
        <div class="inner" :style="innerStyle">
                <div class="front">
                <TickAnimationVue />
            </div>

            <div class="back">
                <div class="back-inner w-100 h-100 position-relative d-flex flex-column justify-content-center align-items-center">
                    <div class="f-header position-absolute">
                        <div class="logo-container">
                            <img src="/assets/images/logo/white_300.png" alt="Logo" style="width: 150px">
                        </div>
                        <h4 style="color: #fff">
                            {{ __.welcome}}
                        </h4>
                    </div>
                    <div class="f-footer position-absolute">
                        <div class="msg-container">
                            <p class="text-center" id="message-inner">
                                <span style="font-weight: bold; font-size: 20px; display:inline">{{ __.hi }}, {{$store.state.signup.userData.f_name}}!</span> <br>
                                <span class="msg d-inline" style="line-height: 34px;" v-html="message"></span>
                                <div class="text-cursor d-inline-block"></div>
                            </p>
                        </div>
                        <div class="timer-container">
                            {{ __.redirect_msg }} <span class="timer">{{ minutes }}:{{ seconds }}</span>
                        </div>
                        <div class="button-container">
                            <button class="btn btn-primary" @click="gotoLogin">
                                {{ __.login_now }}!
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    @import '../../../sass/auth/colors.scss';

    #finish-animation {
        perspective: 1000px;
        width: 170px;
        height: 170px;
        position: fixed;
        overflow: hidden;
        transition: all 1s ease-out .1s, border-radius .2s ease-out .1s;

        &.circle {
            border-radius: 200px;

            .inner { border-radius: 200px; }
        }

        .inner {
            position: relative;
            width: 100%;
            height: 100%;
            background-color: $second-color;
            transform-style: preserve-3d;
            transition: all 1s ease-out, border-radius .2s ease-out .1s;

            .front, .back {
                position: absolute;
                width: 100%;
                height: 100%;
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
            }

            .front {
                transform: rotateY(0deg);
            }

            .back {
                transform: rotateY(180deg);

                .back-inner {
                    .f-header {
                        top: 40%;
                        animation: header-animate 2s cubic-bezier(0.3,1,0.3,1);
                        animation-delay: 6s;
                        animation-fill-mode: forwards;
                    }

                    .f-footer {
                        top: 41%;

                        .msg-container {
                            max-width: 800px;
                            color: #fff;
                            font-size: 17px;
                            
                            p {
                                width: 750px;

                                span {
                                    display: inline-block;
                                    width: 100%;
                                    text-align: center;
                                    opacity: 0;
                                    animation: show 1s cubic-bezier(0.3,1,0.3,1) 7s forwards;
                                }

                                .text-cursor {
                                    border-left: 3px solid rgba(255,255,255, 0.8);
                                    height: 25px;
                                    position: absolute;
                                    margin-top: 6px;
                                    margin-left: 5px;
                                    margin-left: 5px;
                                    opacity: 0;
                                    animation: show 1s cubic-bezier(0.3,1,0.3,1) 8s forwards, blink 0.6s infinite linear alternate;
                                }
                            }
                        }
                        
                        .timer-container {
                            color: #e6e6e6;
                            margin: 35px 0 8px 0;
                            opacity: 0;
                            animation: show 0.5s cubic-bezier(0.3,1,0.3,1);
                            animation-delay: 17s;
                            animation-fill-mode: forwards;

                            span {
                                color: #fff;
                                font-weight: bold;
                            }
                        }

                        .button-container {
                            opacity: 0;
                            animation: show 0.5s cubic-bezier(0.3,1,0.3,1);
                            animation-delay: 17s;
                            animation-fill-mode: forwards;

                            button {
                                background: transparent;
                                border: 0;
                                text-decoration: underline;
                                color: #fff;
                            }
                        }
                    }
                }
            }
        }
    }

    @keyframes header-animate {
        from {
            top: 40%;
        } to {
            top: 10%;
        }
    }

    @keyframes show {
        from {
            opacity: 0;
        } to {
            opacity: 1;
        }
    }

    @keyframes blink {
        from {
            border-left-color: rgba(255,255,255, 0.8);
        } to {
            border-left-color: transparent;
        }
    }
</style>

<script>
import TickAnimationVue from './TickAnimation.vue'

export default {
    name: 'FinishSignupAnimation',
    props: ['__'],

    data: () => ({
        pageStyle: {},
        innerStyle: {},

        message: '',

        timer: {
            minutes: 1,
            seconds: 0
        },

        timerInterval: null
    }),

    computed: {
        minutes() {
            let { minutes } = this.timer
            minutes = minutes.toString();
            return minutes.length === 1 ? `0${minutes}` : minutes
        },

        seconds() {
            let { seconds } = this.timer
            seconds = seconds.toString();
            return seconds.length === 1 ? `0${seconds}` : seconds
        }
    },

    watch: {
        timer: {
            handler(newVal) {
                if (newVal.minutes === 0 && newVal.seconds === 0) {
                    this.gotoLogin()
                }
            },
            deep: true
        }
    },

    methods: {
        gotoLogin() {
            clearInterval(this.timerInterval);
            this.$router.push('/login');
        }
    },

    mounted() {

        setTimeout(() => {
            const finish = document.getElementById('finish-animation');
            const pageX = finish.offsetLeft;
            const pageY = finish.offsetTop;
            const screenWidth = window.innerWidth;
            const screenHeight = window.innerHeight;

            this.pageStyle = {
                left: `${pageX}px`,
                top: `${pageY}px`,
            };

            setTimeout(() => {
                this.pageStyle = {
                    top: '0',
                    left: '0',
                    width: `${screenWidth}px`,
                    height: `${screenHeight}px`,
                    borderRadius: '0',
                };
                this.innerStyle = {
                    transform: 'rotateY(180deg)',
                    borderRadius: '0',
                };
            }, 1200);

        }, 1000)

        // typing effect
        const message = this.__.final_msg
        let currentChar = 0;

        setTimeout(() => {
            const typeEffect = setInterval(() => {
                if (currentChar >= message.length - 1)
                    clearInterval(typeEffect);

                if (message.substring(currentChar, currentChar + 1) === '\n') {
                    this.message += '<br>';
                }

                this.message += message[currentChar]
                currentChar++;
            }, 20);
        }, 10000)

        // timer
        setTimeout(() => {
            this.timerInterval = setInterval(() => {
            if (this.timer.seconds === 0) {
                if (this.timer.minutes === 0)
                    clearInterval(this.timerInterval);
                else {
                    this.timer.minutes--;
                    this.timer.seconds = 59;
                }
            } else {
                this.timer.seconds--;
            }
            }, 1000);
        }, 17000)
    },

    components: {
        TickAnimationVue,
    },
}
</script>