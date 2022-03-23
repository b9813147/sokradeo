<script>
import _                  from 'lodash'
import Vuex               from 'vuex'
import CpntChartLinerange from './chart-linerange.vue'

export default {

    mixins: [CpntChartLinerange],

    data () {
        return {

        }
    },

    computed: _.merge(
        Vuex.mapState('tbaplayer', {
            fragChecked  : state => state.fragChecked,
            events       : state => state.analEvents,
            appointedTime: state => state.appointedTime,
            seekTime     : state => state.seekTime
        }),
    ),

    watch: {

        sectMap (v) {
            this.initTrackInfo(true)
        },

        tbaTime (v) {
            // 片段合法區間計算
            // 因為時間換算誤差(-1.5 ~ +1.5) 故增大合法區間條件
            //if (this.fragChecked && !_.inRange(v, this.tba.frag.start - 2, this.tba.frag.end + 1)) {
            if (this.fragChecked && v > (this.tba.frag.end + 1)) {
                this.setPaused(true)
                this.nextCurrPlay()
                return
            }
        },

    },

    methods: _.merge(

        Vuex.mapActions('tbaplayer', [
            'initPlayer', 'nextCurrPlay',
        ]),

        {
            declarePlugins () {
                let me = this

                let plugin = new Chart.plugin.linerange('linerange')
                plugin.afterEvent = function(chart, e, opts) {

    				if( e.type !== 'click' ) {
    					return
    				}

    				let data = this.getActiveInfo(chart)
    				if (data === null) {
    					return
    				}

                    let tTba = Array.isArray(data.data) ? data.data[0] : data.data
                    me.setTrackInfo(me.parseTbaTimeToTrackInfo(tTba))
    			}

                return {
                    plugins: [plugin],
                    options: {linerange: {}},
                }
            },

            init () {
                if (this.tba.id === this.preTbaId) {
                    this.initTrackInfo(true)
                    return
                }
                this.initPlayer()
            },

            initTrackInfo (forceUpdated) {
                let time = _.isNil(this.tba.frag) ? (_.isNil(this.appointedTime.start) ? 0 : this.appointedTime.start) : this.tba.frag.start
                let info = this.parseTbaTimeToTrackInfo(time)
                info = info === null
                    ? {track: 0, time: 0, forceUpdated: forceUpdated}
                    : _.merge(info, {forceUpdated: forceUpdated})
                this.setTrackInfo(info)
                //console.log('InitTrack',info, this.seekTime)
            },
        }
    ),

    mounted () {
        this.funs.localtime = true
    }

}
</script>

<style lang="scss" scoped>

</style>
