<i18n>
{
    "en": {
        "linerange": {
            "annotation": {
                "localtime": "drag to position the timeline"
            }
        }
    },

    "tw": {
        "linerange": {
            "annotation": {
                "localtime": "拖曳以定位時間軸"
            }
        }
    },

    "cn": {
        "linerange": {
            "annotation": {
                "localtime": "拖曳以定位时间轴"
            }
        }
    }
}
</i18n>

<script>
import _    from 'lodash'
import Vuex from 'vuex'

export default {

    template: '<canvas v-bind:id="id"></canvas>',

    props: ['id'],

    emits: ["reset-comment-detail"],

    data () {
        return {
            chart: null,
            funs : {
                localtime: false,
            },
        }
    },

    computed: _.merge(
        Vuex.mapState('tbaplayer', {
            mode       : state => state.mode,
            preTbaId   : state => state.preTbaId,
            tba        : state => state.tba,
            flushTime  : state => state.flushTime,
            fragChecked: state => state.fragChecked,
            sectMap    : state => state.sectMap,
            eventRange : state => state.eventRange,
            tbaTime    : state => state.tbaTime,
        }),
    ),

    watch: {

        mode (v) {
            if(this.chart === null) {
                return
            }
            let localTimeOpts = this.chart.annotation.elements.localtime.options
            switch (v) {
                case 'general':
                    localTimeOpts.borderColor   = 'rgba(0,0,0,0)'
                    localTimeOpts.label.enabled = false
                    localTimeOpts.draggable     = false
                    break
                case 'edit':
                    if (this.funs.localtime) {
                        localTimeOpts.borderColor   = '#0099ff'
                        localTimeOpts.label.enabled = true
                        localTimeOpts.draggable     = true
                        localTimeOpts.value         = this.tbaTime
                    }
                    break
                default:
                    return
            }
            this.chart.update(0)
        },

        flushTime (v) {
            this.init()
            if(this.chart === null) {
                return
            }
            this.chart.options.plugins.linerange.frag = _.isNil(this.tba.frag) ? null : this.tba.frag
        },

        eventRange (v) {
            if(this.chart === null) {
                return
            }
            this.chart.options.scales.xAxes[0].ticks.min = Math.floor(v.min / 300) * 300
            this.chart.options.scales.xAxes[0].ticks.max = Math.ceil (v.max / 300) * 300
            this.chart.update(0)
        },

        events (v) {
            this.initChart(v)
        },

        tbaTime (v) {
            if (this.chart === null || v < 1) return;
            this.chart.annotation.elements.currtime.options.value  = v
            this.chart.annotation.elements.localtime.options.value = v
			this.chart.update(0)
        },

    },

    methods: _.merge(

        Vuex.mapActions('tbaplayer', [
            'setTrackInfo',
            'setPaused',
            'shiftTbaVideoMap',
        ]),

        {
            initChart (events) {
                if(this.chart !== null) {
    				//this.chart.destroy()
                    //this.chart = null
                    this.updateChart()
                    return
    			}

                let pluginsInfo = this.declarePlugins()
                pluginsInfo.options.linerange.frag = _.isNil(this.tba.frag) ? null : this.tba.frag

                let datasets = this.toChartDatasets(events.datasets)
                this.chart = new Chart(document.getElementById(this.id).getContext('2d'), {
                    plugins: pluginsInfo.plugins,
                    type: 'linerange',
                    data: {
                        labels  : events.labels,
                        datasets: datasets,
                    },
                    options: {
                        plugins  : pluginsInfo.options,
                        maintainAspectRatio: false,
                        animation: { duration: 0 },
                        legend   : { display: false },
                        tooltips : { enabled: false },
                        scales: {
                            xAxes: [{
                                position: 'top',
                                ticks: {
                                    fontColor : '#fff',
                                    fontFamily: '"Microsoft JhengHei", helvetica, "Helvetica Neue", Helvetica, Arial, sans-serif',
                                    callback: (v) => parseInt(v / 60),
                                    stepSize: 300,
                                    min: Math.floor(this.eventRange.min / 300) * 300,
                                    max: Math.ceil (this.eventRange.max / 300) * 300,
                                },
                                gridLines: { color: '#7A8297' },
                            }],
                            yAxes: [{
                            	stacked  : true,
                                ticks: {
                                    fontColor : '#fff',
                                    fontFamily: '"Microsoft JhengHei", helvetica, "Helvetica Neue", Helvetica, Arial, sans-serif',
                                },
                            	gridLines: { color: '#7A8297' },
                                afterFit : (scale) => { scale.width = 120 },
                            }]
                        },
                        annotation: {
                            drawTime   : 'afterDatasetsDraw',
                            events     : ['click'],
                            annotations: [{
                                id         : 'currtime',
                                type       : 'line',
                                mode       : 'vertical',
                                scaleID    : 'x-axis-0',
                                value      : 0,
                                borderColor: 'red',
                                borderWidth: 1
                            }, {
                                id         : 'localtime',
                                type       : 'line',
                                mode       : 'vertical',
                                scaleID    : 'x-axis-0',
                                value      : 0,
                                borderColor: 'rgba(0,0,0,0)',
                                borderWidth: 1,
                                label      : {
                                    backgroundColor: '#005c99',
                                    content: this.$i18n.t('linerange.annotation.localtime'),
                                    enabled: false
                                },
                                draggable  : false,
                                onDragEnd  : (e) => {
                                    this.shiftTbaVideoMap({offset: (e.subject.config.value - this.tbaTime)})
                                },
                            }]
                        },
                    }
                })
            },

            updateChart () {
                this.chart.data.labels   = this.events.labels,
                this.chart.data.datasets = this.toChartDatasets(this.events.datasets)
                this.chart.update(0)
            },

            toChartDatasets (datasets) {
                return _.map(datasets, (v) => { return {
                    label               : '',
                    data                : v.details,
                    backgroundColor     : v.colors,
                    hoverBackgroundColor: 'yellow',
                    pointRadius         : 8,
                    pointStyle          : 'rectRot',
                }})
            },

            parseTbaTimeToTrackInfo (tTba) {
                let valid  = false
                let track  = 0
                let tVideo = 0
                for (let [i, map] of this.sectMap.entries()) {
                    if (tTba < map.range.min || tTba > map.range.max) {
                        continue
                    }
                    track = i
                    for(let sect of map.sects) {
                        if (tTba < sect.tba_start || tTba > sect.tba_end) {
                            continue
                        }
                        tVideo = tTba + sect.video_offset - sect.tba_start
                        valid  = true
                        break
                    }
                    break
                }
                this.$emit("reset-comment-detail");
                return valid ? {track: track, time: tVideo} : null
            },

        }
    ),

}
</script>

<style lang="scss" scoped>

</style>
