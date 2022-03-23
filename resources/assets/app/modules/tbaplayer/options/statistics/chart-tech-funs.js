import _ from 'lodash'

export default function () {
    return _.cloneDeep(config)
}

const seriesLabel = {
    normal: {
        show: true,
        textBorderColor: '#333',
        textBorderWidth: 2
    }
}

const config = {
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'shadow'
        }
    },
    legend: {
        textStyle: {
            color   : 'rgb(255, 255, 255)',
            fontSize: '12',
        },
        data: ['次數', '累計']
    },
    grid: {
        left: '30%'
    },
    toolbox: {
        show: true
    },
    xAxis: {
        type: 'log',
        name: '',
        axisLabel: {
            formatter: '{value}',
            textStyle: {
                color: 'rgb(238, 197, 102)'
            }
        },
        axisTick: {
            lineStyle: {
                type : 'dotted',
                color: 'rgba(238, 197, 102, 0.2)',
            },
            alignWithLabel: true
        },
        axisLine: {
            lineStyle: {
                color: 'rgba(238, 197, 102, 0.2)'
            }
        },
        splitLine: {
            show: false
        },
    },
    yAxis: {
        type: 'category',
        scale: true,
        inverse: true,
        boundaryGap: true,
        axisLabel: {
            textStyle: {
                color   : 'rgb(238, 197, 102)',
                //fontSize: '12',
            },
            margin: 8,
        },
        axisTick: {
            lineStyle: {
                type : 'dotted',
                color: 'rgba(238, 197, 102, 0.2)',
            },
            alignWithLabel: true
        },
        axisLine: {
            lineStyle: {
                color: 'rgba(238, 197, 102, 0.2)'
            }
        },
        data: []
    },
    series: [
        {
            name: '',
            type: 'bar',
            label: seriesLabel,
            itemStyle: {
                color: 'rgba(184, 241, 237, 0.8)'
            },
            barMinHeight  : 20,
            barCategoryGap: '20%',
            data: [],
        },
        {
            name: '',
            type: 'bar',
            barGap: '0%',
            itemStyle: {
                color: 'rgba(152, 75, 75, 0.8)'
            },
            label: {
                normal: {
                    formatter: x => {
                        if (x.value == null || x.value == 0) {
                            return ''
                        }

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
                    },
                    show: true,
                    textBorderColor: '#333',
                    textBorderWidth: 1
                }
            },
            data: []
        },
    ]
}
