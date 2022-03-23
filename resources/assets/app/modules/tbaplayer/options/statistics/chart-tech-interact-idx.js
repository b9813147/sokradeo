import _ from 'lodash'

export default function () {
    return _.cloneDeep(config)
}

const config = {
    tooltip : {
        formatter: "{a} <br/>{b} : {c}"
    },
    toolbox: {
        show: true
    },
    series: [
        { //主表
            name: '',
            type: 'gauge',
            radius: '75%',
            startAngle: 220,
            endAngle  : -40,
            title: {
                offsetCenter:[0, '65%'],
                textStyle: {
                    color      : 'rgb(255, 255, 255, 0.8)',
                    //fontWeight : 'bolder',
                    fontSize   : 16,
                    shadowColor: 'rgb(255, 255, 255)',
                    shadowBlur : 5,
                }
            },
            axisLabel: {
                formatter: x => ((x / 10) % 2) === 0 ? x : '',
                distance: -40,
                textStyle: {
                    color      : 'rgb(255, 255, 255, 0.5)',
                    //fontWeight : 'bolder',
                }
            },
            axisTick: {
                show: false,
                length   : 12,
                lineStyle: {
                    color      : 'auto',
                    shadowColor: 'rgb(255, 255, 255)',
                    shadowBlur : 5,
                }
            },
            axisLine: {
                lineStyle: {
                    color: [
                        [
                            0, new echarts.graphic.LinearGradient(
                            0, 0, 1, 0, [
                                { offset: 0, color: '#fd204b' },
                                { offset: 0.5, color: '#ffc541' },
                                { offset: 1, color: '#6afa96' },
                            ])
                        ],
                        [
                            1, '#444'
                        ]
                    ],
                    width      : 20,
                }
            },
            splitLine: {
                show: false,
                length: 20,
                lineStyle: {
                    width      : 2,
                    color      : 'rgb(184, 241, 237)',
                    shadowColor: 'rgb(255, 255, 255)',
                    shadowBlur : 5,
                }
            },
            pointer: {
                show: false,
                length: '75%',
                width : 5,
            },
            itemStyle:{
                color: 'rgb(184, 241, 237)'
            },
            detail: {
                show: false
            },
            data: [{name: '', value: 0}]
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
                    r : 20,
                },
                style: {
                    fill: 'rgba(51, 51, 51, 0)',
                }
            }, {
                type: 'text',
                left: 'center',
                top : 'center',
                z   : 100,
                style: {
                    fill: '#fff',
                    text: 0,
                    font: 'bold 65px Microsoft YaHei',
                    textShadowBlur   :10,
                    textShadowColor  : 'rgb(0, 0, 0, 1)',
                    textShadowOffsetY: 0,
                }
            }
        ]
    }],
}
