<i18n>
{
    "en": {
        "commentlist"   : {
            "title": "Sokrates Comments",
            "teacher": "Teacher",
            "grade": "Grade",
            "subject": "Subject",
            "date": "Date",
            "totalcomments": "Comments",
            "totalparticipants": "Participants",
            "time": "Time",
            "commenter": "Name",
            "tag": "Tag",
            "text": "Comment",
            "attachment": "Pic",
            "copylink": "Copy",
            "creative": "Creative",
            "appropriate": "Appropriate",
            "advice": "Advice",
            "question": "Question",
            "other": "Other",
            "copysuccess": "Content already copied to clipboard.",
            "copyerror"  : "Operation error!",
            "close": "Close",
            "firstrow": "Sokrates Classroom Observation App, classroom observation report",
            "deleteconfirm": "Delete the comment?",
            "showguestcomments": "Show the guest comments",
            "hideguestcomments": "Hide the guest comments",
            "showpersoncomments": "Show the private comments",
            "hidepersoncomments": "Hide the private comments"
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
        "commentEditorModal": {
            "title"  : "Edit Comment",
            "submit" : "Submit",
            "cancel" : "Cancel",
            "upload" : "Add file (jpg, jpeg, png, mp3, mp4, wav, webm, mov, m4a) [<50MB]: ",
            "error"  : "This file format is invalid."
        }
    },

    "tw": {
        "commentlist"   : {
            "title": "蘇格拉底點評標記清單",
            "teacher": "授課者",
            "grade": "年級",
            "subject": "學科",
            "date": "日期",
            "totalcomments": "標記數",
            "totalparticipants": "參與人數",
            "time": "時間",
            "commenter": "標記者",
            "tag": "分類",
            "text": "標記內容",
            "attachment": "附件",
            "copylink": "切點",
            "creative": "新穎",
            "appropriate": "適當",
            "advice": "建議",
            "question": "問題",
            "other"   : "其他",
            "copysuccess": "內容已複製到剪貼簿",
            "copyerror"  : "操作發生錯誤",
            "close": "關閉",
            "firstrow": "蘇格拉底議課App，智慧眾籌聽課本",
            "deleteconfirm": "確定要刪除此標記內容?",
            "showguestcomments": "顯示訪客標記",
            "hideguestcomments": "隱藏訪客標記",
            "showpersoncomments": "顯示本人標記",
            "hidepersoncomments": "隱藏本人標記"
        },
        "eventSharing": {
            "title"     : "切點資訊",
            "course"    : "課例名稱: ",
            "channel"   : "頻道: ",
            "teacher"   : "授課教師: ",
            "time"      : "切點時間: ",
            "evaluator" : "標記者: ",
            "evaluation": "標記內容: "
        },
        "commentEditorModal": {
            "title"  : "編輯標記內容",
            "submit" : "確定",
            "cancel" : "取消",
            "upload" : "上傳檔案 (jpg, jpeg, png, mp3, mp4, wav, webm, mov, m4a) [<50MB]: ",
            "error"  : "上傳格式錯誤"
        }
    },

    "cn": {
        "commentlist"   : {
            "title": "苏格拉底标记标记清单",
            "teacher": "授课者",
            "grade": "年级",
            "subject": "学科",
            "date": "日期",
            "totalcomments": "标记数",
            "totalparticipants": "参与人数",
            "time": "时间",
            "commenter": "标记人",
            "tag": "分类",
            "text": "标记内容",
            "attachment": "附件",
            "copylink": "切点",
            "creative": "新颖",
            "appropriate": "适当",
            "advice": "建议",
            "question": "问题",
            "other" : "其他",
            "copysuccess": "内容已复制到剪贴板",
            "copyerror"  : "操作发生错误",
            "close" : "关闭",
            "firstrow": "苏格拉底议课App，智慧众筹听课本",
            "deleteconfirm": "确定要删除此标记内容?",
            "showguestcomments": "显示访客标记",
            "hideguestcomments": "隐藏访客标记",
            "showpersoncomments": "显示本人标记",
            "hidepersoncomments": "隐藏本人标记"


    },
        "eventSharing": {
            "title"     : "切点信息",
            "course"    : "课例名称: ",
            "channel"   : "频道: ",
            "teacher"   : "授课教师: ",
            "time"      : "切点时间: ",
            "evaluator" : "标记者: ",
            "evaluation": "标记内容: "
        },
        "commentEditorModal": {
            "title"  : "编辑标记内容",
            "submit" : "確定",
            "cancel" : "取消",
            "upload" : "上传档案 (jpg, jpeg, png, mp3, mp4, wav, webm, mov, m4a) [<50MB]: ",
            "error"  : "上传格式错误"
        }
    }
}
</i18n>

<template>
<article>
    <gallery :options="galleryOptions" :images="gallery.images" :index="gallery.index" @close="gallery.index = null"></gallery>

    <!-- Comment Detail (Narrowed) -->
    <div
      v-if="narrowScreen && isCurCommentSelected"
      class="comment-detail-mobile-wrapper"
      :style="'top:' + topposition + 'px;'"
    >
      <!-- Comment detail -->
      <div class="comment-detail-mobile-text">
        <span v-if="currentcommentsurl !== null">
          <a :href="currentcommentsurl[0]" target="_blank">
            {{ currentcomments }}
          </a>
        </span>
        <span v-else>
          {{ currentcomments }}
        </span>
      </div>
      <!-- Attachment -->
      <div
        v-if="currentAttachment && currentAttachmentExt"
        class="comment-detail-mobile-attachment"
      >
        <img
          v-if="currentAttachmentExt === 'image'"
          :src="currentAttachment"
          title=""
          class="comment-detail-mobile-attachment-img"
          @click="setImage(currentAttachment)"
        />
        <a
          v-else-if="currentAttachmentExt === 'media'"
          class="comment-detail-mobile-attachment-media"
          @click="openAttachedMedia(currentcomments, currentAttachment)"
        >
          <Icon :type="getCommentMediaIcon(currentCommentData)"></Icon>
        </a>
      </div>
    </div>

    <div
        style="position:relative; color:#fff"
        :style="narrowScreen ? 'text-align:right; padding-right:16px' : 'text-align:center'"
    >
        <Button
            icon="arrow-down-a"
            class="ivu-btn ivu-btn-circle ivu-btn-small ivu-btn-icon-only btn-default"
            style="position:absolute; left:10px; top:12px;"
            @click="exportTbaComments"
        />
        <!-- Private and Guest Buttons -->
        <template v-if="evaluateOptions.hasPersonEventAuth">
            <div style="position:absolute; left:45px; top:12px">
                <Button
                    icon="ios-person"
                    class="ivu-btn ivu-btn-circle ivu-btn-small ivu-btn-icon-only btn-default"
                    :class="{ 'btn-active' : evaluateOptions.enablePersonEvents}"
                    @click="switchPersonEventMode"
                />
            </div>
        </template>
        <template v-if="evaluateOptions.hasGuestEventAuth">
            <div style="position:absolute; left:80px; top:12px">
                <Button
                    icon="ios-people"
                    class="ivu-btn ivu-btn-circle ivu-btn-small ivu-btn-icon-only btn-default"
                    :class="{ 'btn-active' : evaluateOptions.enableGuestEvents}"
                    @click="switchGuestEventMode"
                />
            </div>
        </template>

        <div :style="narrowScreen ? 'font-size: 12px; padding: 0 24px 5px 0;' : ''">
            <span>{{ $t('commentlist.title') }}</span>
            <br>
            <span>
                {{ $t('commentlist.teacher') }}: {{ tba.teacher }}
                {{ $t('commentlist.totalcomments') }}: {{ commentNumbers }}
                {{ $t('commentlist.totalparticipants') }}: {{ commentUserNumbers }}
            </span>
        </div>
        <div v-if="!narrowScreen" style="position:absolute; right:20px; top:4px; font-size: 24px; font-weight:bold; color:#00ff00">
            {{formatSecond(tbaTime)}}
        </div>
        <!-- Eval Input (+) -->
        <template v-if="displayEvalInputBtn">
            <div :style="evalInputBtnStyle">
                <Button
                    class="ivu-btn ivu-btn-circle ivu-btn-small ivu-btn-icon-only"
                    style="color: #ffffff; background-color: #19be6b; border-color: grey"
                    @click="toggleEvalModal"
                >
                    <Icon type="plus"></Icon>
                </Button>
            </div>
        </template>
    </div>
    <!-- Comment Table -->
    <div style="background-color:#000000;">
        <Table 
            style="background-color:#000000;"
            ref="commentsTable"
            :height="narrowScreen ? null : tableHeight"
            :row-class-name="rowClassName"
            :columns="commentColumns"
            :data="commentData"
            @on-row-click="commentsRowClick"
            @on-filter-change="filterChart"
            disabled-hover
            stripe
        >
        </Table>
    </div>

    <!-- Comment Detail (Default Screen) -->
    <div v-if="!narrowScreen && isCurCommentSelected">
        <!-- Close Section -->
        <Row>
            <Button
                icon="arrow-down-b"
                size="small"
                long
                @click="resetComment"
            >
            </Button>
        </Row>
        <!-- Comment Detail Content -->
        <Row style="display:flex; height:20vh;">
            <!-- Functions -->
            <div style="padding-top:16px; padding-left:16px; width:65px">
                <!-- Text Size controllers -->
                <Button
                    type="primary"
                    size="small"
                    style="width: 30px; border:0; border-radius:5px; margin:2px; font-size:12px"
                    @click="largeviewfontsize+=4"
                >
                    A+
                </Button>
                <Button
                    type="primary"
                    size="small"
                    style="width: 30px; border:0; border-radius:5px; margin:2px; font-size:12px"
                    @click="(largeviewfontsize>=20) ? largeviewfontsize-=4 : largeviewfontsize"
                >
                    A-
                </Button>
                <!-- Comment Deletion and Edition controllers -->
                <div
                    v-if="isCommentOperationAllowed"
                    style="margin-top: 10px"
                >
                    <Button
                        v-if="isCommentOwner"
                        type="primary"
                        class="btn-controller"
                        style="font-size:14px"
                        @click="setPaused(true); showeditmodal=true;"
                    >
                        <Icon
                            type="edit"
                            class="btn-controller-icon"
                            style="margin-top: 4px"
                        >
                        </Icon>
                    </Button>
                    <Button
                        v-if="isCommentOwner || isAdmin || isVideoOwner"
                        type="primary"
                        class="btn-controller"
                        style="font-size:18px"
                        @click="setPaused(true); deleteComment();"
                    >
                        <Icon
                            type="ios-trash-outline"
                            class="btn-controller-icon"
                            style="margin-top: 2px"
                        >
                        </Icon>
                    </Button>
                </div>
            </div>
            <!-- Comment Text -->
            <div :style="'color:#fff; padding:24px; margin-top: 12px; word-break: break-all; font-size:'+largeviewfontsize+'px; overflow: auto;'">
                <span v-if="currentcommentsurl !== null">
                    <a :href="currentcommentsurl[0]" target="_blank">{{currentcomments}}</a>
                </span>
                <span v-else>
                    {{currentcomments}}
                </span>
            </div>
            <!-- Attachment -->
            <div v-if="currentAttachment && currentAttachmentExt" style="padding:16px">
                <img
                    v-if="currentAttachmentExt === 'image'"
                    :src="currentAttachment"
                    title=""
                    style="width:150px; background-color:#e0e0e0; padding:1px; cursor: pointer"
                    @click="setImage(currentAttachment)"
                />
                <a
                    v-else-if="currentAttachmentExt === 'media'"
                    style="font-size: 4rem; padding: 1rem;"
                    @click="openAttachedMedia(currentcomments, currentAttachment)"
                >
                    <Icon :type="getCommentMediaIcon(currentCommentData)"></Icon>
                </a>
            </div>
        </Row>
    </div>

    <Modal style="background-color:#182328" width="70%" v-model="showimagemodal" @click="showimagemodal=false" footer="none">
        <div style="display:flex;  justify-content:  center;">
            <img :src="modalimage" title="" style= "background-color:#e0e0e0; padding:4px;" @click="showimagemodal=false">
        </div>
        <section slot="footer">
            <Button type="primary" @click="showimagemodal=false">{{$t('commentlist.close')}}</Button>
        </section>
    </Modal>

    <Modal
        class="media-player-modal"
        style="background-color: #182328"
        v-model="showMediaModal"
        :loading="!modalMediaSrc"
        @on-cancel="closeAttachedMedia"
    >
        <section slot="header">
            <p style="color: #ffffff;">{{ modalMediaHeader }}</p>
        </section>
        <cpnt-media-player :mediaSrc="modalMediaSrc"></cpnt-media-player>
        <section slot="footer"><p></p></section>
    </Modal>

    <Modal class="tbaplayer modal-info" v-model="showeditmodal">
        <section slot="header" class="commentlist">
            <span>{{ $t('commentEditorModal.title') }}</span>
            <Tag color="blue">{{ commentEditor.time }}</Tag>
            <Tag :color="getCommentColor(commentEditor.isPositive)">{{ commentEditor.tag.name }}</Tag>
        </section>
        <Input type="textarea" v-model="commentEditor.text" v-bind:rows="4" style="resize: none"></Input>
        <div style="color: white;margin-top: 10px;">
            <label for="commentUpdatedFile">{{ $t('commentEditorModal.upload') }}</label>
            <input type="file" id="commentUpdatedFile" name="commentUpdatedFile" ref="commentUpdatedFile" @change="handleCommentFileUpload('commentUpdatedFile')">
        </div>
        <section slot="footer">
            <Button type="primary" @click="updateComment" :loading="loadingStatus">{{$t('commentEditorModal.submit')}}</Button>
            <Button @click="showeditmodal=false">{{$t('commentEditorModal.cancel')}}</Button>
        </section>
    </Modal>

</article>
</template>

<script>

function decode64(input) {
        try {
                return decodeURIComponent(escape(window.atob(unescape(input))));
        } catch (e) {
            return ''
        }
}

function encode64(input) {
        try {
            return escape(window.btoa(unescape(encodeURIComponent(input))));
        } catch (e) {
            return ''
        }
}


import Vue              from 'vue'
import _                from 'lodash'
import Vuex             from 'vuex'
import VueGallery       from 'vue-gallery';
import CpntMediaPlayer  from './media-player.vue'
import iTable           from 'iview/src/components/table';
import 'iview/dist/styles/iview.css';
Vue.use(iTable);

export default {

    components: {
        'gallery'             : VueGallery,
        'cpnt-media-player'   : CpntMediaPlayer
    },

    props: ['currenttbaid', 'topposition'],

    emits: ['display-eval-modal'],

    data () {
        let me = this;
        return {
            // Current comment data
            currentcomments     : '',
            currentcommentsindex: -1,
            currentCommentData  : null,
            currentAttachment   : '',
            currentAttachmentExt: '',
            currentcommentsurl  : null,
            currentcommentid    : null,
            currentcommentuserid: null,
            // User States
            userId              : null,
            // Modal
            modalimage          : '',
            modalMediaHeader    : '',
            modalMediaSrc       : '',
            showimagemodal      : false,
            showMediaModal      : false,
            showeditmodal       : false,
            // Misc.
            largeviewfontsize   : 20,
            nosynctimeonce      : false,
            evalUsers           : [],
            evaleventparams     : {},
            commentFilters      : {
                name: [],
                tag: [],
                tagModes: {}
            },
            loadingStatus       : false,
            columnTagFilters    : [
                {
                    label  : this.$t('commentlist.creative'),
                    value  : this.$t('commentlist.creative'),
                    numbers: 0
                },
                {
                    label  : this.$t('commentlist.appropriate'),
                    value  : this.$t('commentlist.appropriate'),
                    numbers: 0
                },
                {
                    label  : this.$t('commentlist.advice'),
                    value  : this.$t('commentlist.advice'),
                    numbers: 0
                },
                {
                    label  : this.$t('commentlist.question'),
                    value  : this.$t('commentlist.question'),
                    numbers: 0
                },
                {
                    label  : this.$t('commentlist.other'),
                    value  : this.$t('commentlist.other'),
                    numbers: 0
                },
            ],
            // Table Cols
            commentColumns            : [
                {
                    title   : this.$t('commentlist.time'),
                    key     : 'time',
                    width   : 64,
                    render  : (h, params) => {
                        return h('div', [
                            h('Button', {
                                props: {
                                    type: 'primary',
                                    size: 'small'
                                },
                                style: {
                                    marginRight: '5px'
                                },
                                on: {
                                    click: () => {
                                        //console.log('clickattime',params.row.time)
                                    }
                                }
                            }, this.formatSecond(params.row.time)),
                        ]);
                    }
                },
                {
                    title   : this.$t('commentlist.commenter'),
                    key     : 'name',
                    width   : 100,
                    ellipsis: true,
                    render  : (h, params) => {
                        return h('div', [
                            h('strong', params.row.name)
                        ]);
                    },
                    filters: [],
                    filterMethod(value, row) {
                        return row.name.indexOf(value) > -1;
                    },
                },
                {
                    title: this.$t("commentlist.tag"),
                    width: 150,
                    ellipsis: true,
                    render: (h, item) => {
                        return h("div", [
                            h(
                                "span",
                                {
                                    style: {
                                        color: this.getCommentColor(item.row.isPositive),
                                        wordBreak: "break-word",
                                        whiteSpace: "break-spaces",
                                    },
                                },
                                this.getCommentLabel(item.row)
                            ),
                        ]);
                    },
                    filters: [],
                    filterMethod(value, row) {
                        return row.tag.name.indexOf(value) > -1;
                    },
                },
                {
                    title   : this.$t('commentlist.text'),
                    key     : 'text',
                    render  : (h, params) => {
                        return h('div', {class: 'ellipsis'}, [params.row.text]
                        );
                    }
                },
                {
                    title   : ' ',
                    key     : 'attachment',
                    width   : 30,
                    render: (h, params) => {
                        if (params.row.attachment.type === 'image') {
                            return h('div', [
                                h('Icon', {
                                    props: {
                                        type: 'image'
                                    }
                                }),
                            ]);}
                        else if (params.row.attachment.type === 'media') {
                            return h('div', [
                                h('Icon', {
                                    props: {
                                        type: this.getCommentMediaIcon(params.row)
                                    }
                                }),
                            ]);}
                        else return h('div','')
                    }
                },
                {
                    title   : ' ',
                    key     : 'url',
                    width   : 40,
                    render  : (h, params) => {
                        if(params.row.url) {
                            return h('div', [
                                h('Icon', {
                                    props: {
                                        type: 'link'
                                    },
                                    style: {
                                        cursor: 'pointer',
                                    },
                                    on: {
                                        click: () => {
                                            let me = this
                                            this.$copyText(params.row.url).then(function (e) {
                                                me.setMsgInfo({type:'success', value: me.$t('commentlist.copysuccess')});
                                            }, function (e) {
                                                me.setMsgInfo({type:'error', value: me.$t('commentlist.copyerror')});
                                                // console.log('e',e);
                                            })
                                            //console.log('copy url:',params.row.url)
                                     }
                                    }
                                }),
                            ]);}
                        else return h('div','')
                    }
                },
            ],

            commentData               : [],

            galleryOptions      : {
                rotation: ['0', '90', '180', '270'],
                rotation_index : 0,
                onslide (index, slide) {
                    let img = slide.querySelector('img');
                    img.addEventListener("click", (e) => {
                        e.stopPropagation();
                        me.galleryOptions.rotation_index++;
                        if (me.galleryOptions.rotation_index >= me.galleryOptions.rotation.length) {
                            me.galleryOptions.rotation_index = 0;
                        }
                        img.style = `transform: rotate(${me.galleryOptions.rotation[me.galleryOptions.rotation_index]}deg);`;
                    });
                    slide.addEventListener("click", () => {
                        me.gallery.index = null;
                    });
                }
            },
            commentEditor       : {
                tag: '',
                type: '',
                isPositive: null,
                time: '',
                text : '',
                file : null,
            }
        }
    },

    computed: _.merge(
        Vuex.mapState("tbaplayer", {
            narrowScreen: (state) => state.narrowScreen,
            apiSrv: (state) => state.apiSrv,
            sectMap: (state) => state.sectMap,
            seekTime: (state) => state.seekTime,
            tbaTime: (state) => state.tbaTime,
            tba: (state) => state.tba,
            commentTagTypesList: (state) => state.commentTagTypesList,
            comments: (state) => state.comments,
            participants: (state) => state.participants,
            gallery: (state) => state.gallery,
            evaluateOptions: (state) => state.evaluateOptions,
            fileSetting: (state) => state.fileSetting,
            userInfo: (state) => state.userInfo,
        }),
        Vuex.mapGetters("tbaplayer", {
            isAdmin: "isAdmin",
            isVideoOwner: "isVideoOwner",
        }),
        {
            curObjUrl() {
                let curUrl = window.location.href;
                let objUrl = new URL(curUrl);
                return objUrl;
            },
            contentId() {
                return this.curObjUrl.searchParams.get("contentId");
            },
            groupIds() {
                return this.curObjUrl.searchParams.get("groupIds");
            },
            channelId() {
                return this.curObjUrl.searchParams.get("channelId");
            },
            isCommentOwner() {
                return (
                    this.currentcommentuserid !== null &&
                    this.userInfo !== null &&
                    this.currentcommentuserid === this.userInfo.id
                );
            },
            isCommentOperationAllowed() {
                return this.isCommentOwner || this.isAdmin || this.isVideoOwner;
            },
            commentNumbers() {
                return this.commentData.length;
            },
            commentUserNumbers() {
                let nameList = _.map(this.commentData, "name");
                return _.uniq(nameList).length;
            },
            isCurCommentSelected() {
                let isDisplay = false;
                if (this.currentcommentid !== null) isDisplay = true;
                return isDisplay;
            },
            tableHeight() {
                // Adjust height of commentlist table
                // Default: 0.95
                // With comment detail displayed: 0.7
                let scale = this.isCurCommentSelected ? 0.7 : 0.95;
                return Math.floor(window.innerHeight * scale);
            },
            evalInputBtnStyle() {
                return this.narrowScreen
                    ? "position:absolute; right:10px; top:12px;"
                    : "position:absolute; right:20px; top:50px; z-index: 1000;";
            },
            displayEvalInputBtn() {
                // Remark: display only when logged-in (not yet implemented) and not watching via exhibition (watch-as-open)
                // Check "watch-as-open" specifically because multiple tabs can be used to log-in but URL remains the same
                // which leads to displaying the button without usable functionalities
                let currentUrl = window.location.href;
                let objUrl = new URL(currentUrl);
                let contentId = objUrl.searchParams.get("contentId");
                let groupIds = objUrl.searchParams.get("groupIds");
                let channelId = objUrl.searchParams.get("channelId");
                return (
                    (contentId !== "null" && groupIds !== "null" && channelId !== "null") &&
                    !currentUrl.includes("watch-as-open")
                );
            },
        }
    ),

    watch: {
        comments (v) {
            //console.log('comments update',v)
            this.updateCommentData();
        },

        seekTime (v) {
            //console.log('commentlist seekTime',v)
        },

        tbaTime (v) {
            //console.log('commentlist tbaTime',v,this.nosynctimeonce)
            if(this.nosynctimeonce) {
                this.nosynctimeonce = false;
                return;
            }
            if(this.comments.length==0) return;
            let i=-1;
            if(v>=this.comments[0].time) {
                for(i=0;i<this.comments.length;i++) {
                    if(v<this.comments[i].time) break;
                }
                i=i-1;
            }
            if(i!=this.currentcommentsindex) {
                this.currentcommentsindex = i;
                let dataTemp = JSON.stringify(this.commentData);
                this.commentData= JSON.parse(dataTemp);
                setTimeout(() => {
                    document.querySelector('.ivu-table-row-highlight').scrollIntoView({
                            behavior: "instant",
                            block: "nearest",
                            inline: "nearest"
                    });
                }, 200);
                //console.log('commentlist tbaTime',v,this.currentcommentsindex)
            }
        },

        loadingStatus(v) {
            (v === true) ? this.$Loading.start() : this.$Loading.finish();
        }
    },

    mounted () {
        let me = this;
        this.updateCommentData();
        if (this.narrowScreen) {
            this.commentColumns.splice(3,1)
            delete this.commentColumns[1].width
        }
/*
                this.apiSrv.getInstance().getEvalEvents(this.currenttbaid,'total',null).then((evaldata) => {
                    if(! evaldata.status) {
                        console.log('allEventfromcommentlisterror', this.currenttbaid)
                        return
                    }
                    this.comments = []
                    for(let i=0;i<evaldata.data.length;i++) {
                        for(let j=0;j<evaldata.data[i].datasets[0].eventtexts.length;j++) {
                            for(let k=0;k<evaldata.data[i].datasets[0].eventtexts[j].length;k++) {
                                let item = { time:'', name:'', tag:'', text:'', img:'', url:'' }
                                item.name = evaldata.data[i].user.name;
                                item.time = evaldata.data[i].datasets[0].details[j][k];
                                item.tag  = evaldata.data[i].datasets[0].labelsmode[j][k];
                                item.text = evaldata.data[i].datasets[0].eventtexts[j][k];
                                item.img  = evaldata.data[i].datasets[0].eventimgs[j][k];
                                this.comments.push(item)
                                console.log(i,j,k,item)
                            }
                        }
                    }
                    this.comments = this.comments.sort(function (a, b) {
                        return a.time > b.time ? 1 : -1;
                    });
                    console.log('allEventfromcommentlist', this.currenttbaid, evaldata, this.comments)
                })
*/
        this.getEvalEventOpts();
    },

    methods: _.merge(

        Vuex.mapActions("tbaplayer", [
            "setTrackInfo",
            "setPaused",
            "setMsgInfo",
            "getEvalEvents",
            "deleteEvalEvent",
            "updateComments",
            "setEvaluateOptions",
            // TbaComment API actions
            "deleteTbaComment",
            "updateTbaComment",
        ]),

        {
            getEvalEventOpts () {
                this.apiSrv.getInstance().getEvalEventOpts(this.tba.id, this.evaluateOptions).then((data) => {
                    if (! data.status) {
                        return
                    }
                    this.evalUsers = data.data;
                    _.forEach(this.evalUsers, (evalUser, i) => {
                        if (evalUser.type === 'self') {
                            this.userId = evalUser.value;
                        }
                        if (evalUser.type === 'total' || evalUser.type === 'tbaComment') {
                            this.evaleventparams = this.evalUsers[i];
                            this.evaleventparams['evaluateOptions'] = this.evaluateOptions;
                        }
                    });
                    // console.log('eventOpts',data.data)
                })
            },

            filterChart (filter) {
                if (filter.key === 'name') {
                    this.commentFilters.name = [];
                    if (filter._filterChecked.length === 0) {
                        this.commentFilters.name = [];
                    } else {
                        _.forEach(filter._filterChecked, (checkedName, i) => {
                            _.forEach(this.evalUsers, (evalUser, j) => {
                                if (checkedName === evalUser.text) {
                                    if (evalUser.type === 'self') {
                                        this.commentFilters.name.push(0);
                                    } else {
                                        this.commentFilters.name.push(evalUser.value);
                                    }
                                }
                            });
                        });
                    }
                }
                if (filter.key === 'tag') {
                    this.commentFilters.tag = [];
                    if (filter._filterChecked.length === 0) {
                        this.commentFilters.tag = [];
                    } else {
                        _.forEach(filter._filterChecked, (checkedTag, i) => {
                            this.commentFilters.tag.push(this.commentFilters.tagModes[checkedTag]);
                        });
                    }
                }
                this.evaleventparams = {
                    type: 'user',
                    value: this.commentFilters.name,
                    mode: this.commentFilters.tag
                };
                this.evaleventparams['evaluateOptions'] = {
                    enableGuestEvents: this.evaluateOptions.enableGuestEvents,
                    enablePersonEvents: this.evaluateOptions.enablePersonEvents,
                };
                this.getEvalEvents(this.evaleventparams);
            },

            rowClassName (row, index) {
                if (this.currentcommentsindex==index) {
                    return 'ivu-table-row-highlight';
                }
                if (index%2==0){
                    return 'ivu-table-stripe-even';
                } else {
                    return 'ivu-table-stripe-odd';
                }

            },

            formatSecond (secs) {
                let hr  = Math.floor(secs / 3600);
                let min = Math.floor((secs - (hr * 3600)) / 60);
                let sec = Math.floor( secs - (hr * 3600) - (min * 60));

                return(((hr==0)? '' : (hr<10 ? '0' : '')+hr+':')+(min<10 ? '0' : '')+min+':'+(sec<10 ? '0' : '')+sec);
            },

            formatMMHH(seconds) {
                return ('0'+ Math.floor(seconds / 60) ).slice(-2) + ':' + ('0'+ Math.floor(seconds % 60) ).slice(-2);
            },

            switchPersonEventMode () {
                if(this.narrowScreen)
                    this.setMsgInfo({type:'success', value: this.showPersonComments ? this.$t('commentlist.hidepersoncomments') : this.$t('commentlist.showpersoncomments')});
                this.$set(this.evaluateOptions, 'enablePersonEvents', !this.evaluateOptions.enablePersonEvents);
                this.setEvaluateOptions(this.evaluateOptions);
                this.updateChartByTotalOrTbaComment();
                this.updateComments({evaluateOptions: this.evaluateOptions, eventType: 'tbaComment'});
                this.getEvalEventOpts();
            },

            switchGuestEventMode () {
                if(this.narrowScreen)
                    this.setMsgInfo({type:'success', value: this.showGuestComments ? this.$t('commentlist.hideguestcomments') : this.$t('commentlist.showguestcomments')});
                this.$set(this.evaluateOptions, 'enableGuestEvents', !this.evaluateOptions.enableGuestEvents);
                this.setEvaluateOptions(this.evaluateOptions);
                this.updateChartByTotalOrTbaComment();
                this.updateComments({evaluateOptions: this.evaluateOptions, eventType: 'tbaComment'});
                this.getEvalEventOpts();
            },

            toggleEvalModal() {
                this.$emit("display-eval-modal");
            },

            updateChartBySelf () {
                _.forEach(this.evalUsers, (evalUser, i) => {
                    if (evalUser.type === 'self') {
                        this.evaleventparams = this.evalUsers[i];
                        this.evaleventparams['evaluateOptions'] = {
                            enableGuestEvents: this.evaluateOptions.enableGuestEvents,
                            enablePersonEvents: this.evaluateOptions.enablePersonEvents
                        };
                        this.getEvalEvents(this.evaleventparams)
                    }
                });
            },

            updateChartByTotalOrTbaComment () {
                _.forEach(this.evalUsers, (evalUser, i) => {
                    if (evalUser.type === 'total' || evalUser.type === 'tbaComment') {
                        this.evaleventparams = this.evalUsers[i];
                        this.evaleventparams['evaluateOptions'] = {
                            enableGuestEvents: this.evaluateOptions.enableGuestEvents,
                            enablePersonEvents: this.evaluateOptions.enablePersonEvents
                        };
                        this.getEvalEvents(this.evaleventparams)
                    }
                });
            },

            updateCommentData () {
                // Assign API data to CommentData
                this.commentData = _.map(this.comments, (item) => {
                    let sharingUrl = {url: this.getSharingUrl(item)};
                    item = _.assign(sharingUrl, item);
                    return item;
                });
                
                // Create filters
                this.createFilterList(1, this.commentData, "name", null, true); // name
                this.createFilterList(2, this.commentData, "tag", "name", true); // tag
            },

            seektimetoevent (startTime) {
                let me = this;
                me.setTrackInfo(me.parseTbaTimeToTrackInfo(startTime))
                //this.setPaused(false)

                // let current_url = window.location.href;
                // let obj_url     = new URL(current_url);
                // console.log(obj_url,
                //     obj_url.searchParams.get('contentId'),
                //     obj_url.searchParams.get('groupIds'),
                //     obj_url.searchParams.get('channelId'))
            },

            parseTbaTimeToTrackInfo (tTba) {
                let valid  = false;
                let track  = 0;
                let tVideo = 0;
                for (let [i, map] of this.sectMap.entries()) {
                    if (tTba < map.range.min || tTba > map.range.max) {
                        continue
                    }
                    track = i;
                    for(let sect of map.sects) {
                        if (tTba < sect.tba_start || tTba > sect.tba_end) {
                            continue
                        }
                        tVideo = tTba + sect.video_offset - sect.tba_start;
                        valid  = true;
                        break
                    }
                    break
                }
                return valid ? {track: track, time: tVideo} : null
            },

            createFilterList(
                colIndex, // based on col index of a table
                commentData,
                key,
                subKey = null,
                includeCount = false
            ) {
                // Create a filter list for table
                // Iterate through a unique array to create iview filter
                this.commentColumns[colIndex].filters = [];
                this.commentColumns[colIndex].filters = _.map(
                    _.uniqBy(commentData, (item) => {
                        if (subKey) return item[key][subKey];
                        return item[key];
                    }),
                    (v) => {
                        let label = subKey ? v[key][subKey] : v[key];
                        let value = subKey ? v[key][subKey] : v[key];
                        // Add (count) to label if needed
                        if (includeCount) {
                            let filterCommentList = _.filter(commentData, (item) => {
                                if (subKey) return item[key][subKey] === value;
                                return item[key] === value;
                            });
                            label = label + " (" + filterCommentList.length + ")";
                        }
                        return { label: label, value: value };
                    }
                );
            },

            //生成URL
            getSharingUrl (comment) {
                let me = this;
                let start = comment.time;
                let current_url = window.location.href;
                let obj_url = new URL(current_url);
                let contentId = obj_url.searchParams.get('contentId');
                let groupIds = obj_url.searchParams.get('groupIds');
                let channelId = obj_url.searchParams.get('channelId');
                let memberChannel = 0;
                let url = document.location.origin+'/auth/login?to='+encode64('/Player?'+'contentId='+contentId+'&groupIds='+groupIds+'&channelId='+channelId+'&start='+start+'&memberChannel='+memberChannel);

                return( me.$t("eventSharing.course") + me.tba.name + "\n" +
                        me.$t("eventSharing.teacher") + me.tba.teacher + "\n" +
                        me.$t("eventSharing.time") + me.formatSecond(start) + "\n" +
                        me.$t("eventSharing.evaluator") + comment.name + "\n" +
                        me.$t("eventSharing.evaluation") + comment.text + "\n\n" +
                        url);
            },

            commentsRowClick (row,index) {
                this.nosynctimeonce = true;
                this.currentcommentsindex = index;
                this.seektimetoevent(this.comments[this.currentcommentsindex].time);
                this.setComment();
            },

            setImage (url) {
                this.gallery.images[0] = url;
                this.gallery.index = 0;
            },

            setComment () {
                this.resetComment();
                if (typeof this.comments[this.currentcommentsindex] !== 'undefined') {
                    // Set up comment detail viewer
                    let time = this.formatSecond(this.comments[this.currentcommentsindex].time);
                    let tag = (this.comments[this.currentcommentsindex].tag.name === '') ? '' : ' [' + this.comments[this.currentcommentsindex].tag.name + '] ';
                    let text = this.comments[this.currentcommentsindex].text;
                    this.currentcomments = time + '  ' + this.comments[this.currentcommentsindex].name + tag + text;
                    this.currentCommentData = this.comments[this.currentcommentsindex];
                    this.currentAttachment = this.comments[this.currentcommentsindex].attachment.src;
                    this.currentAttachmentExt = this.comments[this.currentcommentsindex].attachment.type;
                    this.currentcommentsurl = this.fetchUrl(this.currentcomments);
                    this.currentcommentid = this.comments[this.currentcommentsindex].id;
                    this.currentcommentuserid = this.comments[this.currentcommentsindex].userId;
                    // Set up editor
                    this.commentEditor.type = this.comments[this.currentcommentsindex].type;
                    this.commentEditor.tag = this.comments[this.currentcommentsindex].tag;
                    this.commentEditor.isPositive = this.comments[this.currentcommentsindex].isPositive;
                    this.commentEditor.time = this.formatMMHH(this.comments[this.currentcommentsindex].time); // MMSS format
                    this.commentEditor.text = text;
                }
            },

            resetComment() {
                this.currentcomments = '';
                this.currentCommentData = null;
                this.currentAttachment = '';
                this.currentAttachmentExt = '';
                this.currentcommentsurl = null;
                this.currentcommentid = null;
                this.currentcommentuserid = null;
                this.commentEditor.type = '';
                this.commentEditor.tag = '';
                this.commentEditor.isPositive = null;
                this.commentEditor.time = '';
                this.commentEditor.text = '';
            },

            openAttachedMedia(modalMediaHeader, modalMediaSrc) {
                if (!modalMediaHeader || !modalMediaSrc)
                    return;
                this.modalMediaHeader = modalMediaHeader;
                this.modalMediaSrc = modalMediaSrc;
                this.showMediaModal = true;
                this.setPaused(true); // pause main tba player
            },

            closeAttachedMedia() {
                this.modalMediaHeader = "";
                this.modalMediaSrc = "";
                this.showMediaModal = false;
            },

            fetchUrl (txt){
                let link = txt.match(/(http:\/\/|https:\/\/|www\.)((\w|=|\?|\.|\/|&|#|%|-)+)/g);
                if (link != null) {
                    for (let i=0;i<link.length;i++) {
                        if (link[i].indexOf('http') === -1) {
                            link[i] = 'http://'+link[i]
                        }
                    }
                    return link;
                }
                else{
                    return null;
                }
            },

            getCommentColor(type) {
                switch (type) {
                case 0:
                    return "#e72d41"; // red
                case 1:
                    return "#a6ea00"; // green
                default:
                    return "#dedede"; // gray
                }
            },

            getCommentLabel(commentData) {
                return commentData.type + " - " + commentData.tag.name;
            },

            getCommentMediaIcon(commentData) {
                // This method will return iView icon for media file
                // Only support media type (video, audio)
                let icon = "";
                let attachmentData = commentData.attachment;
                if (!attachmentData) return icon;

                let ext = attachmentData.ext.toLowerCase();
                if (this.fileSetting.extList.audio.includes(ext)) icon = "volume-medium";
                else if (this.fileSetting.extList.video.includes(ext)) icon = "ios-videocam";

                return icon;
            },

            exportEvaluations () {
                let current_url = window.location.href;
                let obj_url     = new URL(current_url);
                let contentId   = obj_url.searchParams.get('contentId');
                let groupIds    = obj_url.searchParams.get('groupIds');
                let channelId   = obj_url.searchParams.get('channelId');
                let url         = '/cms/tba/export-tba-evaluate-events?contentId=' + contentId + '&groupIds=' + groupIds + '&channelId=' + channelId + '&enableGuestEvents=' + this.evaluateOptions.enableGuestEvents + '&enablePersonEvents=' + this.evaluateOptions.enablePersonEvents;
                window.location = url;
            },

            exportTbaComments() {
                let url =
                    "/cms/tba/export-tba-comments?contentId=" +
                        this.contentId +
                    "&groupIds=" +
                        this.groupIds +
                    "&channelId=" +
                        this.channelId +
                    "&enableGuestEvents=" +
                        this.evaluateOptions.enableGuestEvents +
                    "&enablePersonEvents=" +
                        this.evaluateOptions.enablePersonEvents;
                    window.location = url;
            },

            // File Attachment upload handler
            handleCommentFileUpload(ref) {
                let file = this.$refs[ref].files[0];
                let imageExtensions = ['jpg', 'jpeg', 'png'];
                let mediaExtensions = ['mp3', 'mp4', 'wav', 'webm', 'mov', 'm4a'];
                let allowedExtensions = [].concat(imageExtensions).concat(mediaExtensions);
                let allowedFileSize = 52428800; // 50MB
                if (file !== undefined) {
                    let ext = file.name.split('.').pop().toLowerCase();
                    let size = file.size;
                    if (allowedExtensions.includes(ext) && size <= allowedFileSize) {
                        this.commentEditor.file = file;
                    } else {
                        this.$refs[ref].value = null;
                        this.setMsgInfo({type:'error', value: this.$t("commentEditorModal.error")});
                    }
                }
            },

            updateEvent () {
                this.loadingStatus = true;

                this.apiSrv.getInstance().updateEvalEvent(
                    this.tba.id,
                    this.currentcommentid,
                    {text: this.commentEditor.text},
                    this.commentEditor.file
                ).then((data) => {
                    let msgInfo = (data.status)
                        ? {type: 'success', value: 'act.update.suce'}
                        : {type: 'error',   value: 'act.update.fail'};
                    this.setMsgInfo(msgInfo);

                    this.commentEditor.file = null;
                    this.$refs.commentUpdatedFile.value = null;

                    if(!data.status) {
                        return;
                    }

                    this.getEvalEvents(this.evaleventparams);
                    this.comments[this.currentcommentsindex].text = data.data.text;
                    this.commentData[this.currentcommentsindex].text = data.data.text;
                    this.commentData[this.currentcommentsindex].url = this.getSharingUrl(this.commentData[this.currentcommentsindex]);

                    // Update attached file
                    if (data.data.image_url !== null) {
                        this.comments[this.currentcommentsindex].img = data.data.image_url;
                        this.commentData[this.currentcommentsindex].img = data.data.image_url;
                        this.comments[this.currentcommentsindex].media = null;
                        this.commentData[this.currentcommentsindex].media = null;
                    }
                    if (data.data.media_url !== null) {
                        this.comments[this.currentcommentsindex].img = null;
                        this.commentData[this.currentcommentsindex].img = null;
                        this.comments[this.currentcommentsindex].media = data.data.media_url;
                        this.commentData[this.currentcommentsindex].media = data.data.media_url;
                    }

                    this.setComment();
                    this.showeditmodal = false;
                }).catch((error) => {
                    console.log(error);
                    this.$Loading.error();
                }).finally(() => {
                    this.loadingStatus = false;
                });
            },


            updateComment () {
                this.loadingStatus = true;
                let tbaId = this.tba.id;
                let commentData = {
                    id: this.currentcommentid,
                    text: this.commentEditor.text,
                };
                let fileData = this.commentEditor.file;
                let msgInfo = null;
                this.apiSrv.getInstance()
                    .updateTbaComment(tbaId, commentData, fileData)
                    .then((res) => {
                        msgInfo = (res.status)
                            ? {type: 'success', value: 'act.update.suce'}
                            : {type: 'error',   value: 'act.update.fail'};

                        // Update UI accordingly
                        this.updateComments({evaluateOptions: this.evaluateOptions, eventType: 'tbaComment'});
                        this.updateChartByTotalOrTbaComment();
                        this.setComment();
                        this.currentcommentid = null;
                        this.showeditmodal = false;
                    })
                    .catch((error) => {
                        console.log(error)
                    })
                    .finally(() => {
                        this.loadingStatus = false;
                        this.commentEditor.file = null;
                        this.$refs.commentUpdatedFile.value = null;
                        this.setMsgInfo(msgInfo);
                    });
            },

            deleteComment () {
                let me = this;
                setTimeout(() => {
                    let checked = confirm(me.$t('commentlist.deleteconfirm'));
                    if (checked) {
                        me.comments.splice(me.currentcommentsindex, 1);
                        me.commentData.splice(me.currentcommentsindex, 1);
                        me.currentcommentsindex = -1;
                        me.deleteTbaComment(me.currentcommentid);
                        me.updateChartByTotalOrTbaComment();
                        me.setComment();
                    }
                }, 100);
            },
        }
    )
}

</script>

<style lang="scss">
  .commentlist {
    height : 100%;
    padding: 0;
    font-size: 14px;
    color: #fff;
  }

  .comment-detail-mobile-wrapper {
    z-index: 5;
    background-color: rgba(32, 32, 32, 0.8);
    position: fixed;
    display: flex;
    justify-content: center;
    width: 100%;
    padding-left: 16px;
    padding-right: 16px;

    .comment-detail-mobile-text {
        color:#ffbb00;
        word-break: break-all;
        max-height: 200px;
        overflow: auto;
    }

    .comment-detail-mobile-attachment {
        padding: 8px;

        .comment-detail-mobile-attachment-img {
            height: 40px;
            background-color: #e0e0e0;
            padding: 1px;
            cursor: pointer;
        }

        .comment-detail-mobile-attachment-media {
            font-size: 2rem;
            padding: 1rem;
        }
    }
  }

  .ellipsis {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    white-space: normal;
  }

  .btn-default {
    color:#fff;
    background-color:#3d4b66;
    border-color:grey;
  }

  .btn-active {
    background-color:#19be6b;
  }

  .btn-controller {
      position: relative;
      width: 30px;
      height: 22px;
      border:0;
      border-radius:5px;
      margin:2px;
  }

  .btn-controller-icon {
      position: absolute;
      left: 0;
      top: 0;
      margin-left: 10px;
  }

  .blueimp-gallery-display {
      display: none;
  }

  .ivu-modal-content {
    background-color: #182328;
  }

  .ivu-table {
    background-color: #182328;
  }

  .ivu-table-cell {
    padding-left: 8px;
    padding-right: 8px;
  }

  /*外层table的border*/
  .ivu-table-wrapper {
    border:none
  }
  /*底色*/
  .ivu-table td{
    background-color: #182328;
    color: #fff;
    border:none
  }
  /*每行的基本样式*/
  .ivu-table-row td {
    background-color: #182328;
    color: #fff;
    border: none;
    cursor: pointer;
  }
  /*头部th*/
  .ivu-table-header th{
    color:#FFD3B4;
    font-weight: bold;
    background-color: #212c31;
    border: none;
  }
  /*偶数行*/
  .ivu-table-stripe-even td{
    background-color: #434343!important;
  }
  /*奇数行*/
  .ivu-table-stripe-odd td{
    background-color: #282828!important;
  }
  /*选中某一行高亮*/
  .ivu-table-row-highlight td {
    background-color: #962121!important;
  }
  /*浮在某行*/
  .ivu-table-row-hover td {
    background-color: #be6600!important;
  }
  .slide > .slide-content:hover{
      cursor: url("/images/app/clockwise-rotation.png"), auto;
  }
</style>
