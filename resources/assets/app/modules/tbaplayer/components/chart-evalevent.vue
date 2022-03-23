<i18n>
{
    "en": {
        "linerange": {
            "annotation": {
                "localtime": "drag to position the timeline",
				"unit"     : "points",
                "title"    : "Hot Zone"
            }
        },
        "base": {
            "choose": "please select"
        },
        "commentStatusSelection": {
            "private": "Private",
            "public": "Public"
        },
        "datasetSelect": {
            "current": "Current"
        },
        "modal": {
            "close" : "Close",
            "delete": "Delete",
            "share" : "Modal Share",
            "update": "Update"
        },
        "infoModal": {
            "title": "Information"
        },
        "evalModal": {
            "title": "Add Comment",
            "inputPlaceholder": "Comment...",
            "upload": "Add file (jpg, jpeg, png, mp3, mp4, wav, webm, mov, m4a) [<50MB]: ",
            "error" : "Upload Error!!",
            "submit": "Submit"
        },
        "eventSharing": {
            "title"     : "Message",
            "course"    : "Lesson Name: ",
            "channel"   : "Channel: ",
            "teacher"   : "Instructor: ",
            "time"      : "Time: ",
            "evaluator" : "Evaluator: ",
            "evaluation": "Content: "
        },
        "copyURL": {
            "success": "Content already copied to clipboard.",
            "error"  : "Operation error!"
        },
        "exportEvents": {
            "tooltip": "Export the observation report (Sokrates channels)"
        }
    },

    "tw": {
        "linerange": {
            "annotation": {
                "localtime": "拖曳以定位時間軸",
				"unit"     : "則",
                "title"    : "熱區"
            }
        },
        "base": {
            "choose": "請選擇"
        },
        "commentStatusSelection": {
            "private": "本人",
            "public": "公開"
        },
        "datasetSelect": {
            "current": "當前評估"
        },
        "modal": {
            "close" : "關閉",
            "delete": "刪除",
            "share" : "複製本切點連結",
            "update": "更新"
        },
        "infoModal": {
            "title": "評估資料"
        },
        "evalModal": {
            "title" : "新增標記內容",
            "inputPlaceholder": "標記內容...",
            "upload": "上傳檔案 (jpg, jpeg, png, mp3, mp4, wav, webm, mov, m4a) [<50MB]: ",
            "error" : "上傳格式錯誤",
            "submit": "確定"
        },
        "eventSharing": {
            "title"     : "切點信息",
            "course"    : "課例名稱: ",
            "channel"   : "頻道: ",
            "teacher"   : "授課教師: ",
            "time"      : "切點時間: ",
            "evaluator" : "標記者: ",
            "evaluation": "標記內容: "
        },
        "copyURL"  : {
            "success": "內容已複製到剪貼簿",
            "error"  : "操作發生錯誤"
        },
        "exportEvents" : {
            "tooltip": "匯出議課報表(頻道,智能終端)"
        }
    },

    "cn": {
        "linerange": {
            "annotation": {
                "localtime": "拖曳以定位时间轴",
				"unit"     : "则",
                "title"    : "热区"
            }
        },
        "base": {
            "choose": "请选择"
        },
        "commentStatusSelection": {
            "private": "本人",
            "public": "公开"
        },
        "datasetSelect": {
            "current": "当前评估"
        },
        "modal": {
            "close" : "关闭",
            "delete": "删除",
            "share" : "複製本切点链接",
            "update": "更新"
        },
        "infoModal": {
            "title": "评估资料"
        },
        "evalModal": {
            "title" : "新增标记内容",
            "inputPlaceholder": "标记内容...",
            "upload": "上传档案 (jpg, jpeg, png, mp3, mp4, wav, webm, mov, m4a) [<50MB]: ",
            "error" : "上传格式错误",
            "submit": "確定"
        },
        "eventSharing": {
            "title"     : "切点信息",
            "course"    : "课例名称: ",
            "channel"   : "频道: ",
            "teacher"   : "授课教师: ",
            "time"      : "切点时间: ",
            "evaluator" : "标记者: ",
            "evaluation": "標記内容: "
        },
        "copyURL"  : {
            "success": "内容已复制到剪贴板",
            "error"  : "操作发生错误"
        },
        "exportEvents" : {
            "tooltip": "汇出议课报表(频道,智能终端)"
        }
    }
}
</i18n>
<style>
    .ivu-tooltip-inner {
        white-space: pre-line !important;
    }
</style>
<template>
<div style="position: relative; left: 40px;">

    <!-- Tba Evaluate Event Chart -->
    <section style="width: 100%; height: 100%;">
		<div v-bind:id="id" style="width: 100%; height: 70px"></div>
    </section>

    <Modal class="tbaplayer modal-info" v-model="modal.info" v-on:on-visible-change="closeInfoModel">
        <section slot="header">
            <span>{{ $t('infoModal.title') }}</span>
            <span>{{eventInfo.mode }}</span>
            <span>{{eventInfo.clock.sign+eventInfo.clock.h+':'+eventInfo.clock.m+':'+ eventInfo.clock.s}}</span>
        </section>
        <Input type="textarea" v-model="eventInfo.text" v-bind:rows="4" style="resize: none"></Input>
        <div style="color: white;margin-top: 10px;">
            <label for="evalUpdatedFile">{{ $t('evalModal.upload') }}</label>
            <input type="file" id="evalUpdatedFile" name="evalUpdatedFile" ref="evalUpdatedFile" @change="handleEvalFileUpload('evalUpdatedFile')">
        </div>
        <section slot="footer">
            <Button type="success" v-show="eventMeta.editable" v-on:click="updateEvent">{{$t('modal.update')}}</Button>
            <Button type="error" v-show="eventMeta.editable" v-on:click="deleteEventInfo">{{$t('modal.delete')}}</Button>
            <Button type="primary" v-on:click="modal.info = false; setPaused(false);">{{$t('modal.close')}}</Button>
        </section>
    </Modal>

    <!-- Add Comment Modal -->
    <Modal
        class="tbaplayer"
        v-model="displayEvalModal"
        v-on:on-visible-change="closeEvalModel"
    >
        <!-- Header -->
        <section slot="header">
            <span>{{ $t('evalModal.title') }}</span>
            <Tag color="blue">{{ eventInfoTimeMMSS }}</Tag>
        </section>
        <!-- Comment Tag Type Selection -->
        <section style="padding-bottom: 5px">
            <!-- Public / Private -->
            <RadioGroup
                v-if="commentStatusSelection.options.length > 0"
                v-model="commentStatusSelection.selectedVal"
                style="color: #FFFFFF;"
            >
                <Radio
                    v-for="opt in commentStatusSelection.options"
                    v-bind:value="opt.value"
                    v-bind:key="opt.value"
                    v-bind:label="opt.value"
                >
                    {{ opt.label }}
                </Radio>
            </RadioGroup>
            <div class="selection-container">
                <!-- Comment Types -->
                <select
                    class="select muti-col"
                    v-if="commentTagTypesList.length > 0"
                    v-model="selectedCommentTypeData"
                >
                     <optgroup
                        v-for="(commentTypeData) in commentTagTypesList"
                        :label="commentTypeData.name"
                        :key="commentTypeData.key"
                    >
                        <option
                            v-for="commentType in commentTypeData.types"
                            :key="commentType.id"
                            :value="{'catKey': commentTypeData.key, 'id': commentType.id, 'name': commentType.name}"
                        >
                            {{ commentType.name }}
                        </option>
                    </optgroup>
                </select>
                <!-- Comment Tags -->
                <select
                    class="select muti-col"
                    v-if="curCommentTagList.length > 0"
                    v-model="selectedCommentTagId"
                    :disabled="!selectedCommentTypeData.id"
                >   
                    <option
                        v-for="commentTag in curCommentTagList"
                        v-bind:key="commentTag.id"
                        v-bind:value="commentTag.id"
                    >
                        {{ commentTag.content.name }}
                    </option>
                </select>
            </div>
        </section>
        <!-- Hint -->
        <section>
            <div class="selection-hint-container">
                <span :class="curCommentTagData.styleClass">
                    {{ curCommentTagData.desc }}
                </span>
            </div>
        </section>
        <!-- Text area and File Upload -->
        <section>
            <Input
                type="textarea"
                :placeholder="$t('evalModal.inputPlaceholder')"
                v-model="eventInfo.text"
                v-bind:rows="4"
                style="resize: none; padding-top: 5px">
            </Input>
            <div style="color: white;margin-top: 10px;">
                <label for="evalFile">{{ $t('evalModal.upload') }}</label>
                <input type="file" id="evalFile" name="evalFile" ref="evalFile" @change="handleEvalFileUpload('evalFile')">
            </div>
        </section>
        <section slot="footer">
            <Button
                type="primary"
                @click="createComment"
                :loading="loadingStatus"
                :disabled="!commentDataValid"
            >
                {{ $t('evalModal.submit') }}
            </Button>
        </section>
    </Modal>

    <Modal class="tbaplayer modal-info" v-model="modal.total" width="750">
        <section slot="header">
            <span>{{ $t('infoModal.title') }}</span>
            <span>{{eventInfoTotal.clock.sign+eventInfoTotal.clock.h+':'+eventInfoTotal.clock.m+':'+ eventInfoTotal.clock.s}}</span>
        </section>
        <div style="border:1px solid white;border-radius:4px;background-color:#506385;color:white;padding:4px 7px;min-height:100px;">
            <div v-for="(text, i) in eventInfoTotal.text">
                <span>{{ text }}</span>
                <Icon type="image" style="color:darkturquoise;cursor:pointer;" v-if="eventInfoTotal.image[i] != null" v-on:click="setImage(eventInfoTotal.image[i])"></Icon>
            </div>
        </div>
        <section slot="footer">
            <Button type="primary" v-on:click="modal.total = false; setPaused(false);">{{$t('modal.close')}}</Button>
        </section>
    </Modal>
</div>
</template>

<script>
import _                  from 'lodash'
import Vuex               from 'vuex'
import VueClipboard       from 'vue-clipboard2'
import VueGallery         from 'vue-gallery';
import CpntChartLinerangeEchart from './chart-linerange-echart.vue';
Vue.use(VueClipboard)

export default {

    mixins: [CpntChartLinerangeEchart],

    components: {
        'gallery': VueGallery
    },

    props: ['options', 'showEvalModal'],

    emits: ['close-eval-modal'],

    data () {
        return {
            eventOpts: [],
            identitySelect: {
                options : [],
                selected: null,
            },
            commentStatusSelection: {
                options : [],
                selectedVal: null, // 0: private, 1: public
            },
            selectedCommentTypeData: {
                catKey: null, // school, tm, personal 
                id: null,
                name: null,
            },
            selectedCommentTagId: null,
            datasetSelect: {
                options : [],
                selected: null,
            },
            modal: {
                info: false,
                eval: false,
				total: false,
            },
            eventInfo: {
                event: null,
                mode : null,
                time : null,
                text : null,
                imgs : null,
                clock: {h: 0, m: 0, s: 0},
            },
            eventMeta: {
                editable: false,
            },
			 eventInfoTotal: {
                text : null,
                clock: {h: 0, m: 0, s: 0},
                image: null,
            },
            currEventList: [],
			showEventModal: false, //JEFF 是否顯示打點訊息子視窗,
            eventSec: 0,
            eventSharingUrl: "",
            images: [],
            index: null,
            evalFile: null,
            selectedEventMode: null,
            loadingStatus: false
        }
    },

    computed: _.merge(
        Vuex.mapState("tbaplayer", {
            apiSrv: (state) => state.apiSrv,
            events: (state) => state.evalEvents,
            eventsHandle: (state) => state.evalEventsHandle,
            gallery: (state) => state.gallery,
            evaluateOptions: (state) => state.evaluateOptions,
            commentTagTypesList: (state) => state.commentTagTypesList,
            privilegedUserTypeList: (state) => state.privilegedUserTypeList,
            fileSetting: (state) => state.fileSetting,
        }),
        Vuex.mapGetters("tbaplayer", {
            allowedExtList: "allowedExtList",
        }),
        {
            groupId() {
                let curUrl = window.location.href;
                let objUrl = new URL(curUrl);
                let groupIds = objUrl.searchParams.get("groupIds");
                let groupId = groupIds ? parseInt(groupIds.split(",")[0]) : null;
                return groupId;
            },

            selectedIdentityLabel() {
                let idx = (this.identitySelect.options.length < 1) ? 0 : this.identitySelect.selected;
                if (!this.identitySelect.options[idx])
                    return "";
                return this.identitySelect.options[idx].label;
            },

            curCommentTagList() {
                // Create a tag list based on the selected comment type
                if (
                    !this.commentTagTypesList ||
                    !this.selectedCommentTypeData.catKey ||
                    !this.selectedCommentTypeData.id ||
                    !this.selectedCommentTypeData.name
                ) return [];
                let commentTypeList = _.find(this.commentTagTypesList, {
                    key: this.selectedCommentTypeData.catKey,
                }).types;
                return _.find(commentTypeList, { id: this.selectedCommentTypeData.id }).tags;
            },

            curCommentTagData() {
                if (!this.selectedCommentTagId || this.curCommentTagList.length < 1) return "";
                let curCommentTagData = _.find(this.curCommentTagList, {id: this.selectedCommentTagId});
                return {
                    "name": curCommentTagData.content.name,
                    "desc": curCommentTagData.content.desc.trim(),
                    "styleClass": curCommentTagData.is_positive
                        ? "selection-hint-text-pos"
                        : "selection-hint-text-neg",
                }
            },

            commentData() {
                return {
                    tbaId: this.tba.id,
                    groupId: this.groupId,
                    typeId: this.selectedCommentTypeData.id,
                    typeName: this.selectedCommentTypeData.name,
                    tagId: this.selectedCommentTagId,
                    tagName: this.curCommentTagData.name,
                    tagDesc: this.curCommentTagData.desc,
                    commentType: 2, // 2 by default
                    public: this.commentStatusSelection.selectedVal,
                    timepoint: this.eventInfo.time,
                    text: this.eventInfo.text,
                };
            },

            displayEvalModal: {
                get() {
                    return this.showEvalModal;
                },
                set(value) {
                    return value;
                }
            },

            eventInfoTimeMMSS() {
                let seconds = this.eventInfo.time;
                return ('0'+ Math.floor(seconds / 60) ).slice(-2) + ':' + ('0'+ Math.floor(seconds % 60) ).slice(-2);
            },

            commentDataValid() {
                return _.every(this.commentData, (v) => {
                    return v !== null;
                });
            },

        }
    ),

    methods: _.merge(

        Vuex.mapActions('tbaplayer', [
            'setMsgInfo',
            'getEvalEvents',
            'setEvalEvents',
            'deleteEvalEvent',
            'reloadTbaAnalChart',
            'setBlockR',
            'updateComments',
            'setEvaluateOptions',
            'createTbaComment',
        ]),

        {
            declarePlugins () {
                let me = this

                let plugin = new Chart.plugin.linerange('linerange')
                plugin.afterEvent = function(chart, e, opts) {

    				if( e.type !== 'click' ) {
    					return
    				}

    				let data = this.getActiveInfo(chart)
    				if (data === null) {
    					return
    				}

                    let tTba = Array.isArray(data.data) ? data.data[0] : data.data
                    me.setTrackInfo(me.parseTbaTimeToTrackInfo(tTba))
                    me.getEventInfo(me.events.datasets[data.datasetIndex].ids[data.index][data.idx])
    			}

                return {
                    plugins: [plugin],
                    options: {linerange: {}},
                }
            },

            init () {
                if (this.tba.id === this.preTbaId) {
                    return
                }

                this.setEvalEvents(null)
                //console.log('options',this.options)

                //chad 預設選取專家或觀課身分
                if (this.options.identities.length > 0) {
                    this.identitySelect.selected = 0
                    for (var i = 0; i < this.options.identities.length; i++) {
                        if (this.options.identities[i].type === "Expert" || this.options.identities[i].type === "Visitor") {
                            this.identitySelect.selected = i
                            break
                        }
                    }
                } else {
                    this.identitySelect.selected = null
                }

                this.currEventList = _.map(this.options.identities, (v, i) => {

                    let labels  = []
					let labeltypes = []
                    let user = {id:'', name:''}
                    let dataset = {
                        ids    : [],
                        colors : [],
                        details: [],
						eventtexts: [],
                        labelsmode: [],
                        eventimgs: []
                    }

                    return [{
						user: user,
                        labels  : labels,
						labeltypes: labeltypes,
                        range   : {min: this.eventRange.min, max: this.eventRange.max},
                        datasets: [dataset],
                    }];
                })

                this.datasetSelect.selected = null

                this.apiSrv.getInstance().getEvalEventOpts(this.tba.id).then((data) => {
                	if(! data.status) {
        				return
        			}
                    this.eventOpts = data.data
                    //console.log('eventOpts',data.data)
                    this.datasetSelect.options = _.map(data.data, (v, i) => {
                        return { value: i, label: '['+v.note+'] ' + v.text }
                    })
                    this.datasetSelect.options.unshift({
                        value: null,
                        label: (this.options.identities.length === 0 ? this.$i18n.t('base.choose') : this.$i18n.t('datasetSelect.current')) + '...',
                    })

					//JEFF 「總合」在init後優先選擇為default
                    for(let eventOptsIndex in this.eventOpts) {
                        if(typeof this.eventOpts[eventOptsIndex]['type'] !== 'undefined' && this.eventOpts[eventOptsIndex]['type'] == 'total') {
                            this.datasetSelect.selected = eventOptsIndex;
                        }
                    }
                    this.switchDataset();
                    //console.log('eventdatainit', this.tba.id, data)
        		})
            },

            getEventInfo (eventId = null, tbaTime = 0) {
                this.initEventInfo()

                if(eventId === null) {
                    return
                }

                this.setPaused(true)

                this.apiSrv.getInstance().getEvalEvent(this.tba.id, eventId).then((data) => {
                    if(! data.status) {
                        this.setPaused(false)
                        return
                    }
                    _.merge(this.eventMeta, data.meta)
                    data.data.clock = this.toHHMMSS(data.data.time)
                    this.eventSec   = data.data.time
                    this.eventInfo  = data.data
                    this.setBlockR('commentlist')
                    if (this.eventMeta.editable) this.modal.info = true
                    //console.log('getEvent',eventId, data)
                })
            },

            deleteEventInfo () {
                this.deleteEvalEvent(this.eventInfo.id)
                this.modal.info = false
            },

            closeInfoModel (visible) {
                if (visible) {
                    return
                }
                this.initEventInfo()
            },

            closeEvalModel (visible) {
                if (visible) {
                    return
                }
                this.initEventInfo()
                this.$emit("close-eval-modal");
            },

            switchIdentity () {
                if (this.datasetSelect.selected !== null || this.identitySelect.selected === null) {
                    this.setDefaultEventMode();
                    return;
                }
                this.setEvalEvents(this.currEventList[this.identitySelect.selected])
                //console.log('Switch Identity currEventList',this.currEventList)
            },

            switchDataset () {
				this.showEventModal = ( this.datasetSelect.selected === null || this.eventOpts[this.datasetSelect.selected]['type'] != 'total') ? true : false; //是否開啟總合訊息子視窗Flag
                //console.log('Switch Dataset currEventList',this.currEventList)
                if (this.datasetSelect.selected === null && this.identitySelect.selected !== null) {
                    this.setEvalEvents(this.currEventList[this.identitySelect.selected])
                    return
                }

                let param = (this.datasetSelect.selected === null) ? null : this.eventOpts[this.datasetSelect.selected]
                this.getEvalEvents(param)
                //console.log('switchDataset',param)
            },

            evaluate (eventMode) {

                this.initEventInfo()
                this.eventInfo.eventMode = eventMode
                this.eventInfo.event     = eventMode.event
                this.eventInfo.mode      = eventMode.mode
                this.eventInfo.time      = this.tbaTime
                this.eventInfo.clock     = this.toHHMMSS(this.tbaTime)

                this.setPaused(true)
                this.modal.eval = true

            },

            initEventInfo () {
                this.eventInfo = {
                    event: null,
                    mode : null,
                    time : null,
                    text : null,
                    imgs : [],
                    clock: {h: 0, m: 0, s: 0},
                };
                this.evalFile = null;
                this.$refs.evalFile.value = null;
                this.$refs.evalUpdatedFile.value = null;
            },

            createEvent () {
                this.loadingStatus = true;

                let eventMode        = this.eventInfo.eventMode
                let currEventListIdx = this.identitySelect.selected
                this.apiSrv.getInstance().createEvalEvent(
                    this.tba.id,
                    eventMode.id,
                    _.pick(this.eventInfo, ['time', 'text']),
                    this.evalFile
                ).then((data) => {
                    let msgInfo = (data.status)
    					? {type: 'success', value: 'act.update.suce'}
    					: {type: 'error',   value: 'act.update.fail'}
                    this.setMsgInfo(msgInfo)

                    this.evalFile = null;
                    this.$refs.evalFile.value = null;

                    if(! data.status) {
                        return
                    }

                    // 更新右側點評清單數據
                    this.updateComments();

                    // 重繪
                    this.switchDataset();
                    // this.setPaused(false)

                    //刷新評課名單的下拉選單
                    this.apiSrv.getInstance().getEvalEventOpts(this.tba.id).then((data) => {
                        if(! data.status) {
                            return
                        }
                        this.eventOpts = data.data
                        this.datasetSelect.options = _.map(data.data, (v, i) => {
                            return { value: i, label: '['+v.note+'] ' + v.text }
                        })
                        this.datasetSelect.options.unshift({
                            value: null,
                            label: (this.options.identities.length === 0 ? this.$i18n.t('base.choose') : this.$i18n.t('datasetSelect.current')) + '...',
                        })
                       //console.log('eventdatarefresh', this.tba.id, eventId, data)
                    })

                    //關閉訪客及私人標記
                    this.evaluateOptions['enableGuestEvents'] = false;
                    this.evaluateOptions['enablePersonEvents'] = false;
                    this.setEvaluateOptions(this.evaluateOptions);
                }).catch((error) => {
                    console.log(error);
                    this.$Loading.error();
                }).finally(() => {
                    this.loadingStatus = false;
                    this.closeEvalModel();
                });
            },

            updateEvent () {
                let eventId = this.eventInfo.id;
                this.apiSrv.getInstance().updateEvalEvent(
                    this.tba.id,
                    eventId,
                    _.pick(this.eventInfo, ['time', 'text']),
                    this.evalFile
                ).then((data) => {
                    let msgInfo = (data.status)
                        ? {type: 'success', value: 'act.update.suce'}
                        : {type: 'error',   value: 'act.update.fail'};
                    this.setMsgInfo(msgInfo);

                    this.evalFile = null;
                    this.$refs.evalUpdatedFile.value = null;

                    if(! data.status) {
                        return
                    }

                    this.updateComments();
                    // // 當前評估
                    let found = false;
                    _.forEach(this.currEventList, (events, eventsIdx) => {
                        let dataset = events[0].datasets[0];
                        _.forEach(dataset.ids, (row, rowIdx) => {
                            let itemIdx = _.indexOf(row, eventId);
                            if (itemIdx === -1) {
                                return true
                            }
                            dataset.eventtexts[rowIdx][itemIdx] = data.data.text;
                            found = true;
                            return false
                        });
                        return !found
                    });
                    this.switchDataset();
                    this.modal.info = false;
                })
            },

            handleDeleteEvent (eventId) {
                //console.log('刪除點評', eventId)
                this.updateComments();
                // 當前評估
                let found = false
				_.forEach(this.currEventList, (events, eventsIdx) => {
                    let dataset = events[0].datasets[0];
					let labels = events[0].labels;
                    let labeltypes = events[0].labeltypes;
                    _.forEach(dataset.ids, (row, rowIdx) => {
                        let itemIdx = _.indexOf(row, eventId)
						if (itemIdx === -1) {
							return true
						}
						dataset.ids    [rowIdx].splice(itemIdx, 1)
						dataset.details[rowIdx].splice(itemIdx, 1)
						dataset.colors [rowIdx].splice(itemIdx, 1)
						dataset.labelsmode[rowIdx].splice(itemIdx, 1)
                        dataset.eventtexts[rowIdx].splice(itemIdx, 1)
						if(Object.keys(dataset.ids[rowIdx]).length == 0) {
                            if(typeof labels[rowIdx] !== 'undefined') {
                                labels.splice(rowIdx, 1)
                            }
                            if(typeof labeltypes[rowIdx] !== 'undefined') {
                                labeltypes.splice(rowIdx, 1)
                            }
                        }
						found = true
						return false
                    })
					// 是否重繪
                    // if (this.datasetSelect.selected === null && this.identitySelect.selected === eventsIdx) {
                    // }
                    return !found
                })
            },

            createComment () {
                let commentData = this.commentData;
                let fileData = this.evalFile;
                let msgInfo = null;

                this.loadingStatus = true;
                this.apiSrv
                    .getInstance()
                    .createTbaComment(commentData, fileData)
                    .then((res) => {
                        msgInfo = (res.status)
                            ? {type: 'success', value: 'act.update.suce'}
                            : {type: 'error',   value: 'act.update.fail'};
                        this.setMsgInfo(msgInfo);

                        // Clear file
                        this.evalFile = null;
                        this.$refs.evalUpdatedFile.value = null;

                        // Reset Private and Guest comment States
                        this.evaluateOptions['enableGuestEvents'] = false;
                        this.evaluateOptions['enablePersonEvents'] = false;
                        this.setEvaluateOptions(this.evaluateOptions);
                        
                        // Update UI
                        this.updateComments();
                        this.switchDataset();

                    }).catch((error) => {
                        console.log(error);
                        this.$Loading.error();
                    }).finally(() => {
                        this.loadingStatus = false;
                        this.closeEvalModel();
                    });
            },

            getEventSharingUrl (startTime) {
                let me          = this;
                let current_url = window.location.href;
                let obj_url     = new URL(current_url);
                axios.post('/get-player-url', {
                    contentId: obj_url.searchParams.get('contentId'),
                    groupIds: obj_url.searchParams.get('groupIds'),
                    channelId: obj_url.searchParams.get('channelId'),
                    start: startTime,
                })
                    .then((data) => {
                        data = data.data;
                        if (data.status) {
                            let url            = data.data.url;
                            let eventTime      = me.toHHMMSS(me.eventSec);
                            me.eventSharingUrl = me.$t("eventSharing.channel") + data.data.channel + "\n" +
                                                 me.$t("eventSharing.course") + me.tba.name + "\n" +
                                                 me.$t("eventSharing.teacher") + me.tba.teacher + "\n" +
                                                 me.$t("eventSharing.time") + eventTime.sign + eventTime.h + ':' + eventTime.m + ':' + eventTime.s + "\n" +
                                                 me.$t("eventSharing.evaluator") + me.eventOpts[me.datasetSelect.selected].text + "\n" +
                                                 me.$t("eventSharing.evaluation") + me.eventInfo.text + "\n\n" +
                                                 url;
                        }
                    })
            },

            copyUrlWithStartTime () {
                let me = this;
                me.$copyText(me.eventSharingUrl).then(function (e) {
                    me.setMsgInfo({type:'success', value: me.$t("copyURL.success")});
                }, function (e) {
                    me.setMsgInfo({type:'error', value: me.$t("copyURL.error")});
                    // console.log('e',e);
                })
            },

            exportEvaluations () {
                let me          = this;
                let current_url = window.location.href;
                let obj_url     = new URL(current_url);
                let contentId   = obj_url.searchParams.get('contentId');
                let groupIds    = obj_url.searchParams.get('groupIds');
                let channelId   = obj_url.searchParams.get('channelId');
                let url         = '/cms/tba/export-tba-evaluate-events?contentId=' + contentId + '&groupIds=' + groupIds + '&channelId=' + channelId;
                //console.log('Export',url)
                window.location = url;
            },

            toHHMMSS (sec) {
                let sign = '';
                if (Math.sign(sec) === -1) {
                    sign = '-';
                    sec  = Math.abs(sec);
                }

                sec = parseInt(sec)
                let h = Math.floor(sec / 3600)
                let m = Math.floor((sec - (h * 3600)) / 60)
                let s = sec - (h * 3600) - (m * 60)
                if(h < 10) {h = '0' + h}
                if(m < 10) {m = '0' + m}
                if(s < 10) {s = '0' + s}
                return {h: h, m: m, s: s, sign: sign}
            },

            setImage(url) {
                this.gallery.images[0] = url;
                this.gallery.index = 0;
            },

            // File Attachment upload handler
            handleEvalFileUpload(ref) {
                let file = this.$refs[ref].files[0];
                if (file !== undefined) {
                    let ext = file.name.split('.').pop().toLowerCase();
                    let size = file.size;
                    if (this.allowedExtList.includes(ext) && size <= this.fileSetting.sizeLimit) {
                        this.evalFile = file;
                    } else {
                        this.$refs.evalFile.value = null;
                        this.setMsgInfo({type:'error', value: this.$t("evalModal.error")});
                    }
                }
            },

            setDefaultEventMode() {
                // This method resets selection for Event Mode
                // Whenever identity is changed or eval modal is opened
                if (this.options.eventModesList.length < 1)
                    return null;
                let identity = this.identitySelect.selected;
                let defaultEventMode = this.options.eventModesList[identity][0]; // select the first mode based on identity (public/private)
                this.selectedEventMode = defaultEventMode;
            },

            setCommentStatusSelection() {
                // Currently only support two options: 'public (1)' and 'private (0)'

                // Create option list
                _.forEach(this.options.identities, (v) => {
                    // Only 2 options: public and private
                    if (this.commentStatusSelection.options.length === 2) return;

                    // Public: ["Expert", "Visitor", "Teacher"]
                    if (_.includes(this.privilegedUserTypeList, v.type)) {
                        this.commentStatusSelection.options.push({
                            value: v.comment_public,
                            label: this.$t("commentStatusSelection.public"),
                        });
                    }

                    // Private: User
                    else if (v.type === "User") {
                        this.commentStatusSelection.options.push({
                            value: v.comment_public,
                            label: this.$t("commentStatusSelection.private"),
                        });
                    }
                });

                // Set default selected value
                // If there is value == 1, then set it to 1 (public)
                // otherwise, set it to 0 (private)
                this.commentStatusSelection.selectedVal = _.find(
                    this.commentStatusSelection.options,
                        ["value", 1]
                    )
                    ? 1 : 0;
            },

            setDefaultCommentTypeData() {
                // Set default selected comment type (first element)
                if (
                    this.selectedCommentTypeData.catKey !== null ||
                    this.selectedCommentTypeData.id !== null ||
                    this.selectedCommentTypeData.name !== null
                ) return;

                let commentTypeData = _.find(
                    this.commentTagTypesList,
                    (commentType) => {
                        return commentType.types.length > 0;
                    }
                );
                this.selectedCommentTypeData.catKey = commentTypeData.key;
                this.selectedCommentTypeData.id = _.head(commentTypeData.types).id;
                this.selectedCommentTypeData.name = _.head(commentTypeData.types).name;
            },

        }

    ),

    watch: {

        eventsHandle (v) {
            if (v === null) {
                return
            }

            switch (v.act) {
                case 'delete-event':
                    this.handleDeleteEvent(v.meta.id)
                    break;
            }
        },

        displayEvalModal(v) {
            if (!v) return;
            this.setDefaultEventMode();
            this.evaluate(this.selectedEventMode);
            this.setDefaultCommentTypeData();
        },

        selectedEventMode(eventMode) {
            this.evaluate(eventMode);
        },

        curCommentTagList(v) {
            // When curCommentTagList is changed, set up its default value
            if (v.length < 1) return;
            this.selectedCommentTagId = _.head(v).id;
        },

        loadingStatus(v) {
            (v === true) ? this.$Loading.start() : this.$Loading.finish();
        }
    },

    mounted () {
        this.setEvalEvents(null);

        this.identitySelect.options = _.map(this.options.identities, (v, i) => {
            return {
                value: i,
                label: v.text,
            }
        });

        this.identitySelect.selected = (this.options.identities.length === 0) ? null : 0;

        // Set up comment status selection
        this.setCommentStatusSelection();

        Vue.prototype.$evalevent = this;
    }

}
</script>

<style lang="scss" scoped>
.tools {
    position: absolute;
    right   : 0;
    margin  : 0;

    .ivu-btn {
        padding-top   : 5px;
        padding-bottom: 5px;
    }
}

.selection-container {
    float: right;

    .select {
        height       : 30px;
        line-height  : 30px;
        font-size    : 12px;
        padding-left : 8px;
        padding-right: 24px;
        border-radius: 5px;
    }

    .muti-col {
        width: auto;
        max-width: 170px;
        margin-left: 5px;
    }
}

.selection-hint-container {
    padding: 5px;
    display: inline-block;
    text-align: right;
    width: 100%;
    color: #dedede;

    .selection-hint-text-pos {
        color: #a6ea00;
    }

    .selection-hint-text-neg {
        color: #e72d41;
    }
}

.modal-info {
    .imgs {
        margin-top: 8px;
        max-height: 256px;
        overflow-y: auto;
        img {
            width: 100%;
        }
    }
}
</style>
