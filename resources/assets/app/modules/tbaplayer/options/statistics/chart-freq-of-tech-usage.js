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
        data: []
    },
    series: [
        {
            z: 2,
            name: '',
            type: 'bar',
            label: {
                normal: {
                    show: true,
                    position: 'right',
                    color: 'rgba(255, 255, 255, 0.9)',
                },
            },
            itemStyle: {
                color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [{
                    offset: 0, color: '#6afa96' // 0%
                    }, {
                    offset: 1, color: '#42ce67' // 100%
                    }], false),
            },
            barCategoryGap: '20%',
            barMaxWidth: '65%', 
            data: [],
        }
    ]
}
