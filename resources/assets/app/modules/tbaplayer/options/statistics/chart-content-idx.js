import _ from 'lodash'

export default function () {
    return _.cloneDeep(config)
}

const lineStyle = {
    normal: {
        width  : 1,
        opacity: 0.8,
    }
}

const config = {
    radar: {
        shape: 'polygon',
        radius: '65%, 10%',
        splitNumber: 0,
        indicator  : [],
        name: {
            textStyle: {
                color: 'rgb(255, 255, 255, 0.5)',
            }
        },
        axisLine: {
            lineStyle: {
                color: 'rgba(255, 255, 255, 0.2)'
            }
        },
        splitLine: {
            lineStyle: {
                color: [
                    'rgba(255, 255, 255, 0.2)',
                    'rgba(255, 255, 255, 0.2)',
                    'rgba(255, 255, 255, 0.2)',
                    'rgba(255, 255, 255, 0.2)',
                    'rgba(255, 255, 255, 0.2)',
                    'rgba(255, 255, 255, 0.65)',
                ]
            }
        },
        splitArea: {
            show: false
        },
    },
    series: [{
        name: '',
        type: 'radar',
        symbol: 'none',
        lineStyle: lineStyle,
        itemStyle: {
            normal: {
                color: new echarts.graphic.RadialGradient(0.5, 0.5, 0.6, [{
                        offset: 0,
                        color: '#1bc8f1'
                    }, // 0% 处的颜色
                    {
                        offset: 1,
                        color: '#6ddef8'
                    } // 100% 处的颜色
                ], false),
                shadowBlur   : 10,
                shadowColor  : 'rgba(0, 0, 0, 0.5)',
                shadowOffsetY: 0
            }
        },
        areaStyle: {
            normal: {
                opacity: 0.86
            }
        },
        data: [],
    }],
    graphic: [{
        type: 'group',
        bounding: 'raw',
        right   : '50%',
        bottom  : '50%',
        z       : 100,
        children: [
            {
                type: 'polygon',
                //left: 'center',
                //top : 'center',
                z   : 100,
                shape: {
                    points: [ // 五角形座標
                        [ 48,  -18],
                        [ 30,  42],
                        [-30,  42],
                        [-48,  -18],
                        [  0, -54]
                    ]
                },
                style: {
                    fill: new echarts.graphic.RadialGradient(0.5, 0.5, 0.5, [{
                        offset: 0,
                        color: 'rgba(0, 0, 0, 0.2)'
                    }, {
                        offset: 1,
                        color: 'rgba(0, 0, 0, 0)'
                    }], false),
                    shadowBlur: 20,
                }
            }, {
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
