try {

	window.videojs = require('video.js')
	require('../../../../../public/lib/videojs/lang/en')
	require('../../../../../public/lib/videojs/lang/tw')
	require('../../../../../public/lib/videojs/lang/cn')
	require('videojs-contrib-hls/dist/videojs-contrib-hls.min')
	// require('videojs-youtube/dist/Youtube.min')
	require('silvermine-videojs-quality-selector')(window.videojs)
	//require('videojs-contrib-quality-levels/dist/videojs-contrib-quality-levels.min')
	//require('videojs-resolution-switcher')
	require('../../../../../public/lib/videojs/ezStation/ezStation')

	window.echarts = require('echarts')

} catch (e) {

}
