<script>
import Vuex from 'vuex'
import CpntLogin from './login.vue'
import CpntDefaultChannelPrompt from './default-channel-prompt.vue'

export default {

  components: {
    'cpnt-login': CpntLogin,
    'cpnt-default-channel-prompt': CpntDefaultChannelPrompt,
  },

  data() {
    return {
      mixin: {
        modal: {
          login: {
            value: false,
            closable: true,
            maskClosable: true,
            to: null,
          },
          defaultChannelPrompt: {
            value: false,
            closable: true,
            maskClosable: true,
            to: null,
          },
        },
      },
    }
  },

  computed: _.merge(
      Vuex.mapGetters(['logined']),
  ),

  methods: {

    checkLogined(current = false, redirect = false, closable = true, maskClosable = true) {
      /* 註解:若須即時判斷後端真實登入狀況, 請使用Promise與Callback實作之 */
      axios.get('/auth/check').then(response => {
        console.log(this.logined, response.data)
        if (this.logined && response.data) {
          return
        }
        this.$store.state.user = null;
        let url = window.btoa(window.location.href)
        url = current ? url : null
        if (redirect) {
          let query = current ? '?to=' + url : ''
          window.location.href = '/auth/login/login-as-habook' + query
        } else {
          this.mixin.modal.login.to = url
          this.mixin.modal.login.closable = closable
          this.mixin.modal.login.maskClosable = maskClosable
          this.mixin.modal.login.value = true
        }
      }).catch(error => {
        //console.log(error.response)
      });
    },

    displayDefaultChannelPrompt() {
      this.mixin.modal.defaultChannelPrompt.value = true
    },

    closeDefaultChannelPrompt() {
      this.mixin.modal.defaultChannelPrompt.value = false
    },

  },

}
</script>
