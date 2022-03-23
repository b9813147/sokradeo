import Vue                 from 'vue'
import VueRouter           from 'vue-router'
import CpntHome            from '../components/home.vue'
import CpntAbout           from '../components/about.vue'
import CpntFiltered        from '../components/filtered.vue'
import CpntFilter          from '../components/filter.vue'
import CpntContent         from '../components/content.vue'
import CpntGroupChannel    from '../components/groupChannel.vue'
import CpntMyMovie         from '../components/myMovie.vue'
import CpntActivityChannel from '../components/activityChannel.vue';
import CpntContentReview   from '../components/contentReview.vue';
import CpntGenObsrvClass   from '../components/generalObservationClass.vue';
import CpntObsrvClass      from '../components/observationClass.vue';
import CpntObservations    from '../../../app/components/thumb-observation';

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
        default: CpntFiltered,
        // navtools: CpntFilter
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
    {
      path     : '/content-review/:channelId',
      name     : 'content-review',
      component: CpntContentReview
    },
    {
      path     : '/general-observation-class/',
      name     : 'general-observation-class',
      component: CpntGenObsrvClass
    },
    {
      path     : '/observation-class/',
      name     : 'observation-class',
      component: CpntObsrvClass
    },
    {
      path     : '/observation/:printSwitch/:content-id/:channel-id',
      name     : 'observation',
      component: CpntObservations,
      props    : true
    }
  ]

})
