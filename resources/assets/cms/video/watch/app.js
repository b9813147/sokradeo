import _               from 'lodash'
import axios           from 'axios'
import Vue             from 'vue'
import iView           from 'iview'
import HelperEzStation from '../../../app/helpers/ezStation/ezStation.vue'

require('./bootstrap')

Vue.use(iView)

new Vue({

    mixins: [HelperEzStation],

    el: '#app',

    data: {
    	player: null,
    },

    methods: {

        init: function () {

        	this.player = videojs('player')
            this.player.pluginEzStation(this.ezStationOpts(), Globals.ezStation);
            this.player.controlBar.addChild('QualitySelector')
        },

    },

    mounted: function () {

    	this.init()

    }

})
