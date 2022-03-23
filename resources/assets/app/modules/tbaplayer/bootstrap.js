try {

	window._       = require('lodash')

	window.Vue     = require('vue')

	window.Vuex    = require('vuex')

	window.VueI18n = require('vue-i18n')

	window.iview   = require('iview')

	Vue.use(Vuex)
	Vue.use(VueI18n)
	Vue.use(iview)

	window.videojs = require('video.js')
	require('videojs-contrib-hls/dist/videojs-contrib-hls.min')
	// require('videojs-youtube/dist/Youtube.min')
	require('silvermine-videojs-quality-selector')(window.videojs)
	//require('videojs-contrib-quality-levels/dist/videojs-contrib-quality-levels.min')
	//require('videojs-resolution-switcher')
	require('../../../../../public/lib/videojs/ezStation/ezStation')

	window.echarts = require('echarts')

} catch (e) {

}
