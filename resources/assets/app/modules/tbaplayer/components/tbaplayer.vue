<i18n>
{
    "en": {
        "sokradeo": {
            "player": "Sokrates Video"
        },
        "modeTypes": {
            "general": "general",
            "edit"      : "edit",
            "comments"  : "Comments >",
            "charts"    : "AI >"
        },
        "infoModal": {
            "title": "Information"
        },
        "sharingModal": {
            "title": "切点信息",
            "course": "课例名称: ",
            "teacher": "授课教师: ",
            "time": "切点时间: ",
            "copyButton": "複製内容"
        },
        "copyURL"  : {
            "success": "The URL is copied to clipboard",
            "error"  : "Operation failed."
        }
    },

    "tw": {
        "sokradeo": {
            "player": "蘇格拉底影片"
        },
        "modeTypes": {
            "general": "一般",
            "edit"   : "編輯",
            "comments"  : "標記 >",
            "charts"    : "AI >"
        },
        "infoModal": {
            "title": "資訊"
        },
        "sharingModal": {
            "title": "切點信息",
            "course": "課例名稱: ",
            "teacher": "授課教師: ",
            "time": "切點時間: ",
            "copyButton": "複製內容"
        },
        "copyURL"  : {
            "success": "網址已複製到剪貼簿",
            "error"  : "操作發生錯誤"
        }
    },

    "cn": {
        "sokradeo": {
            "player": "苏格拉底影片"
        },
        "modeTypes": {
            "general": "一般",
            "edit"   : "编辑",
            "comments"  : "标记 >",
            "charts"    : "AI >"
        },
        "infoModal": {
            "title": "资讯"
        },
        "sharingModal": {
            "title": "切点信息",
            "course": "课例名称: ",
            "teacher": "授课教师: ",
            "time": "切点时间: ",
            "copyButton": "複製内容"
        },
        "copyURL"  : {
            "success": "网址已复制到剪贴簿",
            "error"  : "操作发生错误"
        }
    }
}
</i18n>

<template>
<article v-bind:id="id">
    <!-- <gallery :images="gallery.images" :index="gallery.index" @close="gallery.index = null"></gallery> -->
    <section v-if="!narrowScreen"  class="main">
        <div class="block-l scroll">
            <Card class="video" style="height: 60vh">
                <div slot="title">
                    <div id="tbaTitleWrap" style="display:flex;justify-content:space-around;">
                        <div style="margin-left:0; margin-right:auto;">
                            <img style="width: 150px; margin: -20px 0px -20px -10px;cursor: pointer;" v-bind:src="imgs.logo()" v-on:click="redirectToExhibition"></img>
                            <!--                    <span style="padding-right: 8px; color: dodgerblue;">{{$t('sokradeo.player')}}</span>-->
                            <span id="tbaTitle" style="font-size: large;">{{tbaTitle}}</span>
                        </div>
                        <div style="display:flex;justify-content:space-around;min-width:150px;margin-top:-3px;">
                            <div class="infos" style="margin:0 0 0 auto;">
                                <Button type="info" shape="circle" size="small" icon="information" v-on:click="modal.info=true"></Button>
                                <Button type="success" shape="circle" size="small" icon="link" v-on:click="copyUrl"></Button>
                                <!--<Button type="success" shape="circle" size="small" icon="link" v-on:click="showUrlSharingModal()"></Button>-->
                                <Button v-show="info.playlisted === 1" shape="circle" size="small" icon="stats-bars"       v-on:click="setBlockR('statistics')"></Button>
                                <Button v-show="info.playlisted === 1" shape="circle" size="small" icon="ios-list-outline" v-on:click="setBlockR('playlist')"  ></Button>
                            </div>
                            <!-- Block R Switch -->
                            <div
                                @click="switchBlockRTo(blockRSwitchData.switchTo)"
                                :style="blockRSwitchStyle"
                            >
                                {{ blockRSwitchData.label }}
                            </div>
                        </div>
                    </div>
                    <div class="tools" v-if="options.cpnts.tools">
                        <Dropdown placement="bottom-end" v-on:on-click="setMode">
                        	<Button>{{$t('modeTypes.'+mode)}}<Icon type="arrow-down-b"></Icon></Button>
                        	<DropdownMenu slot="list">
                        		<DropdownItem name="general">{{$t('modeTypes.general')}}</DropdownItem>
                        		<DropdownItem name="edit"   >{{$t('modeTypes.edit')   }}</DropdownItem>
                        	</DropdownMenu>
                        </Dropdown>
                    </div>
                </div>
                <div style="height: 100%; overflow: hidden;">
                    <cpnt-video-player ref="cpntVideoPlayer" v-bind:id="id+'_video_player'" v-bind:options="options.cpnts.videoPlayer" :dimension="videoDimension"></cpnt-video-player>
                </div>
            </Card>
            <!-- Analysis Chart -->
            <section class="block scroll" style="height: 26vh">
                <div style="height: 24.5vh">
                    <cpnt-chart-analevent
                        v-bind:id="id+'_chart_analevent'"
                        @reset-comment-detail="resetCommentDetail"
                    >
                    </cpnt-chart-analevent>
                </div>
            </section>
            <!-- Tba Evaluate Event Chart -->
            <section class="block hasModal chart-evalevent">
                <cpnt-chart-evalevent
                    v-bind:id="id + '_chart_evalevent'"
                    v-bind:options="options.cpnts.chartEvalEvent"
                    v-bind:showEvalModal="showEvalModal"
                    @close-eval-modal="resetShowEvalModal"
                    @reset-comment-detail="resetCommentDetail"
                >
                </cpnt-chart-evalevent>
            </section>
            <!-- Tba Comment Chart -->
            <section class="block chart-tba-comment">
                <cpnt-chart-tba-comment
                    :id="id + '_chart_tba_comment'"
                    @reset-comment-detail="resetCommentDetail"
                >
                </cpnt-chart-tba-comment>
            </section>
        </div>
        <!-- Right Sidebar (commentlist && statistics)  -->
        <div class="block-r">
            <cpnt-statistics v-show="block_r === 'statistics'" ref="cpntStatistics"></cpnt-statistics>
            <cpnt-commentlist
                ref="commentlist"
                v-if="block_r === 'commentlist'"
                :currenttbaid="tba.id"
                @display-eval-modal="setShowEvalModal"
            >
            </cpnt-commentlist>
        </div>
    </section>

     <div v-if="narrowScreen">
        <div class="scroll" style="width:100% display:">
            <div :style="'width:'+(videoDimension.width)+'px;height:'+(videoDimension.height+80)+'px;'"> _ </div>
            <section>
                <cpnt-commentlist
                    ref="commentlist"
                    :currenttbaid="tba.id"
                    :topposition="(videoDimension.height+32)"
                    @display-eval-modal="setShowEvalModal"
                >
                </cpnt-commentlist>
            </section>
            <!-- Tba Statistics (It needs to be called for API to work correctly) -->
            <section v-show="displayAllowed.statistics">
                <cpnt-statistics ref="cpntStatistics"></cpnt-statistics>
            </section>
            <!-- Tba Analysis (It needs to be called for API to work correctly) -->
            <section v-show="false" class="block scroll" style="height: 25vh;">
                <div style="height: 24.5vh">
                    <cpnt-chart-analevent
                        v-bind:id="id+'_chart_analevent'"
                    >
                    </cpnt-chart-analevent>
                </div>
            </section>
            <!-- Tba Evaluate Event Chart (It needs to be called for API to work correctly) -->
            <section v-show="false" class="block hasModal chart-evalevent">
                <cpnt-chart-evalevent
                    v-bind:id="id + '_chart_evalevent'"
                    v-bind:options="options.cpnts.chartEvalEvent"
                    v-bind:showEvalModal="showEvalModal"
                    @close-eval-modal="resetShowEvalModal"
                    @reset-comment-detail="resetCommentDetail"
                >
                </cpnt-chart-evalevent>
            </section>
            <!-- Tba Comment Chart (It needs to be called for API to work correctly) -->
            <section v-show="false" class="block chart-tba-comment">
                <cpnt-chart-tba-comment
                    :id="id + '_chart_tba_comment'"
                    @reset-comment-detail="resetCommentDetail"
                >
                </cpnt-chart-tba-comment>
            </section>
            <div class="video" style="z-index:3; position:fixed; top:0px; left:0px;" :style="'height:'+(videoDimension.height+40)+'px;'">
                <div slot="title"  style="height:30px;">
                    <img style="width: 135px; margin: -20px 0px -20px -10px;cursor: pointer;" v-bind:src="imgs.logo()" v-on:click="redirectToExhibition"></img>
                    <span style="cursor:pointer; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 260px;" v-on:click="modal.info=true">{{tba.name}}</span>
                    <div class="tools" v-if="options.cpnts.tools">
                        <Dropdown placement="bottom-end" v-on:on-click="setMode">
                        	<Button>{{$t('modeTypes.'+mode)}}<Icon type="arrow-down-b"></Icon></Button>
                        	<DropdownMenu slot="list">
                        		<DropdownItem name="general">{{$t('modeTypes.general')}}</DropdownItem>
                        		<DropdownItem name="edit"   >{{$t('modeTypes.edit')   }}</DropdownItem>
                        	</DropdownMenu>
                        </Dropdown>
                    </div>
                </div>
                <div style="overflow: hidden;">
                    <cpnt-video-player ref="cpntVideoPlayer" v-bind:id="id+'_video_player'" v-bind:options="options.cpnts.videoPlayer" :dimension="videoDimension"></cpnt-video-player>
                </div>
                <div style="position:absolute;right:8px; top:24px; font-size: 20px; font-weight:bold; color:#00ff00">
                    {{formatSecond(tbaTime)}}
                </div>
            </div>
        </div>
    </div>

    <cpnt-messenger></cpnt-messenger>
    <Modal class="tbaplayer" v-model="modal.info" v-bind:title="$t('infoModal.title')">
        <section>
            <Input type="textarea" v-model="tba.description" v-bind:rows="4" style="resize: none" readonly></Input>
        </section>
    </Modal>
    <Modal class="tbaplayer" v-model="modal.sharing" v-bind:title="$t('sharingModal.title')">
        <section>
            <Input type="textarea" v-model="sharingUrl" v-bind:rows="8" element-id="sharingUrl" style="resize: none" readonly></Input>
        </section>
        <div slot="footer">
            <Button type="info" @click="copyUrlWithStartTime()">{{ $t('sharingModal.copyButton') }}</Button>
        </div>
    </Modal>
</article>
</template>

<script>
import SimpleBar            from 'simplebar'
import _                    from 'lodash'
import Vuex                 from 'vuex'
import VueGallery           from 'vue-gallery';
import CpntVideoPlayer      from './video-player.vue'
import CpntChartAnalEvent   from './chart-analevent.vue'
import CpntChartEvalEvent   from './chart-evalevent.vue'
import CpntChartTbaComment  from './chart-tba-comment.vue'
import CpntStatistics       from './statistics.vue'
import CpntPlaylist         from './playlist.vue'
import CpntMessenger        from './messenger.vue'
import CpntCommentlist      from './commentlist.vue'
import CpntNoData           from './no-data.vue'

function encode64(input) {
    try {
        return escape(window.btoa(unescape(encodeURIComponent(input))));
    } catch (e) {
        return ''
    }
}

export default {

    components: {
        'gallery'               : VueGallery,
    	'cpnt-video-player'     : CpntVideoPlayer,
        'cpnt-chart-analevent'  : CpntChartAnalEvent,
        'cpnt-chart-evalevent'  : CpntChartEvalEvent,
        'cpnt-chart-tba-comment': CpntChartTbaComment,
        'cpnt-statistics'       : CpntStatistics,
        'cpnt-playlist'         : CpntPlaylist,
        'cpnt-messenger'        : CpntMessenger,
        'cpnt-commentlist'      : CpntCommentlist,
        'cpnt-no-data'          : CpntNoData,
    },

    props: ['id', 'options'],

    data () {
        return {
            imgs: {
                logo: require('../resources/images/logo')[this.$i18n.locale],
            },
            block: {
                r: {
                    selected: 'statistics', // statistics playlist
                },
            },
            modal: {
                info: false,
                sharing: false
            },
            sharingUrl: "",
            iscommentsready: false,
            videoDimension: {width:640, height:480},
            tbaTitle: '',
            showEvalModal: false,
        }
    },

    computed: _.merge(
        Vuex.mapState("tbaplayer", {
            narrowScreen: (state) => state.narrowScreen,
            mode: (state) => state.mode,
            info: (state) => state.info,
            playlist: (state) => state.playlist,
            commentlist: (state) => state.commentlist,
            currPlay: (state) => state.currPlay,
            tba: (state) => state.tba,
            tbaTime: (state) => state.tbaTime,
            gallery: (state) => state.gallery,
            apiSrv: (state) => state.apiSrv,
            block_r: (state) => state.block_r,
            commentTagTypesList: (state) => state.commentTagTypesList,
            comments: (state) => state.comments,
            participants: (state) => state.participants,
            displayAllowed: (state) => state.displayAllowed,
        }),
        {
            blockRSwitchData() {
                // Data for switching block R
                return {
                    selected: this.block_r,
                    label:
                        this.block_r === "commentlist"
                            ? this.$t("modeTypes.charts")
                            : this.$t("modeTypes.comments"),
                    switchTo:
                        this.block_r === "commentlist" ? "statistics" : "commentlist",
                    allowed:
                        this.displayAllowed.commentlist && this.displayAllowed.statistics,
                };
            },
            blockRSwitchStyle() {
                return {
                    margin: "5px 0 0 auto",
                    cursor: this.blockRSwitchData.allowed
                        ? "pointer"
                        : "not-allowed",
                };
            }
        }
    ),

    watch: {
        currPlay (v) {
            if (v === null || v >= this.playlist.length) {
                return
            }
            this.$refs.cpntVideoPlayer.reset()
            this.setMode('general')
            this.setTbaVideo(this.playlist[v])
            this.flushPlayer()
        },
    },


    created() {
        if(window.innerWidth < window.innerHeight) {
            this.setNarrowScreen(true);
            this.videoDimension.width = window.innerWidth;
            this.videoDimension.height = this.videoDimension.width/8*5+20;
        } else {
            this.setNarrowScreen(false);
            this.videoDimension.width = 640;
            this.videoDimension.height = 480;
        }
        //console.log('Portrait', this.narrowScreen,'Options',this.options)
    },

    methods: _.merge(

        Vuex.mapActions("tbaplayer", [
            "setMode",
            "setTbaVideo",
            "flushPlayer",
            "setMsgInfo",
            "setPaused",
            "setBlockR",
            "updateComments",
            "getTbaCommentTagTypes",
            "setNarrowScreen",
        ]),

        {
            switchBlockRTo(blockName) {
                if (this.blockRSwitchData.allowed) {
                    this.setBlockR(blockName)
                }
            },
            formatSecond(secs) {
                var hr  = Math.floor(secs / 3600);
                var min = Math.floor((secs - (hr * 3600)) / 60);
                var sec = Math.floor( secs - (hr * 3600) - (min * 60));
                return(((hr==0)? '' : (hr<10 ? '0' : '')+hr+':')+(min<10 ? '0' : '')+min+':'+(sec<10 ? '0' : '')+sec);
            },
            formatSeconds (secNum) {
                secNum      = parseInt(secNum, 10);
                let hours   = Math.floor(secNum / 3600);
                let minutes = Math.floor((secNum - (hours * 3600)) / 60);
                let seconds = secNum - (hours * 3600) - (minutes * 60);

                if (hours   < 10) {hours   = "0" + hours;}
                if (minutes < 10) {minutes = "0" + minutes;}
                if (seconds < 10) {seconds = "0" + seconds;}
                return hours + ':' + minutes + ':' + seconds;
            },
            showUrlSharingModal () {
                this.setPaused(true);
                let me          = this;
                let current_url = window.location.href;
                let obj_url     = new URL(current_url);
                axios.post('/get-player-url', {
                    contentId: obj_url.searchParams.get('contentId'),
                    groupIds: obj_url.searchParams.get('groupIds'),
                    channelId: obj_url.searchParams.get('channelId'),
                    start: this.tbaTime,
                })
                    .then((data) => {
                        data = data.data;
                        if (data.status) {
                            me.modal.sharing = true;
                            let url          = data.data.url;
                            me.sharingUrl    = me.$t("sharingModal.course") + me.tba.name + "\n" +
                                               me.$t("sharingModal.teacher") + me.tba.teacher + "\n" +
                                               me.$t("sharingModal.time") + me.formatSeconds(me.tbaTime) + "\n\n" +
                                               url;
                        } else {
                            me.setMsgInfo({type:'error', value: me.$t("copyURL.error")});
                        }
                    })
                    .catch((e) => {
                        me.setMsgInfo({type:'error', value: me.$t("copyURL.error")});
                    });

            },
            copyUrl () {
                let me = this;
                let current_url = window.location.href;
                let obj_url = new URL(current_url);
                let contentId = obj_url.searchParams.get('contentId');
                let groupIds = obj_url.searchParams.get('groupIds');
                let channelId = obj_url.searchParams.get('channelId');
                let url = document.location.origin+'/auth/login?to='+encode64('/Player?'+'contentId='+contentId+'&groupIds='+groupIds+'&channelId='+channelId);
                me.$copyText(url).then(function (e) {
                    me.setMsgInfo({type:'success', value: me.$t('copyURL.success')});
                }, function (e) {
                    me.setMsgInfo({type:'error', value: me.$t('copyURL.error')});
                    // console.log('e',e);
                })
            },
            copyUrlWithStartTime () {
                let el = document.querySelector('#sharingUrl');
                el.select();

                try {
                    document.execCommand('copy');
                    this.setMsgInfo({type:'success', value: this.$t("copyURL.success")});
                } catch (err) {
                    this.setMsgInfo({type:'error', value: this.$t("copyURL.error")});
                }

                window.getSelection().removeAllRanges();
            },
            redirectToExhibition () {
                let current_url = window.location.href;
                let obj_url = new URL(current_url);
                let contentId = obj_url.searchParams.get('contentId');
                let groupIds = obj_url.searchParams.get('groupIds');
                let channelId = obj_url.searchParams.get('channelId');
                let url = null;
                if (groupIds === null) {
                    url = 'https://'+document.location.hostname+'/exhibition/tbavideo/content/'+contentId;
                } else {
                    url = 'https://'+document.location.hostname+'/exhibition/tbavideo#/content/'+contentId+'?groupIds='+groupIds+'&channelId='+channelId;
                }

                window.location = url;
            },
            adjustTitleMaxWidth() {
                let logoWidth = 150;
                let toolWidth = 150;

                let elmHeader = document.querySelector('#tbaTitleWrap');
                let elmTbaTitle = document.querySelector('#tbaTitle');

                let headerWidth = elmHeader ? elmHeader.offsetWidth : 0;
                let titleMaxWidth = headerWidth - logoWidth - toolWidth - 30;
                let elmTbaTitleWidth = elmTbaTitle ? elmTbaTitle.offsetWidth : 0;

                let tbaInnerTitle = elmTbaTitle ? elmTbaTitle.innerText : "";
                let tbaTextContent = elmTbaTitle ? elmTbaTitle.textContent : "";
                let tbaTitle = tbaInnerTitle || tbaTextContent;
                tbaTitle = (tbaTitle.length === 0) ? this.tbaTitle : tbaTitle;

                if (elmTbaTitleWidth > 0) {
                    if (elmTbaTitleWidth > titleMaxWidth) {
                        let title = tbaTitle.substring(0, tbaTitle.length-1);
                        this.tbaTitle = title;
                    } else {
                        let title = this.tbaTitle;
                        if (title.slice(-3) !== '...' && title !== this.tba.name) {
                            console.log(this.tbaTitle);
                            this.tbaTitle = title.substring(0, title.length-3) + '...';
                        }
                    }
                }
            },
            preloadcomments () {
                this.updateComments();
            },
            preloadCommentTagTypes () {
                let curUrl = window.location.href;
                let objUrl = new URL(curUrl);
                let groupIds = objUrl.searchParams.get("groupIds");
                let groupId = groupIds ? parseInt(groupIds.split(",")[0]) : null;
                this.getTbaCommentTagTypes(groupId);
            },
            setShowEvalModal() {
                this.showEvalModal = true;
            },
            resetShowEvalModal() {
                this.showEvalModal = false;
            },
            resetCommentDetail() {
                this.$refs.commentlist.resetComment();
            }
        }
    ),

    mounted () {
        //console.log('playlist', this.playlist)
        _.forEach(document.getElementById(this.id).getElementsByClassName('scroll'), (v) => {
            new SimpleBar(v)
        });

        // this.setBlockR(this.info.playlisted === 1 ? 'playlist' : 'statistics')
        this.setBlockR('commentlist')
        let me = this

        window.setTimeout(function() {
            me.preloadCommentTagTypes();
            me.preloadcomments();
            if(me.comments.length>0) me.iscommentsready = true;
        },100);
    },

    updated () {
        // 補償:cpnt-statistics
        if (this.block_r === 'statistics') {
            this.$refs.cpntStatistics.makeUp()
        }
        if (this.tbaTitle === '') {
            this.tbaTitle = this.tba.name;
        }
        this.adjustTitleMaxWidth();
    },

}
</script>

<style lang="scss">
body {
    background-color: #171a20;
}

.tbaplayer {
    .ivu-card-head {
        padding: 12px 16px;
		border-bottom: none; //JEFF
        background-color: #232731; //JEFF
    }

    .video {
        .ivu-card-body {
            padding: 0;
            height : calc(100% - 42.6px);
        }
        background-color: #000;
        color : #fff;
    }

	.simplebar-content {
        background-color: #171a20;
    }

    .vjs-default-skin.vjs-paused .vjs-big-play-button {
        display: none;
    }
}
</style>

<style lang="scss" scoped>
.main {
    display: flex;
	height : 100vh;
}

.block-l {
    width: 65%;
}

.block-r {
    width: 35%;
}

.chart-evalevent, .chart-tba-comment {
    width: 96.5% !important;
}

@media (min-width: 2561px) {
    .chart-evalevent, .chart-tba-comment {
        width: 97.5% !important;
    }
}

@media (min-width: 1921px) and (max-width: 2560px) {
    .chart-evalevent, .chart-tba-comment {
        width: 97% !important;
    }
}

@media (max-width: 1680px) {
    .chart-evalevent, .chart-tba-comment {
        width: 95.5% !important;
    }
}

@media (max-width: 1440px) {
    .chart-evalevent, .chart-tba-comment {
        width: 95% !important;
    }
}

@media (max-width: 1280px) {
    .chart-evalevent, .chart-tba-comment {
        width: 94.5% !important;
    }
}

@media (max-width: 1679px) {
    .block-l {
        width: 65%;
    }
    .block-r {
        width: 35%;
    }
}

@media (max-width: 1439px) {
    .block-l {
        width: 60%;
    }
    .block-r {
        width: 40%;
    }
}

.video {

    .infos,
    .tools {
        display : inline-block;
        /*position: absolute;*/
        /*top     : 0;*/
        margin  : 0;
    }

    .infos {
        /*padding: 9px;*/
    }

    .tools {
        right  : 0;
        padding: 4.25px;
    }
}

.scroll {
    overflow: hidden;
}

.hasModal {
    z-index: 1;
}

.ivu-card:hover {
    border-color: #000;
}
</style>
