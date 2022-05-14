<template>
    <div id="map-container" class="map-container w-100 h-100 position-relative">
        <div id="map" style="height: 100%"></div>
        <div class="my-location" @click="moveToCurrentPosition"> <i class="fa-solid fa-location-crosshairs"></i> </div>
    </div>
</template>

<style scoped>
    .map-container {
        cursor: default;
    }

    .map-container .my-location {
        display: flex;
        position: absolute;
        bottom: 30px;
        left: 10px;
        width: 45px;
        height: 45px;
        background: #fff;
        border-radius: 5px;
        justify-content: center;
        align-items: center;
        font-size: 20px;
        color: #666;
        cursor: pointer;
        box-shadow: 0px 0px 3px;
    }
</style>

<script>
import { Loader } from '@googlemaps/js-api-loader';

const googleAPI = 'AIzaSyCs1Mnx_DaV9lj-o9XayZ8IGsRSss7NxFA';


export default {
    name: 'Map',
    props: ['getCoordsCallback', 'center', 'zoom'],

    data: () => ({
        map: null,
        marker: null,
        geocoder: null,
        infoWindow: null,
        preventSelection: false,

        countriesCoords: {
            saudiArabia: {
                lat: 24.711,
                lng: 46.675,
            },
            egypt: {
                lat: 26.8206,
                lng: 30.8025,
            },
        },
        
        currentPosition: {
            lat: 0,
            lng: 0
        },

        navigatorSupported: false,
        geolocationError: true,
        errorMsgs: {
            geolocation: '',
        },
    }),

    watch: {
        currentPosition: {
            handler: function(val) {
                this.getCoordsCallback(val);
            },
            deep: true
        },
    },

    methods: {
        async moveToCurrentPosition() {
            this.navigatorSupported = !! window.navigator.geolocation;

            const getCurrentPosition = new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject);
            });

            if (this.navigatorSupported) {
                await getCurrentPosition.then(position => {
                    this.geolocationError = false;
                    this.currentPosition = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    }

                    this.map.panTo(this.currentPosition);
                    this.map.getZoom() < 12 ? this.map.setZoom(12) : null;
                    this.marker.setPosition(this.currentPosition);
                    this.marker.setVisible(true);

                    this.showLocationInfo();
                }).catch(err => {
                    this.geolocationError = true;
                    if (err.code === 1) {
                        this.errorMsgs.geolocation = 'please allow location access';
                    } else if (err.code === 2) {
                        this.errorMsgs.geolocation = 'Error while getting location';
                    } else if (err.code === 3) {
                        this.errorMsgs.geolocation = 'Check your internet connection';
                    } else {
                        this.errorMsgs.geolocation = 'unknown error';
                    }
                });
            }
        },

        showLocationInfo() {
            this.geocoder.geocode({
                location: this.currentPosition
            }, (results, status) => {
                if (status === 'OK') {
                    this.preventSelection = false;
                    if (results[0]) {
                        this.infoWindow.setContent(results[0].formatted_address);
                        this.infoWindow.open(this.map, this.marker);
                    } else {
                        window.alert('No results found');
                    }
                } else {
                    this.preventSelection = true;
                }
            });
        },
    },

    async mounted() {
        const loader =  new Loader({
            apiKey: googleAPI,
            language: this.$store.state.lang,
        });
        const mapDiv = document.getElementById('map');
        const timezone = new Date().toString().match(/GMT[\-\+]\d+|UTC[\-\+]\d+/)[0]
        let country = 'saudiArabia';

        if (timezone.includes('+0200')) {
            country = 'egypt';
        }

        // map init
        await loader.load();
        this.map = new google.maps.Map(mapDiv, {
            center: this.center ?? this.countriesCoords[country],
            zoom: this.zoom ? parseInt(this.zoom) : 5,
            draggableCursor: this.preventSelection ? 'not-allowed' : 'pointer',
        })

        // marker init
        this.marker = new google.maps.Marker({
            position: {
                lat: 0,
                lng: 0
            },
            map: this.map,
            title: 'Your Location',
        })
        this.marker.setVisible(false);

        // geocoder init
        this.geocoder = new google.maps.Geocoder();

        // info window init
        this.infoWindow = new google.maps.InfoWindow();
        
        this.map.addListener("click", e => {
            this.currentPosition = {
                lat: e.latLng.lat(),
                lng: e.latLng.lng()
            }
            this.marker.setPosition(this.currentPosition);
            this.marker.setVisible(true);

            this.showLocationInfo();
        })
    },
}
</script>