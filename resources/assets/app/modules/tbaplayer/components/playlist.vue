<i18n>
{
    "en": {
        "playlist": "Play List",
        "title"   : {
            "length": "Total Length of Frag",
            "sec"   : "sec"
        },
        "track"   : {
            "frag"  : "frag",
            "length": "length",
            "sec"   : "sec"
        }
    },

    "tw": {
        "playlist": "切片清單",
        "title"   : {
            "length": "切片總長度",
            "sec"   : "秒"
        },
        "track"   : {
            "frag"  : "切片",
            "length": "長度",
            "sec"   : "秒"
        }
    },

    "cn": {
        "playlist": "切片清单",
        "title"   : {
            "length": "切片总长度",
            "sec"   : "秒"
        },
        "track"   : {
            "frag"  : "切片",
            "length": "长度",
            "sec"   : "秒"
        }
    }
}
</i18n>

<template>
<article>
    <Card class="playlist">
        <div slot="title">
            <p>{{ $t('playlist') + ': ' + info.name }}</p>
            <p>{{$t('title.length')}}: {{totalLength}} {{$t('title.sec')}}</p>
        </div>
        <div class="playlist-main scroll">
            <Button type="ghost" v-for="(v, i) in list" v-bind:key="i" v-bind:class="{active: curr === i}" v-on:click="setCurrPlay(i)">
                <!-- 暫時註解
                <p v-if="typeof v.tba.frag === 'undefined' || v.tba.frag === null">{{v.tba.name}}</p>
                <p v-else>{{v.tba.frag.name === null ? v.tba.name : v.tba.frag.name}} ({{$t('track.length')}}:{{(v.tba.frag.end - v.tba.frag.start)}}{{$t('track.sec')}})</p>
                <p>{{v.tba.name}}</p>
                <p>{{v.tba.frag.description}}</p>
                -->
                <p v-if="typeof v.tba.frag === 'undefined' || v.tba.frag === null">{{$t('track.frag')}}{{i+1}}: {{v.tba.name}}</p>
                <p v-else>{{$t('track.frag')}}{{i+1}}({{$t('track.length')}}{{v.tba.frag.end - v.tba.frag.start}}{{$t('track.sec')}}): {{v.tba.frag.name}}</p>
                <span v-if="typeof v.tba.frag === 'undefined' || v.tba.frag === null"></span>
                <span v-else>{{v.tba.frag.description}}</span>
                <p>({{v.tba.name}})</p>
            </Button>
        </div>
    </Card>
</article>
</template>

<script>
import _    from 'lodash'
import Vuex from 'vuex'

export default {

    data () {
        return {

        }
    },

    computed: _.merge(
        Vuex.mapState('tbaplayer', {
            info: state => state.info,
            list: state => state.playlist,
            curr: state => state.currPlay,
        }),

        {
            totalLength () {
                let l = 0
                this.list.forEach(function(v) {
                    if (typeof v.tba.frag === 'undefined' || v.tba.frag === null) {
                        return
                    }
                    l += v.tba.frag.end - v.tba.frag.start
                })
                return l
            },
        }
    ),

    methods: _.merge(

        Vuex.mapActions('tbaplayer', [
            'setCurrPlay',
        ]),

        {

        }
    ),

    mounted () {


    },

}
</script>

<style lang="scss">
.tbaplayer {
    .playlist {
        .ivu-card-body {
            padding: 0;
            height : calc(100% - 66.6px);
        }
    }
}
</style>

<style lang="scss" scoped>
.playlist {
    height : 100vh;
    padding: 0;

    .playlist-main {
        height    : 100%;
        overflow-y: auto;

        button {
            width     : 100%;
            margin    : 2px 0;
            text-align: left;
            color     : #fff;

            p {
                white-space  : nowrap;
            	text-overflow: ellipsis;
            	overflow     : hidden;
            }

            span {
                white-space: initial;
            }
        }

        button.active {
            color : #fff380;
        }
    }
}
</style>
