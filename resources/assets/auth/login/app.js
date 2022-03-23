import _     from 'lodash'
import axios from 'axios'
import Vue   from 'vue'
import iView from 'iview'

Vue.use(iView)

new Vue({

    el: '#app',

    data: {

    },

    methods: {

        init () {



        },

        loginAsHabook () {
            let url_string       = window.location.href;
            let url              = new URL(url_string);
            let to               = (url.searchParams.get('to') === null) ? 'default' :  url.searchParams.get('to');
            window.location.href = 'login/login-as-habook?to=' + to;
        },

    },

    mounted: function () {

        this.init()

    }

})
