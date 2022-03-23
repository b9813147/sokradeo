import _               from 'lodash'
import axios           from 'axios'
import Vue             from 'vue'
import iView           from 'iview'
import i18n            from './lang/index'
import store           from './store/index'
import TbaPlayerApiSrv from './services/tbaplayer'
import TbaPlayerHelper from '../../../app/modules/tbaplayer/helpers/tbaplayer'
import CpntTbaPlayer   from '../../../app/modules/tbaplayer/components/tbaplayer.vue'

require('./bootstrap')

Vue.use(iView)
TbaPlayerApiSrv.baseUrl = ''
TbaPlayerHelper.register(store, {module: 'tbaplayer', apiSrv: TbaPlayerApiSrv})

let app = new Vue({

    store,

    i18n,

    components: {
    	'cpnt-tbaplayer': CpntTbaPlayer,
    },

    data: {

		tbaPlayer: {
			options: Globals.tbaPlayerOpts
		},

    },

    methods: {

        init: function (tbaPlayerInfo) {
            this.$store.dispatch('tbaplayer/info', tbaPlayerInfo)
        },

    },

    mounted: function () {

    	this.$store.dispatch('tbaplayer/init')
    }

})

app.init(Globals.tbaPlayerInfo)
app.$mount('#app')
