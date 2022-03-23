import Vue                      from 'vue'
import VueRouter                from 'vue-router'
import CpntHome                 from '../components/home.vue'
import CpntAbout                from '../../../../exhibition/tbavideo/index/components/about.vue'
import CpntFiltered             from '../../../../exhibition/tbavideo/index/components/filtered.vue'
import CpntFilter               from '../../../../exhibition/tbavideo/index/components/filter.vue'
import CpntContent              from '../../../../exhibition/tbavideo/index/components/content.vue'
import CpntGroupChannel         from '../../../../exhibition/tbavideo/index/components/groupChannel'
import CpntMyMovie              from '../../../../exhibition/tbavideo/index/components/myMovie.vue'
import CpntActivityChannel      from '../../../../exhibition/tbavideo/index/components/activityChannel.vue'


Vue.use(VueRouter)

export default new VueRouter({

    routes: [
        {
            path     : '/',
            name     : 'home',
            component: CpntHome
        },
        {
            path     : '/about',
            name     : 'about',
            component: CpntAbout
        },
        {
            path      : '/filtered',
            name      : 'filtered',
            components: {
                default : CpntFiltered,
                navtools: CpntFilter
            }
        },
        {
            path     : '/content/:contentId',
            name     : 'content',
            component: CpntContent
        },
        {
            path     : '/myChannel/:channelId',
            name     : 'channel',
            component: CpntGroupChannel
        },
        {
            path     : '/myMovie/',
            name     : 'movie',
            component: CpntMyMovie
        },
        {
            path     : '/activity-channel/:channelId',
            name     : 'activity-channel',
            component: CpntActivityChannel
        },
    ]

})
