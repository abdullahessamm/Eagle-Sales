<template>
    <div id="step-3" class="signup-step">
        <div class="map-container">
            <Map
                :getCoordsCallback="getCoordsCallback"
                :getGeoCodeResultCallback="getGeoCodeResultCallback"
                :center="ipLocationLatlng"
                :zoom="9"
            >
            </Map>
            <div class="input-container">
                <button :class="`eagle-sales-btn ${readyForNext ? '' : 'disabled'}`" @click="goToNext">
                    <span> {{ locationIsAvailable === false && coords.lat !== 0 ? __.buttons.unavailable_location : __.buttons.next }} </span>
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
    name: 'Step2',
    props: ['__'],

    data: () => ({
        readyForNext: false,
        locationIsAvailable: false,

        coords: {
            lat: 0,
            lng: 0
        },

        address: {
            country: '',
            gov: '',
        }
    }),

    computed: {
        ipLocationLatlng() {
            const ipLocation = this.$store.state.signup.ipLocation;
            if (ipLocation)
                return ipLocation.lat && ipLocation.lon ? {
                    lat: ipLocation.lat,
                    lng: ipLocation.lon
                } : null
        },
    },

    watch: {
        address: {
            handler() {
                const availablePlaces = this.$store.state.signup.availablePlaces;
                const country = availablePlaces.find(place => place.iso_code === this.address.country);
                
                if (country) {
                    const gov = country.cities.find(city => city.name === this.address.gov);
                    if (gov) {
                        this.readyForNext = true;
                    } else {
                        this.readyForNext = false;
                    }
                } else {
                    this.readyForNext = false;
                }
            },
            deep: true
        },

        readyForNext: function(val) {
            this.locationIsAvailable = val;
        },
    },

    methods: {
        getCoordsCallback(coords) {
            this.coords = coords;
        },

        getGeoCodeResultCallback(results) {
            results.forEach(result => {
                result.address_components.forEach(component => {
                    if (component.types.includes('country')) {
                        this.address.country = component.short_name;
                    }
                    if (component.types.includes('administrative_area_level_1')) {
                        this.address.gov = component.long_name;
                    }
                });
            });
        },

        goToNext() {
            const coords = `${this.coords.lat},${this.coords.lng}`;
            this.$store.commit('SET_SIGNUP_SELECTED_PLACE', this.address);
            this.$store.commit('SET_SIGNUP_USER_DATA_STATE', { coords });
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