import Vue          from 'vue'
import VueRouter    from 'vue-router'
import CpntHome     from '../components/home.vue'
import CpntMembers  from '../components/members.vue'
import CpntChannels from '../components/channels.vue'
import CpntChannel  from '../components/channel.vue'

Vue.use(VueRouter)

export default new VueRouter({

	routes: [
		{ path: '/',                     name: 'home',     component: CpntHome     },
		{ path: '/members/:fun',         name: 'members',  component: CpntMembers  },
		{ path: '/channels',             name: 'channels', component: CpntChannels },
		{ path: '/channel/:id/:cmsType', name: 'channel',  component: CpntChannel  },
	]

})
