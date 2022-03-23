import _ from 'lodash'

export default function () {
    return _.cloneDeep(config)
}

const config = {
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'shadow'
        }
    },
    grid: {
        top: '2%',
        // left: '25%',
        bottom: '8%',
        containLabel: true,
    },
    toolbox: {
        show: true
    },
    xAxis: {
        type: 'value',
        name: '',
        axisLabel: {
            formatter: '{value}',
            textStyle: {
                color: 'rgba(255, 255, 255, 0.4)'
            }
        },
        axisTick: {
            show: false,
            lineStyle: {
                type : 'dotted',
                color: 'rgba(255, 255, 255, 0.2)',
            },
            alignWithLabel: true
        },
        axisLine: {
            show: false,
            lineStyle: {
                color: 'rgba(255, 255, 255, 0.2)'
            }
        },
        splitLine: {
            show: true,
            lineStyle: {
                color: 'rgba(255, 255, 255, 0.2)'
            }
        },
        splitNumber: 4,
    },
    yAxis: {
        type: 'category',
        scale: true,
        inverse: true,
        boundaryGap: true,
        axisLabel: {
            textStyle: {
                color   : 'rgba(255, 255, 255, 0.8)',
                //fontSize: '12',
            },
            margin: 8,
        },
        axisTick: {
            show: false,
            lineStyle: {
                type : 'dotted',
                color: 'rgba(255, 255, 255, 0.1)',
            },
            alignWithLabel: true
        },
        axisLine: {
            show: false,
            lineStyle: {
                color: 'rgba(255, 255, 255, 0.1)'
            }
        },
        data: []
    },
    series: [
        {
            z: 2,
            name: '',
            type: 'bar',
            itemStyle: {
                color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [{
                    offset: 0, color: '#80d8e8' // 0%
                    }, {
                    offset: 1, color: '#0a7ed7' // 100%
                    }], false),
            },
            label: {
                normal: {
                    formatter: x => {
                        if (x.value == null || x.value == 0) {
                            return ''
                        }
                        /* 顯示:秒數
                        let hr  = Math.floor(x.value / 3600)
                        let min = Math.floor((x.value - hr * 3600) / 60)
                        let sec = parseInt(x.value - hr * 3600 - min * 60)
                        while (min.length < 2) {
                            min = '0' + min
                        }
                        while (sec.length < 2) {
                            sec = '0' + min
                        }
                        if (hr) hr += ':'
                        return hr + min + ':' + sec
                        */
                        return parseInt(x.value)
                    },
                    show: true,
                    position: 'right',
                    color: 'rgba(255, 255, 255, 0.9)',
                }
            },
            barMaxWidth: '65%', 
            data: []
        }
    ]
}
