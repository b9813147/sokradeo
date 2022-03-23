<script>
import Vuex      from 'vuex'
import CpntLogin from './login.vue'

export default {

    components: {
        'cpnt-login': CpntLogin,
    },

    data () {
        return {
            mixin: {
                modal: {
                    login: {
                        value       : false,
                        closable    : true,
                        maskClosable: true,
                        to          : null,
                    },
                },
            },
        }
    },

    computed: _.merge(
        Vuex.mapGetters(['logined']),
    ),

	methods: {

        checkLogined (current = false, redirect = false, closable = true, maskClosable = true) {
            /* 註解:若須即時判斷後端真實登入狀況, 請使用Promise與Callback實作之 */
            if (this.logined) {
                return
            }

            let url = window.btoa(window.location.href)
            url     = current ? url : null
            if (redirect) {
                let query            = current ? '?to=' + url : ''
                window.location.href = '/auth/login/login-as-habook' + query
            } else {
                this.mixin.modal.login.to           = url
                this.mixin.modal.login.closable     = closable
                this.mixin.modal.login.maskClosable = maskClosable
                this.mixin.modal.login.value        = true
            }
        },
	},

}
</script>
