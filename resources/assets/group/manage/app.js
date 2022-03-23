import _      from 'lodash'
import axios  from 'axios'
import Vue    from 'vue'
import Vuex   from 'vuex'
import iView  from 'iview'
import router from './router/index'
import store  from './store/index'
import i18n   from './lang/index'

Vue.use(Vuex)
Vue.use(iView)

new Vue({

    el: '#app',

    router,

    store,

    i18n,

    data: {

    },

    computed: _.merge(
        Vuex.mapState(['groupId', 'channels']),
        Vuex.mapGetters([]),
    ),

    methods: {

        init: function () {



        }

    },

    mounted: function () {

        this.init()

    }

})
