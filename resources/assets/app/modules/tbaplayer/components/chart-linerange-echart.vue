<script>
import _    from 'lodash'
import Vuex from 'vuex'

export default {

    template: '<div v-bind:id="id" style="width:100%; height:100%"></div>',

    props: ['id', 'options'],

    emits: ["reset-comment-detail"],

    data () {
        return {
            chart: null,
            funs : {
                localtime: false,
            },
            charttext: {
                plus: {},
                minus: {}
            },
            chartimage: {
                plus: {},
                minus: {}
            },
            eventid: {
                plus: {},
                minus: {}
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

        flushTime (v) {
            this.init()
            if(this.chart === null) {
                return
            }
            //this.chart.options.plugins.linerange.frag = _.isNil(this.tba.frag) ? null : this.tba.frag
        },

        eventRange (v) {
            if(this.chart === null) {
                return
            }
            let option = this.chart.getOption();
            option.xAxis[0].min = Math.floor(v.min / 300) * 300;
            option.xAxis[0].max = Math.ceil (v.max / 300) * 300;
            option.xAxis[1].min = Math.floor(v.min / 300) * 300;
            option.xAxis[1].max = Math.ceil (v.max / 300) * 300;
            this.chart.setOption(option, true);
        },

        events (v) {
            this.initChart(v)
        },

        tbaTime (v) {
            if (this.chart === null || v < 1) return;
            let option = this.chart.getOption();
            option.series[0].markLine.data[0].xAxis = v;
            option.series[1].markLine.data[0].xAxis = v;
			this.chart.setOption(option, true);
        },
       

    },

    methods: _.merge(

        Vuex.mapActions('tbaplayer', [
            'setTrackInfo',
            'setPaused',
            'shiftTbaVideoMap',
            'setBlockR',
        ]),

        {
            initChart (events) {
                let vm = this;
                if(this.chart !== null) {
                    this.updateChart();
                    return
                }
                let datasets = vm.toChartDatasets(events.datasets);
                let datasetsplus = (typeof datasets.plus.value !== 'undefined') ? datasetsplus = datasets.plus.value : [];
                let datasetsminus = (typeof datasets.minus.value !== 'undefined') ? datasets.minus.value : [];

                this.chart = echarts.init(document.getElementById(vm.id));
                let lineStyleColor = {};
                lineStyleColor['plus'] = 'rgb(61, 205, 178)';
                lineStyleColor['minus'] = 'rgb(10, 126, 215)';
                let areaStyleColor = {};
                areaStyleColor['plus'] = new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                    offset: 0, color: 'rgba(61, 205, 178, 0.6)' // 0%
                    }, {
                    offset: 1, color: 'rgba(0, 163, 131, 0.2)' // 100%
                    }], false);
                areaStyleColor['minus'] = new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                        offset: 0, color: 'rgba(128, 221, 232, 0.2)' // 0%
                    }, {
                        offset: 1, color: 'rgba(10, 126, 215, 0.6)' // 100%
                    }], false); 
                let option = {
                        title:{
                            show: "true",
                            text: vm.$i18n.t('linerange.annotation.title'),
                            textStyle: {
                                color: '#fff',
                                fontStyle: 'normal',
                                fontWeight: 'normal',
                                fontSize: 12,
                            },
                            top:"24",
                            left: "1%",
                            textAlign: "left",
                        },
                        grid: [{
                            top: '10px',
                            right: '10px',
                            left: '80px',
                            height: '25px'

                        }, {
                            top: '35px',
                            left: '80px',
                            right: '10px',
                            bottom: '5px',
                            height: '25px'
                        }],
                        // tooltip: {
                        //     trigger: 'axis',
                        //     confine: false,
                        //     axisPointer: {
                        //         animation: false
                        //     },
                        //     textStyle:{
                        //         fontSize:'small',
                        //     },
                        //     position: function (pos, params, dom, rect, size) {
                        //         var obj = [0, pos[1]-10-size.contentSize[1]];
                        //         if(size.viewSize[0] - pos[0] > size.contentSize[0]) {
                        //             obj[0] = pos[0]+10;
                        //         } else if(pos[0] > size.contentSize[0]) {
                        //             obj[0] = pos[0]-10-size.contentSize[0];
                        //         } else {
                        //             obj[['left', 'right'][+(pos[0] < size.viewSize[0] / 2)]] = 5;
                        //         }
                        //         return obj;  
                        //     },
                        //     formatter: function (tipData) {
                        //         let tmpText = "";
                        //         let resultTxt = "";
                        //         let pointCount = 0;
                        //         resultTxt += '<div style="float:left;">';
                        //         tipData = tipData[0];
                        //         if(tipData['seriesName'] == 'plus') {
                        //             for(let txtIndex in vm.charttext.plus[tipData.axisValue]) {
                        //                 if(tmpText == "") {
                        //                     tmpText = vm.charttext.plus[tipData.axisValue][txtIndex].text;
                        //                 } else {
                        //                     tmpText += "<br/>"+vm.charttext.plus[tipData.axisValue][txtIndex].text
                        //                 }
                        //                 pointCount++;
                        //             }
                        //         } else if(tipData['seriesName'] == 'minus') {
                        //             for(let txtIndex in vm.charttext.minus[tipData.axisValue]) {
                        //                 if(tmpText == "") {
                        //                     tmpText = vm.charttext.minus[tipData.axisValue][txtIndex].text;
                        //                 } else {
                        //                     tmpText += "<br/>"+vm.charttext.minus[tipData.axisValue][txtIndex].text
                        //                 }
                        //                 pointCount++;
                        //             }
                        //         }
                        //         resultTxt += pointCount+vm.$i18n.t('linerange.annotation.unit')+'</div>';
                        //         resultTxt += '<div style="float:left;margin-left:5px;">'+tmpText+'</div>';
                                
                        //         if(pointCount > 0) {
                        //             return resultTxt;
                        //         }
                                
                        //     }
                        // },
                        xAxis: [{
                            min: Math.floor(vm.eventRange.min / 300) * 300,
                            max: Math.ceil (vm.eventRange.max / 300) * 300,
                            interval: 300,
                            axisLine : {
                                show: true,
                            },
                            splitLine : {
                                show: true,
                                lineStyle: {
                                    color: '#7A8297',
                                    type: 'solid',
                                    width: 1
                                }
                            },
                            axisTick: {
                                show:false,
                            },
                            axisPointer: {
                                show: false,
                                type: 'none',
                            },
                            axisLabel : {
                                show:false,
                                formatter: function (v, index) {
                                    return parseInt(v / 60);
                                }
                            },
                        }, {
                            gridIndex: 1,
                            min: Math.floor(vm.eventRange.min / 300) * 300,
                            max: Math.ceil (vm.eventRange.max / 300) * 300,
                            interval: 300,
                            axisLine : {
                                show: true,
                            },
                            splitLine : {
                                show: true,
                                lineStyle: {
                                    color: '#7A8297',
                                    type: 'solid',
                                    width: 1
                                }
                            },
                            axisTick: {
                                show:false,
                            },
                            axisPointer: {
                                show: false,
                                type: 'none'
                            },
                            axisLabel : {
                                show:false,
                                formatter: function (v, index) {
                                    return parseInt(v / 60);
                                }
                            },
                        }],
                        yAxis: [
                            {
                                type : 'value',
                                axisLine : {
                                    show: false,
                                },
                                splitLine : {
                                    show: false,
                                },
                                axisTick: {
                                    show:false,
                                },
                                axisLabel : {
                                    show:false,
                                },
                            },
                            {
                                gridIndex: 1,
                                type : 'value',
                                inverse: true,
                                axisLine : {
                                    show: false,
                                },
                                splitLine : {
                                    show: false,
                                },
                                axisTick: {
                                    show:false,
                                },
                                axisLabel : {
                                    show:false,
                                },
                            }
                        ],
                        series: [{
                            name: 'plus',
                            symbolSize: 10,
                            data: datasetsplus,
                            type: 'line',
                            smooth: true,
                            silent: false,
                            itemStyle: { 
                                normal: {color: lineStyleColor['plus']} 
                            },
                            areaStyle: {
                                 normal: {color: areaStyleColor['plus']}
                            },
                            markLine : {
                                silent: true,
                                lineStyle: {
                                    normal: {
                                        type: 'solid',
                                        color: 'red',
                                    }
                                },
                                symbol: 'none',
                                animation: false,
                                label: {
                                    show: false
                                },
                                data : [
                                    {
                                        xAxis: 0
                                    },
                                ]
                            }
                        }, {
                            name: 'minus',
                            symbolSize: 10,
                            data: datasetsminus,
                            type: 'line',
                            xAxisIndex: 1,
                            yAxisIndex: 1,
                            smooth: true,
                            silent: false,
                            itemStyle: { 
                                normal: {color: lineStyleColor['minus']} 
                            },
                            areaStyle: {
                                 normal: {color: areaStyleColor['minus']}
                            },
                            markLine : {
                                silent: true,
                                lineStyle: {
                                    normal: {
                                        type: 'solid',
                                        color: 'red',
                                    }
                                },
                                symbol: 'none',
                                animation: false,
                                label: {
                                    show: false
                                },
                                data : [
                                    {
                                        xAxis: 0
                                    },
                                ]
                            }
                        }]
                    };

                    this.chart.setOption(option,true);

                    this.chart.on('click', function (params) {
                        vm.setTrackInfo(vm.parseTbaTimeToTrackInfo(params.value[0]));
                        if(vm.showEventModal) { //ajax取得個人點評資訊
                            if(Object.keys( vm.eventid[params.seriesName][params.value[0]] ).length >= 1) {
                                vm.getEventInfo(vm.eventid[params.seriesName][params.value[0]][0], params.value[0]);
                            }
                        } else {
                            let textresult = [];
                            let imageresult = [];
                            if(Object.keys( vm.charttext[params.seriesName][params.value[0]] ).length > 0) {
                                vm.eventInfoTotal.clock = vm.toHHMMSS(params.value[0])
                                for(let textIndex in vm.charttext[params.seriesName][params.value[0]]) {
                                    if(textresult != '') {
                                        textresult.push("\n" + vm.charttext[params.seriesName][params.value[0]][textIndex].text);
                                        imageresult.push(vm.chartimage[params.seriesName][params.value[0]][textIndex]);
                                    } else {
                                        textresult.push(vm.charttext[params.seriesName][params.value[0]][textIndex].text);
                                        imageresult.push(vm.chartimage[params.seriesName][params.value[0]][textIndex]);
                                    }
                                }
                            }
                            vm.eventInfoTotal.text = textresult;
                            vm.eventInfoTotal.image = imageresult;
                            vm.setBlockR('commentlist');
                            //vm.modal.total = true;
                        }
                    });
               
            },

            updateChart () {
                let option = this.chart.getOption();
                let chartDatasets = this.toChartDatasets(this.events);
                option.series[0].data = chartDatasets.plus.value;
                option.series[1].data = chartDatasets.minus.value;
                this.charttext.plus = chartDatasets.plus.text;
                this.charttext.minus = chartDatasets.minus.text;
                this.chartimage.plus = chartDatasets.plus.image;
                this.chartimage.minus = chartDatasets.minus.image;
                this.eventid.plus = chartDatasets.plus.id;
                this.eventid.minus = chartDatasets.minus.id;
                this.chart.setOption(option,true);
                //console.log('eventid',this.eventid)
            },

            toChartDatasets(events) {
                // Create data for chart
                let result = {
                    plus: {
                        value: [
                            [Math.floor(this.eventRange.min / 300) * 300, 0],
                            [Math.ceil(this.eventRange.max / 300) * 300, 0],
                        ],
                        text: {},
                        id: {},
                        image: {},
                    },
                    minus: {
                        value: [
                            [Math.floor(this.eventRange.min / 300) * 300, 0],
                            [Math.ceil(this.eventRange.max / 300) * 300, 0],
                        ],
                        text: {},
                        id: {},
                        image: {},
                    },
                };

                if (Array.isArray(events) && events.length > 0) {
                    for (let eventsIndex in events) {
                        if (
                            typeof events[eventsIndex]["datasets"] !== "undefined" &&
                            Array.isArray(events[eventsIndex]["datasets"]) &&
                            events[eventsIndex]["datasets"].length > 0
                        ) {
                            let datasets = events[eventsIndex]["datasets"][0];
                            let user = events[eventsIndex]["user"];
                            if (
                                typeof datasets["details"] !== "undefined" &&
                                Array.isArray(datasets["details"]) &&
                                datasets["details"].length > 0
                            ) {
                                for (let detailsindex in datasets["details"]) {
                                    if (events[eventsIndex]["labeltypes"][detailsindex] === 0) {
                                        //minus
                                        result["minus"] = this.segmentTbaTime(
                                            datasets["details"][detailsindex],
                                            datasets["eventtexts"][detailsindex],
                                            datasets["ids"][detailsindex],
                                            datasets["eventimgs"][detailsindex],
                                            user,
                                            events[eventsIndex]["labels"][detailsindex],
                                            datasets["labelsmode"][detailsindex],
                                            result["minus"]["value"],
                                            result["minus"]["text"],
                                            result["minus"]["id"],
                                            result["minus"]["image"]
                                        );
                                    } else {
                                        //plus
                                        result["plus"] = this.segmentTbaTime(
                                            datasets["details"][detailsindex],
                                            datasets["eventtexts"][detailsindex],
                                            datasets["ids"][detailsindex],
                                            datasets["eventimgs"][detailsindex],
                                            user,
                                            events[eventsIndex]["labels"][detailsindex],
                                            datasets["labelsmode"][detailsindex],
                                            result["plus"]["value"],
                                            result["plus"]["text"],
                                            result["plus"]["id"],
                                            result["plus"]["image"]
                                        );
                                    }
                                }
                            }
                        }
                    }
                }

                if (Object.keys(result["plus"]["text"]).length > 0) {
                    for (let chartXaxis in result["plus"]["text"]) {
                        result["plus"]["text"][chartXaxis] = result["plus"]["text"][
                            chartXaxis
                        ].sort(function(a, b) {
                            return a.value > b.value ? 1 : -1;
                        });
                    }
                }

                return result;
            },

            //切割Tba時間 (秒=>分 並統計個數、製作tooltip內容)
            segmentTbaTime (detail, eventtexts, ids, images, user, label, labelmodes, chartValue, chartText, chartId, chartImage) {
                // console.log(ids, images);
                let result = { 'value': [], 'text': [], 'image': []};
                let segVal = 60; //切割時間:以一分鐘為單位切割
                let segmentVal = {};
                let segmentTxt = {};
                let segmentId = {};
                let segmentImg = {};
                for(let detailsindex in detail) {
                    let segmentkey = Math.floor( detail[detailsindex] / segVal ) * segVal;
                    //value
                    if(typeof segmentVal[segmentkey] === 'undefined') {
                        segmentVal[segmentkey] = 1
                    } else {
                        segmentVal[segmentkey]++;
                    }
                    //text
                    if(typeof eventtexts[detailsindex] !== 'undefined') {
                        let context = '';
                        let labelmode = ''
                        if(typeof labelmodes[detailsindex] !== 'undefined') {
                            labelmode += ' '+labelmodes[detailsindex];
                        }
                        context += '['+label+' '+labelmode+'] ';
                        if(typeof user['name'] !== 'undefined') {
                            context += user['name']+'：';
                        }
                        context += eventtexts[detailsindex];
                        if(typeof segmentTxt[segmentkey] === 'undefined') {
                            segmentTxt[segmentkey] = [{text: context, value: detail[detailsindex]}];
                        } else {
                            segmentTxt[segmentkey].push( {text: context, value: detail[detailsindex]} );
                        }
                    }
                    //id
                    
                    if(typeof segmentId[segmentkey] === 'undefined') {
                        segmentId[segmentkey] = [ ids[detailsindex] ];
                    } else {
                        segmentId[segmentkey].push( ids[detailsindex] );
                    }
                    //image
                    if(typeof images[detailsindex] !== 'undefined') {
                        if(typeof segmentImg[segmentkey] === 'undefined') {
                            segmentImg[segmentkey] = [ images[detailsindex] ];
                        } else {
                            segmentImg[segmentkey].push( images[detailsindex] );
                        }
                    }
                }
                // console.log(segmentTxt);
                // console.log(chartText);
                //value
                if(Object.keys(segmentVal).length > 0) {
                    for(let chartXaxis in segmentVal) {
                        let existkey = 0;
                        if(Object.keys(chartValue).length > 0) {
                            for(let chartIndex in chartValue) {
                                if(chartValue[chartIndex][0] == parseInt(chartXaxis)) {
                                    chartValue[chartIndex][1] += parseInt(segmentVal[chartXaxis]);
                                    existkey = 1;
                                }
                            }
                        }
                        if(existkey == 0) {
                            chartValue.push([parseInt(chartXaxis), parseInt(segmentVal[chartXaxis])]);
                        }
                    }
                }
                //text
                if(Object.keys(segmentTxt).length > 0) {
                    for(let chartXaxis in segmentTxt) {
                        let existkey = 0;
                        if(typeof chartText[chartXaxis] !== 'undefined') {
                            chartText[chartXaxis] = chartText[chartXaxis].concat(segmentTxt[chartXaxis]);
                        } else {
                            chartText[chartXaxis] = segmentTxt[chartXaxis];
                        }
                    }
                }
                //id
                if(Object.keys(segmentId).length > 0) {
                    for(let chartXaxis in segmentId) {
                        let existkey = 0;
                        if(typeof chartId[chartXaxis] !== 'undefined') {
                            chartId[chartXaxis] = chartId[chartXaxis].concat(segmentId[chartXaxis]);
                        } else {
                            chartId[chartXaxis] = segmentId[chartXaxis];
                        }
                    }
                }
                //image
                if(Object.keys(segmentImg).length > 0) {
                    for(let chartXaxis in segmentImg) {
                        let existkey = 0;
                        if(typeof chartImage[chartXaxis] !== 'undefined') {
                            chartImage[chartXaxis] = chartImage[chartXaxis].concat(segmentImg[chartXaxis]);
                        } else {
                            chartImage[chartXaxis] = segmentImg[chartXaxis];
                        }
                    }
                }
                //排序
                chartValue.sort(function(a,b) {
                    if (a[0] === b[0]) {
                        return 0;
                    }
                    else {
                        return (a[0] < b[0]) ? -1 : 1;
                    }
                });

                result['value'] = chartValue;
                result['text'] = chartText;
                result['id'] = chartId;
                result['image'] = chartImage;
                return result;
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
