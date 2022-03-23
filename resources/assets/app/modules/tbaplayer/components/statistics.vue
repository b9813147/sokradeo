<i18n>
{
    "en": {
        "statistics": {
            "techFuns"            : "Tech Functions",
            "freqOfTechUsage"     : "Tech Func Triggered Times",
            "accumTimeOnTechUsage": "Tech Func Triggered Duration",
			"second"              : "(sec)",
            "techInteractIdx"     : "Tech Interaction",
            "methodAnal"          : "Pedagogical Application",
            "contentIdx"          : "Content Implementation"
        },
        "charts": {
            "techFuns": {
                "legends": ["Freq", "Duration"]
            },
            "techInteractIdx": {
                "name": "Tech Interaction Idx",
                "data": {
                    "name": "Idx"
                }
            },
            "methodAnal": {
                "name": "App Idx"
            },
            "contentIdx": {
                "waitAssessment": "Wait for assessment"
            }
        }
    },

    "tw": {
        "statistics": {
            "techFuns"            : "科技運用",
            "freqOfTechUsage"     : "教學科技運用次數",
            "accumTimeOnTechUsage": "教學科技運用時數",
            "second"              : "(秒)",
            "techInteractIdx"     : "科技互動",
            "methodAnal"          : "教法應用",
            "contentIdx"          : "教材實踐"
        },
        "charts": {
            "techFuns": {
                "legends": ["次數", "累計"]
            },
            "techInteractIdx": {
                "name": "科技互動",
                "data": {
                    "name": "互動指數"
                }
            },
            "methodAnal": {
                "name": "應用指數"
            },
            "contentIdx": {
                "waitAssessment": "待評定"
            }
        }
    },

    "cn": {
        "statistics": {
            "techFuns"            : "科技运用",
            "freqOfTechUsage"     : "教学科技运用次数",
            "accumTimeOnTechUsage": "教学科技运用时数",
            "second"              : "(秒)",
            "techInteractIdx"     : "科技互动",
            "methodAnal"          : "教法应用",
            "contentIdx"          : "教材实践"
        },
        "charts": {
            "techFuns": {
                "legends": ["次数", "累计"]
            },
            "techInteractIdx": {
                "name": "科技互动",
                "data": {
                    "name": "互动指数"
                }
            },
            "methodAnal": {
                "name": "应用指数"
            },
            "contentIdx": {
                "waitAssessment": "待评定"
            }
        }
    }
}
</i18n>

<template>
<Row>
    <Col span="12" class="block-r-r">
        <Card class="chart-annex">
            <span slot="title">{{ $t('statistics.techInteractIdx') }}</span>
            <span slot="extra">
                <trafficlight :id="'light-techInteractIdx'" :light="statistics.lightTechInteractIdx"></trafficlight>
            </span>
			<div class="chart-annex-main" ref="techInteractIdx"></div>
        </Card>
        <Card class="chart-annex">
            <span slot="title">{{ $t('statistics.methodAnal') }}</span>
			<span slot="extra">
                <trafficlight :id="'light-methodAnal'" :light="statistics.lightMethodAnal"></trafficlight>
            </span>
            <div class="chart-annex-main" ref="methodAnal"></div>
        </Card>
        <Card class="chart-annex">
            <span slot="title">{{ $t('statistics.contentIdx') }}</span>
			<span slot="extra">
                <trafficlight :id="'light-contentIdx'" :light="statistics.lightContentIdx"></trafficlight>
            </span>
            <div class="chart-annex-main" ref="contentIdx"></div>
        </Card>
    </Col>
    <Col span="12" class="block-r-l">
		<Card class="chart-annex">
            <span slot="title">{{ $t('statistics.freqOfTechUsage') }}</span>
            <div class="chart-annex-main" ref="freqOfTechUsage"></div>
        </Card>
        <Card class="chart-annex">
            <span slot="title">{{ $t('statistics.accumTimeOnTechUsage') }}</span>
			<span slot="title" style="font-size: smaller">{{ $t('statistics.second') }}</span>
            <div class="chart-annex-main" ref="accumTimeOnTechUsage"></div>
        </Card>
    </Col>
</Row>
</template>

<script>
import _    from 'lodash'
import Vuex from 'vuex'
import trafficlight from './chart-trafficlight'

export default {

    data () {
        return {
            statistics: {
                //techFuns: null,
                freqOfTechUsage     : null,
                accumTimeOnTechUsage: null,
                techInteractIdx     : null,
                methodAnal          : null,
                contentIdx          : null,
                lightTechInteractIdx: 'N', //科技互動燈號
                lightMethodAnal     : 'N', //教法應用燈號
                lightContentIdx     : 'N', //教材實踐燈號
            },
        }
    },

    computed: _.merge(
        Vuex.mapState('tbaplayer', {
            apiSrv   : state => state.apiSrv,
            preTbaId : state => state.preTbaId,
            tba      : state => state.tba,
            flushTime: state => state.flushTime,
        }),
    ),

    watch: {

        flushTime (v) {
            this.init()
        },

    },

    methods:  _.merge(

        Vuex.mapActions("tbaplayer", ["setDisplayAllowed"]),

        {

        init () {
            if (this.tba.id === this.preTbaId) {
                return
            }

            this.initChartTechFuns(null)
            this.initTechInteractIdx(null)
            this.initMethodAnal(null)
            this.initContentIdx(null)

            let promises = []
            let promise  = null

            // 科技運用
            promise = this.apiSrv.getInstance().getStatistics(this.tba.id, 'TechFuns').then((data) => {
                if(! data.status) {
                    return false
                }
                this.initChartTechFuns(data.data)
                return true
            })
            promises.push(promise)

            // 科技互動
            promise = this.apiSrv.getInstance().getStatistics(this.tba.id, 'TechInteractIdx').then((data) => {
                if(! data.status) {
                    return false
                }
                this.initTechInteractIdx(data.data)
                return true
            })
            promises.push(promise)

            // 教法應用
            promise = this.apiSrv.getInstance().getStatistics(this.tba.id, 'MethodAnal').then((data) => {
                if(! data.status) {
                    return false
                }
                this.initMethodAnal(data.data)
                return true
            })
            promises.push(promise)

            // 教材實踐
            promise = this.apiSrv.getInstance().getStatistics(this.tba.id, 'ContentIdx').then((data) => {
                if(! data.status) {
                    return false
                }
                this.initContentIdx(data.data)
                return true
            })
            promises.push(promise)

            Promise.all(promises).then((datas) => {
                var stats = this.statistics
                window.addEventListener('resize', _.debounce(function () {
                    //if (stats.techFuns !== null) {stats.techFuns.resize()}
                    if (stats.freqOfTechUsage      !== null) {stats.freqOfTechUsage.resize()     }
                    if (stats.accumTimeOnTechUsage !== null) {stats.accumTimeOnTechUsage.resize()}
                    if (stats.techInteractIdx      !== null) {stats.techInteractIdx.resize()     }
                    if (stats.methodAnal           !== null) {stats.methodAnal.resize()          }
                    if (stats.contentIdx           !== null) {stats.contentIdx.resize()          }
                }, 250))
            })
        },

        initChartTechFuns (data) {
            if (data === null) {
                this.statistics.freqOfTechUsage.setOption(
                    require('../options/statistics/chart-freq-of-tech-usage').default()
                )
                this.statistics.accumTimeOnTechUsage.setOption(
                    require('../options/statistics/chart-accum-time-on-tech-usage').default()
                )
                return
            }

            if (data.labels.length === 0) {
                return
            }

            // 修正ECharts:當軸使用指數類型時(log), 資料為零會導致計算錯誤, 須以NULL代替之
            data.labels.forEach((v, i) => {
                if (data.freqs[i]     === 0) {data.freqs[i]     = null}
                if (data.durations[i] === 0) {data.durations[i] = null}
            })

            let legends = this.$t('charts.techFuns.legends')
            let opts    = null

            // (1)科技運用次數
            opts = require('../options/statistics/chart-freq-of-tech-usage').default()
            //opts.xAxis.name     = legends[0]
            opts.yAxis.data     = data.labels
            opts.series[0].name = legends[0]
            opts.series[0].data = data.freqs
            //this.statistics.freqOfTechUsage = echarts.init(this.$refs.freqOfTechUsage)
            this.statistics.freqOfTechUsage.setOption(opts)

            // (2)科技運用時間
            let tmpData = {
                labels: [],
                data  : [],
            }
            data.durations.forEach((v, i) => {
                if (v === null) {
                    return
                }
                tmpData.labels.push(data.labels[i])
                tmpData.data.push(v)
            })
            opts = require('../options/statistics/chart-accum-time-on-tech-usage').default()
            //opts.xAxis.name     = legends[1]
            opts.yAxis.data     = tmpData.labels
            opts.series[0].name = legends[1]
            opts.series[0].data = tmpData.data
            //this.statistics.accumTimeOnTechUsage = echarts.init(this.$refs.accumTimeOnTechUsage)
            this.statistics.accumTimeOnTechUsage.setOption(opts)
        },

        initTechInteractIdx (data) {
            if (data === null) {
                this.statistics.techInteractIdx.setOption(
                    require('../options/statistics/chart-tech-interact-idx').default()
                )
                return
            }

            if (
                typeof data.idx === "undefined" ||
                data.idx === null ||
                data.idx === 0
            ) {
                // Disable block R switch if tech interact index is NOT given
                this.setDisplayAllowed({
                    key: "statistics",
                    val: false,
                });
                return;
            }

            data.idx = _.round(data.idx)

            let opts = require('../options/statistics/chart-tech-interact-idx').default()
            opts.graphic[0].children[1].style.fill = this.getIdxColor(data.idx)
			this.statistics.lightTechInteractIdx = this.getIdxLight(data.idx); //燈號
            opts.graphic[0].children[1].style.text = data.idx
            opts.series[0].name = this.$t('charts.techInteractIdx.name')
            opts.series[0].data[0] = {
                name : this.$t('charts.techInteractIdx.data.name'),
                value: data.idx,
            }
			opts.series[0].axisLine.lineStyle.color[0][0] = data.idx/100;
            //this.statistics.techInteractIdx = echarts.init(this.$refs.techInteractIdx)
            this.statistics.techInteractIdx.setOption(opts)
        },

        initMethodAnal (data) {
            if (data === null) {
                this.statistics.methodAnal.setOption(
                    require('../options/statistics/chart-method-anal').default()
                )
                return
            }

            if (typeof data.value === 'undefined' || data.value === null ||
                typeof data.items === 'undefined' || data.items === null ||
                data.items.labels.length === 0
            ) {
                return
            }

            data.value = _.round(data.value)

            let opts = require('../options/statistics/chart-method-anal').default()
            //色表
            let polarColorList = [ ['rgba(106, 250, 150, 0.86)'],['rgba(252, 0, 49, 0.86)'],['rgba(27, 200, 241, 0.86)'],['rgba(239, 169, 21, 0.86)'],['rgba(164, 89, 193, 0.86)'],['rgba(149, 58, 203, 0.86)'] ];
            // 字串分行
            // for(let itemindex in data.items.labels) {
            //     let stringLength = data.items.labels[itemindex].length;
            //     let insertPosition = Math.round(stringLength/2);
            //     data.items.labels[itemindex] = data.items.labels[itemindex].slice(0, insertPosition) + "\n" + data.items.labels[itemindex].slice(insertPosition);
            // }
			opts.graphic[0].children[1].style.fill = this.getIdxColor(data.value)
			this.statistics.lightMethodAnal = this.getIdxLight(data.value); //燈號
            opts.graphic[0].children[1].style.text = data.value
            opts.angleAxis.data = data.items.labels
            opts.series[0].name = this.$t('charts.methodAnal.name')
            opts.series[1].splitNumber = data.items.labels.length; //分隔線
            opts.series[1].startAngle = 90 - Math.floor(360 / data.items.labels.length / 2);
            opts.series[1].endAngle = opts.series[1].startAngle + 359.99;
            //opts.series[0].data = data.items.values 資料加入顏色架構，原值廢除
            opts.series[0].data   = _.map(data.items.values, (v, i) => {
                return {
                    value: v, 
                    itemStyle: {'normal': { 'color': polarColorList[i] }}
                }
            })
            //this.statistics.methodAnal = echarts.init(this.$refs.methodAnal)
            this.statistics.methodAnal.setOption(opts)
        },

        initContentIdx (data) {
            if (data === null) {
                this.statistics.contentIdx.setOption(
                    require('../options/statistics/chart-content-idx').default()
                )
                return
            }
			let nullDataFlg = false;
            if (typeof data.value === 'undefined' || data.value === null ||
                typeof data.items === 'undefined' || data.items === null ||
                data.items.labels.length === 0
            ) {
                nullDataFlg = true;
                data.value = this.$t('charts.contentIdx.waitAssessment')
            }

            data.value = _.isNumber(data.value) ? _.round(data.value) : data.value

            // 字串分行
            // for(let itemindex in data.items.labels) {
            //     let stringLength = data.items.labels[itemindex].length;
            //     let insertPosition = Math.round(stringLength/2);
            //     data.items.labels[itemindex] = data.items.labels[itemindex].slice(0, insertPosition) + "\n" + data.items.labels[itemindex].slice(insertPosition);
            // }
			
			let opts = require('../options/statistics/chart-content-idx').default()
            opts.graphic[0].children[1].style.fill = this.getIdxColor(data.value)
			this.statistics.lightContentIdx = this.getIdxLight(data.value); //燈號
            opts.graphic[0].children[1].style.text = data.value
			if(nullDataFlg) {
                opts.graphic[0].children[1].style.font = 'bold 20px Microsoft YaHei'
            }
            opts.radar.splitNumber = data.items.labels.length
            opts.radar.indicator   = _.map(data.items.labels, (v) => {
                return {name: v, max: 1}
            })
            opts.series[0].data.push(data.items.values)
            //this.statistics.contentIdx = echarts.init(this.$refs.contentIdx)
            this.statistics.contentIdx.setOption(opts)
        },

        getIdxColor (idx) {
            let color = '#fff'
            if (!_.isNumber(idx)) {
                return color
            }
            if (idx >= 70) {
                color = '#32d875'
            } else if (idx >= 50) {
                color = '#FFD700'
            } else {
                color = '#FF0000'
            }
            return color
        },
		getIdxLight (idx) {
            let lightStatus = 'N';
            if (!_.isNumber(idx)) {
                return lightStatus
            }
            if (idx >= 70) {
                lightStatus = 'G'
            } else if (idx >= 50) {
                lightStatus = 'Y'
            } else {
                lightStatus = 'R'
            }
            return lightStatus
        },

        makeUp () {

            // make up: charts rendered in hidden DIVs will not display until browser resize
            if (
                this.statistics.freqOfTechUsage.getWidth()      === 0 ||
                this.statistics.accumTimeOnTechUsage.getWidth() === 0 ||
                this.statistics.techInteractIdx.getWidth()      === 0 ||
                this.statistics.methodAnal.getWidth()           === 0 ||
                this.statistics.contentIdx.getWidth()           === 0
            ) {
                this.statistics.freqOfTechUsage.resize()
                this.statistics.accumTimeOnTechUsage.resize()
                this.statistics.techInteractIdx.resize()
                this.statistics.methodAnal.resize()
                this.statistics.contentIdx.resize()
            }
        },
    }),

	components: {
        trafficlight,
    },
	
    mounted () {
        this.statistics.freqOfTechUsage      = echarts.init(this.$refs.freqOfTechUsage)
        this.statistics.accumTimeOnTechUsage = echarts.init(this.$refs.accumTimeOnTechUsage)
        this.statistics.techInteractIdx      = echarts.init(this.$refs.techInteractIdx)
        this.statistics.methodAnal           = echarts.init(this.$refs.methodAnal)
        this.statistics.contentIdx           = echarts.init(this.$refs.contentIdx)
    }

}
</script>

<style lang="scss">
.tbaplayer {
    .chart-annex {
        .ivu-card-body {
            padding: 0;
            height : calc(100% - 38.6px);
        }
    }
}
</style>

<style lang="scss" scoped>
.ivu-card {
    background-color: #191c23;
}
.ivu-card-bordered {
    border: 2px solid #191c23;
}

.chart-annex {
    padding: 0;

    .chart-annex-main {
        height             : 100%;
        background-position: center;
        background-size    : contain;
        background-repeat  : no-repeat;
		background-color   : #232731;
    }
	
	.ivu-card-extra {
        right: 10px;
        top: 10px;
    }
}

.block-r-l {
    .chart-annex {
        height: 50vh;
    }
}

.block-r-r {
    .chart-annex {
        height: 33.33vh;
    }
}
</style>
