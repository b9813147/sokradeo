import _           from 'lodash'
import { graphic } from 'echarts/lib/export'

export default function () {
    return _.cloneDeep(config)
}

const config = {
    tooltip: {
        show: true,
        formatter: function (a) {
            return a.name.replace(/\n/g, '') + "<br/>"+a.seriesName + " : " + a.value ;
        }
        //formatter: "{b} <br/>{a} : {c}",
    },
    angleAxis: {
        type: 'category',
        data: [],
        z: 1,
        axisLabel: {
            show: true,
            interval: 0,
            textStyle: {
                color: '#AAA',
                margin: 0,
            }
        },
        axisTick: {
            show: false,
            lineStyle: {
                color: 'rgb(236, 245, 240)'
            },
            alignWithLabel: true
        },
        axisLine: {
            lineStyle: {
                color: "#AAA",
                width: 1,
                type: "solid"
            }
        },
        splitArea: {
            show: false
        },
    },
    radiusAxis: {
        z: 2,
        axisLabel: {
            show: false
        },
        axisTick: {
            show: false
        },
        axisLine: {
            show: false
        },
        splitLine: {
            show: true,
            lineStyle: {
                type : 'solid',
                color: 'rgba(255, 245, 240, 0.2)'
            }
        },
    },
    polar: {
        radius: '70%'
    },
    series: [
        {
        name: '',
        type: 'bar',
        data: [],
        coordinateSystem: 'polar',
        },
        {
            name: '',
            type: 'gauge',
            radius: '72%',
            clockwise: false,
            animation: false,
            axisTick: {
                show: false
            },
            axisLine: {
                show: false,
                lineStyle: {
                    color: [
                        [1, '#ffffff']
                    ],
                    width: 0,
                    opacity: 0
                }
            },
            splitLine: {
                show: true,
                length: '92%',
                lineStyle: {
                    color: 'rgba(255, 255, 255, 0.2)',
                    width: '1'
                }
            },
            axisLabel: {
                show: false,
            },
            detail: {
                show: false,
            },
        },
    ],
    
    graphic: [{
        type: 'group',
        bounding: 'raw',
        right   : '50%',
        bottom  : '50%',
        z       : 100,
        children: [
            {
                type: 'circle',
                left: 'center',
                top : 'center',
                z   : 100,
                shape: {
                    cx: 0,
                    cy: 0,
                    r : 60,
                },
                style: {
                    fill: new echarts.graphic.RadialGradient(0.5, 0.5, 0.5, [{
                        offset: 0,
                        color: 'rgba(0, 0, 0, 0.4)'
                    }, {
                        offset: 1,
                        color: 'rgba(0, 0, 0, 0)'
                    }], false),
                    shadowBlur: 20,
                }
            }, 
            {
                type: 'text',
                left: 'center',
                top : 'center',
                z   : 100,
                style: {
                    fill: '#fff',
                    text: 0,
                    font: 'bold 60px Microsoft YaHei',
                    textShadowBlur   :10,
                    textShadowColor  : 'rgb(0, 0, 0, 1)',
                    textShadowOffsetY: 0,
                }
            }
        ]
    }],
}
