import _                        from 'lodash'
import axios                    from 'axios'
import Vue                      from 'vue'
import Vuex                     from 'vuex'
import iView                    from 'iview'
import VueSelect                from 'vue-select'
import { mixin as clickaway }   from 'vue-clickaway';
import router                   from './router/index'
import store                    from './store/index'
import i18n                     from './lang/index'
import CpntMixinApp             from '../../app/components/mixin-app.vue'
import CpntUserInfoSidebar      from '../../../app/components/user-info-sidebar.vue';

Vue.use(Vuex)
Vue.use(iView)
Vue.component('cpnt-select', VueSelect)

new Vue({

    mixins: [CpntMixinApp, clickaway],

    el: '#app',

    router,

    store,

    i18n,

    components: {
        "cpnt-user-info-sidebar": CpntUserInfoSidebar,
    },

    data () {
        return {
            debounce: null,
            keywordSelect: {
                list    : [],
                selected: '',
            },
            hists: [],
            selectedLangUrl: this.$i18n.locale,
            notificationCountLimit: 99,
            mobileSearchbar: {
                show: false,
            },
        }
    },

    computed: _.merge(
        Vuex.mapState(["user", "modal", "isNarrowScreen", "isMobileBrowser"]),
        Vuex.mapGetters(['urlAvatar']),
        {
            unreadNotificationCount() {
                return this.user.notification_count;
            },
        }
    ),

    methods: _.merge(

        Vuex.mapActions([
            "updateSearchTime",
            "setKeyword",
            "initFilterList",
            "updateWindowSize",
            "updateIsMobileBrowser",
          ]),

        {

            init () {

                this.initFilterList()
                this.initScreenWatcher()
                this.initDefaultChannelPrompt()
                
            },

            initScreenWatcher() {

                // A debounce function is used to throttle the resize event
                window.onresize = () => {
                    clearTimeout(this.debounce);
                    this.debounce = setTimeout(() => {
                      this.updateWindowSize({
                        width: window.innerWidth,
                        height: window.innerHeight,
                      });
                      this.updateIsMobileBrowser();
                    }, 100);
                  };

            },

            initDefaultChannelPrompt() {
                // Display a prompt modal to set up default channel for users
                if (!this.logined || (this.user && this.user.group_channel_id)) return;
                this.displayDefaultChannelPrompt();
            },

            searchKeywords () {

                let search = this.keywordSelect.selected

                if (search === '') {
                    return
                }

                axios.get('/exhibition/tbavideo/search-keywords', {
                    params: {name: search}
                })
                .then((data) => {
                    data = data.data
                    if (! data.status) {
                        return
                    }
                    this.keywordSelect.list = data.data
                })
                .catch((e) => {
                	console.log(e)
                })
            },

            selectKeyword (v) {

                this.keywordSelect.selected = v
            },

            searchKeyword () {

                let search = this.keywordSelect.selected

                this.setKeyword(search)

                if (search === '') {
                    return
                }

                this.filters()
            },

            openHistDropdown (visible) {
                if (! visible) {
                    return
                }
                this.getHists()
            },

            getHists () {
                this.hists = []
                axios.get('/exhibition/tbavideo/get-hists', {})
                .then((data) => {
                    data = data.data
                    if (! data.status) {
                        return
                    }
                    _.forEach(data.data, (v) => {
                        this.hists.push({
                            id  : v.tba_id,
                            text: v.tba.name,
                        })
                    })
                })
                .catch((e) => {
                	console.log(e)
                })
            },

            selectHist (v) {
                this.checkLogined()
                if (! this.logined) {
					return
				}
				window.open('tbavideo/watch?contentId=' + v)
            },

            filters () {
                this.updateSearchTime()
                router.push({name: 'filtered'})
            },

            navigate (name) {
                let isForceCloseSideBar = true;
                switch (name) {
                    case 'home':
                    case 'about':
                    case 'filtered':
                        this.setKeyword(this.keywordSelect.selected = '')
                        router.push({name: name})
                        break
                    case 'login':
                        this.checkLogined(false, true)
                        break
                    case 'user-info-sidebar':
                        isForceCloseSideBar = false;
                        this.toggleUserSideBar();
                        break
                }
                // Force closing user info side bar when the page is interacted
                if (isForceCloseSideBar) {
                    this.closeVisibleUserSideBar();
                }
            },

            closeVisibleUserSideBar() {
                if (!this.$store.state.sider.right.collapsed) {
                    this.toggleUserSideBar();
                }
            },

            toggleUserSideBar() {
                this.$store.state.sider.right.collapsed = !this.$store.state.sider.right.collapsed;
            },

            switchLang() {
                // Set lang Route ('/lang/lang/{locale}')
                let origin = window.location.origin;
                let langRoute = "";
                if (this.selectedLangUrl === "en")
                    langRoute = origin + "/lang/lang/en";
                else if (this.selectedLangUrl === "tw")
                    langRoute = origin + "/lang/lang/tw";
                else if (this.selectedLangUrl === "cn")
                    langRoute = origin + "/lang/lang/cn";
                
                // Change lang and callback to current URL
                let callBackUrl = window.location.href ? window.location.href : "";
                window.location = langRoute + "?callBack=" + callBackUrl;
            },

            toggleMobileSearchbar() {
                this.mobileSearchbar.show = !this.mobileSearchbar.show;
            },
        }
    ),

    mounted: function () {

        this.init()

    }

})
