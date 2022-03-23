<template>
  <video v-if="narrowScreen" :id="id" class="video-js vjs-default-skin vjs-big-play-centered" 
    :width="dimension.width" :height="dimension.height"
        controls 
        playsinline 
        webkit-playsinline="true"
        x5-playsinline="true"
        x5-video-player-type="h5"
        x5-video-orientation="landscape|portrait"
        x5-video-player-fullscreen="true"
        autoplay
        data-setup='{"playbackRates": [1, 1.5, 2]}'
        oncontextmenu="return false;"
    >
        <source v-if="androidwechat" src="" type="video/mp4" label="ezStation">
  </video>
  <video v-else-if="chrome" :id="id" class="video-js vjs-default-skin vjs-big-play-centered" style="width: 100%; height: 100%"
    controls autoplay muted="muted" poster="/images/app/spinner.gif"
	  data-setup='{ "playbackRates": [1, 1.5, 2]}' oncontextmenu="return false;">
  </video>
  <video v-else :id="id" class="video-js vjs-default-skin vjs-big-play-centered" style="width: 100%; height: 100%"
    controls autoplay muted="muted" poster="/images/app/spinner.gif"
	  data-setup='{"playbackRates": [1, 1.5, 2]}' oncontextmenu="return false;">
   </video>
</template>

<script>

// muted="muted" poster="/images/app/spinner.gif"
import _    from 'lodash'
import Vuex from 'vuex'

export default {

    props: ['id', 'options', 'dimension'],

    data () {
        return {
            ua: '',
            androidwechat: false,
            player  : null,
            muted   : false,
            watchReadyState: false,
            chrome: false,
            currTime: 0,
            initSeekTime: 0,
            opts : {
            // 隱藏用不到的功能按鈕
                hiddens : ['starMark', 'hardMark', 'generalMark', 'displayPanel', 'videoInfo','videoIdx', 'markStat'],
                handlers: {}
            },
        }
    },

    computed: _.merge(
        Vuex.mapState('tbaplayer', {
            narrowScreen   : state => state.narrowScreen,
            mode           : state => state.mode,
            sectMap        : state => state.sectMap,
            video          : state => state.video,
            videoRange     : state => state.videoRange,
            track          : state => state.track,
            seekTime       : state => state.seekTime,
            paused         : state => state.paused,
            statusTime     : state => state.statusTime,
        }),
    ),

    watch: {

        mode (v) {
            switch (v) {
                case 'edit':
                    this.setStatus('pause')
                    break
            }
        },

        video (v) {
            if(v === null) {
				return
			}
			this.setVideo(v)
        },

        seekTime (v) {
            this.player.currentTime(v)
            //console.log('vjs seekTime',v, this.player.readyState(), this.initSeekTime)
        },

        statusTime (v) {
            this.setStatus(this.paused ? 'pause' : 'play')
        },

    },

    methods: _.merge(

        Vuex.mapActions('tbaplayer', [
            'setTbaTime',
            'setTrackInfo',
        ]),

        {
            reset () {
                this.player.reset()
            },

            setVideo (info) {
                let srces = _.map(info.video.list, (v) => {
                    return {
                        src  : v.url,
                        type : v.mime,
                        label: v.label,
                    }
                })
                this.player.src(srces)
                //console.log('assign video source')
                this.player.ezStation.init(info.ezStation);
                this.player.load()
            },

            setStatus (status) {
                if(status==='pause' && this.player.paused()) {
                    return
                }

                switch(status) {
                    case 'play':
                        this.player.play()
                        break
                    case 'pause':
                        // for chrome:修正播放器狀態錯亂的錯誤 videojs player.pause()
                        let video = document.getElementById(this.id).getElementsByTagName('video')[0]
                        video.play().then(() => { this.player.pause() })
                        break
                }
            },

            parseVideoTimeToTbaTime(tVideo) {
                let valid = false
                let tTba  = 0
                let sects = this.sectMap[this.track].sects
                for (let sect of sects) {
                    tTba = tVideo - sect.video_offset + sect.tba_start
                    if (tTba >= sect.tba_start && tTba <= sect.tba_end) {
                        valid = true
                        break
                    }
                }
                return valid ? tTba : this.videoRange.max;
            },

            checkReadyState() {
                let that = this
                //console.log('readystate',this.player.readyState(),'watchreadystate',this.watchReadyState)
                if(!this.watchReadyState) return;
                //console.log('readystate',this.player.readyState())
                if(this.player.readyState()>1) {
                    this.player.poster("")
                    var bpb1 = document.querySelector('.vjs-big-play-button');
                    console.log(bpb1.style);
                    bpb1.style = "display:block;";
                    var bpb2 = document.querySelector('.vjs-default-skin.vjs-paused');
                    console.log(bpb2.style);
                    // bpb2.style = "display:block;"+(this.narrowScreen ? '' : "width:100%;height:100%");
                    console.log(bpb1.style, bpb2.style);
                    if(!this.player.paused()) this.player.bigPlayButton.hide();
                    this.watchReadyState = false;
                    if(this.seekTime>this.player.currentTime()) this.player.currentTime(this.seekTime);
                } else {
                    setTimeout(() => {
                        that.checkReadyState();
                    }, 1000);
                }
            },
        }
    ),

    created () {
        this.ua = navigator.userAgent.toLowerCase();
        this.androidwechat = (this.ua.indexOf('micromessenger')!=-1) && (this.ua.indexOf('tbs')!=-1);
        this.chrome = (this.ua.indexOf('chrome')!=-1);
    },

    mounted () {
        let me = this
        me.player = videojs(me.id)
        me.player.controlBar.volumePanel.hide();
        me.player.controlBar.durationDisplay.hide();
        me.player.controlBar.subsCapsButton.hide();
        me.player.controlBar.remainingTimeDisplay.hide();
        me.player.controlBar.currentTimeDisplay.hide();
        me.player.controlBar.chaptersButton.hide();
        //console.log('controlbar', me.player.controlBar)

        me.player.ready(function() {
            //console.log("ready",me.player.readyState(),me.dimension.width,me.dimension.height,me.seekTime);
            //me.player.posterImage.hide();
            if(me.player.readyState()<2) {
                me.watchReadyState = true;
                me.checkReadyState();
            }
            /*
            me.player.poster("")
            var bpb1 = document.querySelector('.vjs-big-play-button');
            bpb1.style = "display:block";
            var bpb2 = document.querySelector('.vjs-default-skin.vjs-paused');
            bpb2.style = "display:block";
            if(!me.player.paused()) me.player.bigPlayButton.hide();
            */
        });

        
        me.player.pluginEzStation(_.merge(this.opts, {
            handlers: {
                markClick: function (player, type, time) {
                    // 註解:可以自行判斷是否新增標記, 如:當標記相近則不予新增
                    // 註解:請依據需求實作(利用ApiSrv為接口)
                    let id = Math.floor(Date.now() / 1000)
                    player.ezStation.createMark({id: id, type: type, time: time})
                },
            }
        }))
        
        
        me.player.controlBar.addChild('QualitySelector')

        me.player.on('loadeddata', function () {
            //console.log('lodeddata',this.currentTime(), me.seekTime)
            if(me.seekTime > this.currentTime()) {
                me.player.currentTime(me.seekTime);
                setTimeout(() => {
                        if(me.seekTime > me.player.currentTime()) me.player.currentTime(me.seekTime);
                }, 2000);
            }
        })

        me.player.on('timeupdate', function () {
            let tVideo = Math.floor(this.currentTime())
            if (tVideo === me.currTime) {
                return
            }
            me.currTime = tVideo
            me.setTbaTime(me.parseVideoTimeToTbaTime(tVideo))
            //console.log('updatetime',me.currTime)
        })

        me.player.on('volumechange', function () {
            me.muted = me.player.muted()
        })

        me.player.on('play', function () {
            me.player.muted(me.muted)
            me.watchReadyState = false;
            me.player.bigPlayButton.hide()
            me.player.posterImage.hide();
            if(!me.narrowScreen) {
                let vdiv = document.getElementById('tbaplayer_video_player');
                vdiv.style = "width:100%; height:100%";
                console.log("autosize")
            }
        })

        me.player.on('pause', function () {
            me.player.bigPlayButton.show()
        })

        me.player.on('ended', function () {
            me.setTrackInfo({track: me.track + 1, time: 0})
        })

        window.addEventListener('resize', _.debounce(function () {
            //me.player.ezStation.resize()
            if(me.narrowScreen) {
                if(window.innerWidth > window.innerHeight) {
                    me.player.width(window.innerWidth)
                    me.player.height(window.innerHeight-24)
                } else {
                    me.player.width(me.dimension.width)
                    me.player.height(me.dimension.height)
                }
            }
            //console.log('resize')
        }, 250))
    }
}
</script>

<style>
    .vjs-default-skin.vjs-paused .vjs-big-play-button {
        display: none;
    }
.vjs-default-skin .vjs-time-divider,
.vjs-default-skin .vjs-captions-button {
    display: none;  
}
.video-js.vjs-has-started .vjs-poster {
  display: none;
}
</style>
