import Vue       from 'vue'
import VueRouter from 'vue-router'
import CpntList  from '../components/list.vue'
import CpntEdit  from '../components/edit.vue'

Vue.use(VueRouter)

export default new VueRouter({

	routes: [
		{ path: '/',         name: 'list', component: CpntList },
		{ path: '/edit/:id', name: 'edit', component: CpntEdit },
	]

})
