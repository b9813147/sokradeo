import _      from 'lodash'
import axios  from 'axios'
import Vue    from 'vue'
import iView  from 'iview'
import router from './router/index'
import store  from './store/index'
import i18n   from './lang/index'

Vue.use(iView)

new Vue({

    el: '#app',

    router,

    store,

    i18n,

    data: {},

    methods: {

        init () {

        },

    },

    mounted () {

        this.init()

    }

})
