<template>
    <div id="step-3" class="signup-step">
        <div class="map-container">
            <Map :getCoordsCallback="getCoordsCallback" :center="ipLocationLatlng" :zoom="9"></Map>
            <div class="input-container">
                <button :class="`eagle-sales-btn ${readyForNext ? '' : 'disabled'}`" @click="goToNext">
                    <span> Next </span>
                </button>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    .map-container {
        width: 80%;
        height: 80%;
        margin: 0 auto;

        .disabled {
            background-color: #ccc;
            cursor: not-allowed;
            border-color: #ccc;
        }
    }
</style>

<script>
import Map from './Map.vue';

export default {
    name: 'Step3',

    data: () => ({
        readyForNext: false,

        coords: {
            lat: 0,
            lng: 0
        },
    }),

    computed: {
        ipLocationLatlng() {
            const ipLocation = this.$store.state.signup.ipLocation;
            console.log(ipLocation.lat);
            return {
                lat: ipLocation.lat,
                lng: ipLocation.lon
            }
        },
    },

    methods: {
        getCoordsCallback(coords) {
            this.coords = coords;
            this.readyForNext = true;
        },

        goToNext() {
            const parsedLocation = `${this.coords.lat},${this.coords.lng}`;
            console.log(parsedLocation);
            switch (this.$store.state.signup.job) {
                case '0':
                    this.$store.commit('SET_SUPPLIER_LOCATION', parsedLocation);
                    break;
                case '3':
                    this.$store.commit('SET_CUSTOMER_LOCATION', parsedLocation);
                    break;
                case '5':
                    this.$store.commit('SET_ONLINE_CLIENT_LOCATION', parsedLocation);
                    break;
            }
            this.$store.commit('INCREASE_SIGNUP_STEP_STATE');
        }
    },

    components: {
        Map,
    },

    mounted() {
        console.log(this.ipLocationLatlng)
    }
}
</script>