import Vue  from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({

    state: {
    	tba         : null,
    	videos      : [],
        videoSrcType: null,
    },

    mutations: {

        initTbaVideoInfo (state, data) {
            state.tba          = data.tba
            state.videos       = data.videos
            state.videoSrcType = data.videoSrcType
    	},

    },

    actions: {

    	initTbaVideoInfo (ctx, data) {
    		ctx.commit('initTbaVideoInfo', data)
    	},

    },

})
