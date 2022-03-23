import Vue          from 'vue'
import VueRouter    from 'vue-router'
import CpntHome     from '../components/home.vue'
import CpntAbout    from '../components/about.vue'
import CpntMember   from '../components/member.vue'
import CpntChannel  from '../components/channel.vue'

Vue.use(VueRouter)

export default new VueRouter({

	routes: [
		{ path: '/',                     name: 'home',    component: CpntHome    },
		{ path: '/about',                name: 'about',   component: CpntAbout   },
		{ path: '/member',               name: 'member',  component: CpntMember  },
		{ path: '/channel/:id/:cmsType', name: 'channel', component: CpntChannel },
	]

})
