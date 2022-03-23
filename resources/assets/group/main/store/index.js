import Vue  from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({

	state: {
		groupId : Globals.group.id,
        channels: Globals.group.channels,
    },

    mutations: {

    },

    actions: {

    },

})
