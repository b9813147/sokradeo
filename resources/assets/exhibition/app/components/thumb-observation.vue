<template>
  <div class="obsrv-main-theme" :style="{ padding: styling.mainThemePadding }">
    <Row :gutter="16">
      <Col span="24">
        <h2 align="center" style="padding-bottom: 15px;"><span style="padding-left: 15px;">{{ resources.channelName }}</span> {{ $t('observation.observation_1') }}</h2>
      </Col>
      <div v-show="printSwitch">
        <Col :xs="24" :sm="12" :md="12" :lg="12"><img v-bind:src="path.tba+resources.id+'/'+resources.thum+'?'+Math.random()" v-if="resources.thum" alt=""></Col>
        <Col :xs="24" :sm="12" :md="12" :lg="12">
          <Row>
            <Col><p>{{ $t('observation.observation_2') }} : {{ resources.name }}</p></Col>
            <Col><p>{{ $t('observation.observation_3') }} : {{ resources.teacher }}</p></Col>
            <Col><p>{{ $t('observation.observation_4') }} : {{ resources.habook }}</p></Col>
            <Col><p>{{ $t('observation.observation_5') }} : {{ resources.lecture_date }}</p></Col>
            <Col><p>{{ $t('observation.observation_6') }} : {{ resources.update_time }}</p></Col>
            <Col><p>{{ $t('observation.observation_7') }} : -</p></Col>
            <Col><p>{{ $t('observation.observation_8') }} : {{ resources.channelName }}</p></Col>
            <Col><p>{{ $t('observation.observation_9') }} : -</p></Col>
            <Col><p>{{ $t('observation.observation_10') }} : -</p></Col>
            <Col><p>{{ $t('observation.observation_11') }} : <span v-for="(item,index) in resources.lessonExample" :key="index"> {{ item.type ? $t(`annexes.${item.type}`) : null }}、 </span></p></Col>
          </Row>
        </Col>
      </div>
    </Row>

    <hr class='baseline'>

    <!-- Description -->
    <Row>
      <Col span="24" style="padding-top: 20px">
        <h2>
          {{ $t("observation.observation_12") }}
          <span
            class="toggle-section-container"
            @click="toggleSectionContent(sectionContent.secDesc)"
          >
            <Icon :type="sectionContent.secDesc.isHidden ? iconType.plus : iconType.minus"></Icon>
          </span>
        </h2>
      </Col>
      <Col span="24" style="padding-top: 20px" :ref="sectionContent.secDesc.ref">
        <p class="introduction">{{ resources.description }}</p>
      </Col>
    </Row>

    <!-- Lesson Observation List -->
    <Row>
      <Col span="24" style="padding-top: 20px">
        <h2>
          {{ $t("observation.observation_13") }}
          <span
            class="toggle-section-container"
            @click="toggleSectionContent(sectionContent.secObsrvList)"
          >
            <Icon :type="sectionContent.secObsrvList.isHidden ? iconType.plus : iconType.minus"></Icon>
          </span>
        </h2>
      </Col>
      <Col span="24" :ref="sectionContent.secObsrvList.ref">
        <Row>
          <!-- Observer -->
          <Col span="24">
            <Icon type="ios-people-outline"></Icon>
            <span class="sub-title">
              {{ $t("observation.observation_14") }} :
            </span>
            {{ commentResources.observers.length }}
          </Col>
          <!-- Oberserver List -->
          <Col span="24">
            <div
              :class="[
                'observer-container',
                { 'limit-text': isMobile },
              ]"
              v-for="(item, k) in commentResources.observers"
              :key="k"
            >
              <Avatar size="small" class="observer-total">
                {{ item.totalComments }}
              </Avatar>
              <span v-if="item.name">{{ item.name }}</span>
              <span v-if="item.habook">({{ item.habook }})</span>
            </div>
          </Col>
          <!-- Course Core -->
          <Col span="24">
            <Icon type="ios-book-outline"></Icon>
            <span class="sub-title">
              {{ $t("observation.observation_15") }} :
            </span>
            {{ resources.courseCore }}
          </Col>
          <!-- Observation Focus -->
          <Col span="24">
            <Icon type="ios-information-outline"></Icon>
            <span class="sub-title">
              {{ $t("observation.observation_16") }} :
            </span>
            {{ resources.observationFocus }}
          </Col>
          <!-- Students -->
          <Col span="24">
            <Icon type="university"></Icon>
            <span class="sub-title">
              {{ $t("observation.observation_10") }} :
            </span>
            {{ resources.studentCount }}
          </Col>
          <!-- Student Feedback -->
          <Col span="24">
            <Icon type="reply"></Icon>
            <span class="sub-title">
              {{ $t("observation.observation_28") }} :
            </span>
            {{ resources.irsCount }}
            ({{
              resources.studentCount === 0
                ? 0
                : this.formatOneDecimal(
                    resources.irsCount / resources.studentCount
                  )
            }}
            {{ $t("observation.observation_26") }} )
          </Col>
          <!-- Comment -->
          <Col span="24">
            <Icon type="android-chat"></Icon>
            <span class="sub-title">
              {{ $t("observation.observation_17") }} :
            </span>
            <span>{{ commentResources.observerComments.length }}</span>
            <span>{{ commentAttachmentTotal }}</span>
          </Col>
          <!-- Comment Tags -->
          <Col span="24">
            <div
              :class="[
                'comment-tag-container',
                { 'limit-text': isMobile },
              ]"
              v-for="(v, k) in commentResources.observerCommentTags"
              :key="k"
            >
              <Avatar size="small" class="comment-tag-total">
                {{ v.totalTags }}
              </Avatar>
              <span
                :style="{
                  color: getCommentColor(v.isPositive)
                }"
              >
                {{ v.tag.name }}
              </span>
            </div>
          </Col>
          <!-- Comment (Table) -->
          <Col span="24">
            <!-- Desktop -->
            <Table
              border
              size="large"
              :columns="commentTableColumns"
              :data="commentResources.observerComments"
              v-if="!isMobile"
            ></Table>
            <!-- Mobile -->
            <div
              class="comment-table-mobile-container"
              v-else-if="isMobile"
            >
              <div
                v-for="(comment, k) in commentResources.observerComments"
                :key="k"
                :class="[
                  'comment-table-mobile-item',
                  { 'comment-table-mobile-item-striped': k % 2 === 0 },
                ]"
              >
                <Row type="flex" justify="center" align="top">
                  <Col
                    span="3"
                    class="comment-table-mobile-item-time-wrapper"
                  >
                    <span
                      class="comment-table-mobile-item-time"
                      @click="goToPlayer(comment.time)"
                    >
                      {{ fmtSecondsToMMSS(comment.time) }}
                    </span>
                  </Col>
                  <Col
                    span="21"
                    class="comment-table-mobile-item-text-wrapper"
                  >
                    <span v-html="getCommentTextMobile(comment)"></span>
                    <span class="comment-table-mobile-item-attachment">
                      <Icon
                        :type="getCommentAttachmentIcon(comment)"
                        @click="displayAttachmentViewer(comment)"
                        style="color: #1890ff; cursor: pointer;"
                      >
                      </Icon>
                    </span>
                  </Col>
                </Row>
              </div>
            </div>
          </Col>
        </Row>
      </Col>
    </Row>

    <!-- Photos -->
    <div id='attachment' style="padding-top: 20px;">
      <h2>
        {{ $t('observation.observation_23') }}
        <span
          class="toggle-section-container"
          @click="toggleSectionContent(sectionContent.secAttch)"
        >
          <Icon :type="sectionContent.secAttch.isHidden ? iconType.plus : iconType.minus"></Icon>
        </span>
      </h2>
      <Row
        :ref="sectionContent.secAttch.ref"
        v-if="commentAttachmentDetail.imgList.length > 0"
      >
        <Col
          :xs="24"
          :sm="12"
          :md="6"
          :lg="6"
          v-for="(v, k) in commentAttachmentDetail.imgList"
          :key="k"
          style="padding: 5px"
        >
          <figure
            :ref="v.uniqId"
            style="background-color: transparent"
          >
            <!-- Photo tag -->
            <div align="center">
              <figcaption>{{ v.uniqId }}</figcaption>
            </div>
            <!-- Photo image -->
            <div
              :class="
                isMobile
                  ? 'photo-image-mobile-container'
                  : 'photo-image-container'
              "
            >
              <img class="photo-image" :src="v.src" :alt="v.uniqId" />
            </div>
          </figure>
        </Col>
      </Row>
    </div>

    <!-- Report -->
    <div id="report" v-if="report" style="padding-top: 20px;">
      <h2>
        {{ $t("observation.observation_25") }}
        <span
          class="toggle-section-container"
          @click="toggleSectionContent(sectionContent.secReport)"
        >
          <Icon :type="sectionContent.secReport.isHidden ? iconType.plus : iconType.minus"
          ></Icon>
        </span>
      </h2>
      <Row type="flex" justify="center" :ref="sectionContent.secReport.ref">
        <img
          :src="path.tba + contentId + '/report.png?' + Math.random()"
          alt="sok-report"
          style="width: 90%;"
        />
      </Row>
    </div>

    <!-- Norm Table -->
    <div id="norm-table" v-if="report" style="padding-top: 20px;">
      <Row style="padding-top: 20px;">
        <h2>
          {{ normTableHeader }}
          <span
            class="toggle-section-container"
            @click="toggleSectionContent(sectionContent.secNormTable)"
          >
            <Icon :type="sectionContent.secNormTable.isHidden ? iconType.plus : iconType.minus"></Icon>
          </span>
        </h2>
        <Col span="24" :ref="sectionContent.secNormTable.ref">
          <!-- 
            Use v-show instead of conditional props because of the followings:
              - There are two col and data sets (one for mobile and one for desktop)
              - To avoid: You may have an infinite update loop in watcher with expression "columns"
           -->
          <Table
            v-show="!isMobile"
            border
            size="large"
            :columns="normTableColumns"
            :data="normTableData"
          ></Table>
          <Table
            v-show="isMobile"
            border
            :columns="normTableMobileColumns"
            :data="normTableMobileData"
          ></Table>
        </Col>
      </Row>
    </div>

    <!-- Modal Image Viewer -->
    <Modal
      class="image-viewer-modal"
      style="background-color: #182328"
      v-model="showImageViewerModal"
      @on-cancel="closeImageViewerModal"
    >
      <section slot="header">
        <p style="color: #ffffff;">
          {{ modalImageViewerHeader }}
        </p>
      </section>
      <div class="image-viewer-container">
        <img
          class="image-src"
          :src="modalImageViewerSrc"
          :alt="modalImageViewerHeader"
        />
      </div>
      <section slot="footer"><p></p></section>
    </Modal>

    <!-- Media Player Modal -->
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
    <!-- Back to Top -->
    <BackTop></BackTop>
  </div>
</template>

<script>
import _                 from 'lodash'
import Vuex              from 'vuex';
import CpntMediaPlayer   from '../../../app/modules/tbaplayer/components/media-player.vue'
import TimeFormatterUtil from "../../../../commons/time-formatter";

export default {
  name      : "observation",
  emits     : ["obsrvDisplayCtrl"],
  components: {
    'cpnt-media-player': CpntMediaPlayer
  },
  computed  : _.merge(
      Vuex.mapState(["path", "user", "extList"]),
      Vuex.mapGetters(['logined']),
      {
        styling() {
          // This is a computed property for styling (default and mobile)
          return {
            mainThemePadding: this.isMobile ? "10px" : "20px 10%",
            commentTable: {
              fontSize: this.isMobile ? "0.8em" : "1em",
              iconSize: this.isMobile ? "14" : "18",
            }
          }
        },
        iconType() {
          return {
            plus: "android-add-circle",
            minus: "android-remove-circle",
          }
        },
        commentTableMobileColumns() {
          // This is a list of columns for comment mobile view
          // Please refer to the commentTableColumns for more detail
          let timepointCol = this.commentTableColumns[0];
          let textCol = this.commentTableColumns[3];
          let attachmentCol = this.commentTableColumns[this.commentTableColumns.length - 1];
          
          return [timepointCol, textCol, attachmentCol];
        },
        commentAttachmentDetail() {
          // Create lists of attachments (image, video, audio)
          let commentAttachmentDetailData = {
            imgList: [],
            videoList: [],
            audioList: [],
          };
          if (this.commentResources.observerComments.length < 1)
            return commentAttachmentDetailData;

          // Iterate through each comment
          let imgNo = 1;
          let videoNo = 1;
          let audioNo = 1;
          _.forEach(this.commentResources.observerComments, (v, k) => {
            if (!v.attachment.src || !v.attachment.ext) return;

            let ext = v.attachment.ext.toLowerCase();
            if (
              v.attachment.type === "image" &&
              this.extList.img.includes(ext)
            ) {
              // Img
              commentAttachmentDetailData.imgList.push({
                uniqId: "P" + imgNo++,
                commentIdx: k,
                src: v.attachment.src,
              });
            } else if (
              v.attachment.type === "media" &&
              this.extList.video.includes(ext)
            ) {
              // Video
              commentAttachmentDetailData.videoList.push({
                uniqId: "V" + videoNo++,
                commentIdx: k,
                src: v.attachment.src,
              });
            } else if (
              v.attachment.type === "media" &&
              this.extList.audio.includes(ext)
            ) {
              // Audio
              commentAttachmentDetailData.audioList.push({
                uniqId: "A" + audioNo++,
                commentIdx: k,
                src: v.attachment.src,
              });
            }
          });

          return commentAttachmentDetailData;
        },
        commentAttachmentTotal() {
          // This is a list of all attachment counts for presentation
          let imgLabel = this.$t("observation.observation_attachment.img");
          let videoLabel = this.$t("observation.observation_attachment.video");
          let audioLabel = this.$t("observation.observation_attachment.audio");
          return `(
            ${imgLabel}: ${this.commentAttachmentDetail.imgList.length},
            ${videoLabel}: ${this.commentAttachmentDetail.videoList.length},
            ${audioLabel}: ${this.commentAttachmentDetail.audioList.length}
          )`;
        },
        normTableHeader() {
          return this.$t("observation.observation_30").replace(
            /\{year\}/g,
            this.resources.globalNormRefData.year
          );
        },
        yearlyItemTitle() {
          if (!this.resources.globalNormRefData) return "";
          return this.$t(
            "observation.observation_norm_table_col.item.norm"
          ).replace(/\{year\}/g, this.resources.globalNormRefData.year);
        },
        yearlyNormData() {
          // Create yearly data
          // This is a fixed data provided by analysts
          let yearlyNormData = this.resources.globalNormRefData;
          let yearlyNormDataTemplate = this._getNormTableTemplate();
          if (!yearlyNormData) return yearlyNormDataTemplate;

          // Assign yearly data to template
          yearlyNormDataTemplate["itemName"] = this.yearlyItemTitle;
          yearlyNormDataTemplate["p1"] = yearlyNormData.p1;
          yearlyNormDataTemplate["p2"] = yearlyNormData.p2;
          yearlyNormDataTemplate["p3"] = yearlyNormData.p3;
          yearlyNormDataTemplate["p4"] = yearlyNormData.p4;
          yearlyNormDataTemplate["p5"] = yearlyNormData.p5;
          yearlyNormDataTemplate["p6"] = yearlyNormData.p6;
          yearlyNormDataTemplate["freq"] = yearlyNormData.freq;
          yearlyNormDataTemplate["t"] = yearlyNormData.tech_interact;
          yearlyNormDataTemplate["p"] = yearlyNormData.peda_app;
          yearlyNormDataTemplate["feedbackAvg"] = yearlyNormData.feedback_avg;

          // One decimal format
          _.forEach(yearlyNormDataTemplate, (v, k) => {
            if (k === "itemName") return;
            yearlyNormDataTemplate[k] = this.formatOneDecimal(v);
          });

          return yearlyNormDataTemplate;
        },
        normTableMobileColumns() {
          // This is a list of columns for norm mobile view
          // It is a transposed version of normTableColumns
          // Please refer to the normTableColumns for more detail
          if (!this.resources.globalNormRefData) return [];
          let itemCol = {
            title: this.$t("observation.observation_norm_table_col.item.title"),
            key: "itemName",
            align: "center",
          };
          let yearlyCol = {
            title: this.yearlyItemTitle,
            key: "yearlyNormData",
            align: "center",
          };
          let curLessonCol = {
            title: this.$t("observation.observation_norm_table_col.item.content"),
            key: "curLessonNormData",
            align: "center",
            render: (h, item) => {
              return h("div", [
                h(
                  "span",
                  {
                    style: {
                      color: this.checkHighlightAdditionMobile(item),
                    },
                  },
                  item.row.curLessonNormData
                ),
              ]);
            },
          };
          return [itemCol, yearlyCol, curLessonCol];
        },
        normTableMobileData() {
          // Transposed version of normTableData (for Mobile)
          if (this.normTableData.length !== 2) return [];
          let normTableData = this.normTableData;

          let yearlyTableData = normTableData[0];
          let curLessonTableData = normTableData[1];

          return [
            // Peda App Title
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.teaching_method_app.title"),
            ),
            // Peda App Data
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.teaching_method_app.item_1"),
              yearlyTableData.p1,
              curLessonTableData.p1
            ),
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.teaching_method_app.item_2"),
              yearlyTableData.p2,
              curLessonTableData.p2
            ),
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.teaching_method_app.item_3"),
              yearlyTableData.p3,
              curLessonTableData.p3
            ),
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.teaching_method_app.item_4"),
              yearlyTableData.p4,
              curLessonTableData.p4
            ),
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.teaching_method_app.item_5"),
              yearlyTableData.p5,
              curLessonTableData.p5
            ),
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.teaching_method_app.item_6"),
              yearlyTableData.p6,
              curLessonTableData.p6
            ),
            // Diff Title
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.diff.title"),
            ),
            // Diff Data
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.diff.item_1"),
              yearlyTableData.freq,
              curLessonTableData.freq
            ),
            // Consolidation Title
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.consolidation.title"),
            ),
            // Consolidation Data
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.consolidation.item_1"),
              yearlyTableData.t,
              curLessonTableData.t
            ),
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.consolidation.item_2"),
              yearlyTableData.p,
              curLessonTableData.p
            ),
            // Feedback Title and Data
            this._getNormTableTransposedTemplate(
              this.$t("observation.observation_norm_table_col.group.feedback"),
              yearlyTableData.feedbackAvg,
              curLessonTableData.feedbackAvg
            ),
          ];
        },
      }
  ),
  props: {
    contentId: {
      type: Number,
      required: true
    },
    channelId: {
      type: Number,
      required: true
    },
    printSwitch: {
      type: Boolean,
      default: false
    },
    report: {
      type: Boolean,
      required: true
    },
    isMobile: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      sectionContent: {
        secDesc: {
          ref: "section-description",
          isHidden: false,
        },
        secObsrvList: {
          ref: "section-observation-list",
          isHidden: false,
        },
        secAttch: {
          ref: "section-attachment",
          isHidden: false,
        },
        secReport: {
          ref: "section-report",
          isHidden: false,
        },
        secNormTable: {
          ref: "section-norm-table",
          isHidden: false,
        },
      },
      commentTableColumns: [
        {
          title: this.$t("observation.observation_18"),
          sortable: true,
          align: "center",
          key: "time_point",
          render: (h, item) => {
            return h("div", [
              h(
                "span",
                {
                  style: {
                    color: "#4682b4",
                    fontWeight: "bold",
                    cursor: "pointer",
                    fontSize: this.styling.commentTable.fontSize,
                  },
                  on: {
                    click: () => {
                      this.goToPlayer(item.row.time);
                    },
                  },
                },
                this.fmtSecondsToMMSS(item.row.time)
              ),
            ]);
          },
          maxWidth: this.isMobile ? 70 : 100,
        },
        {
          title: this.$t("observation.observation_19"),
          sortable: true,
          key: "name",
          maxWidth: 120,
        },
        {
          title: this.$t("observation.observation_20"),
          sortable: true,
          align: "center",
          key: "tag",
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
          maxWidth: 200,
        },
        {
          title: this.$t("observation.observation_21"),
          sortable: true,
          key: "text",
          render: (h, item) => {
            return h(
              "div",
              {
                domProps: {
                  innerHTML: this.getCommentText(item.row),
                },
                style: {
                  padding: "5px",
                  fontSize: this.styling.commentTable.fontSize,
                },
              },
            );
          },
        },
        {
          title: this.$t("observation.observation_22"),
          sortable: true,
          align: "center",
          render: (h, params) => {
            if (params.row.attachment.type === "image") {
              let imgUniqId = this.getImgUniqId(params.index);
              return h("div", [
                h(
                  "span",
                  {
                    style: {
                      color: "#4682b4",
                      cursor: "pointer",
                    },
                    on: {
                      click: () => {
                        let commentAttachmentImgData = _.find(
                          this.commentAttachmentDetail.imgList,
                          ["uniqId", imgUniqId]
                        );
                        if (!commentAttachmentImgData) return;
                        let modalImageViewerHeader =
                          commentAttachmentImgData.uniqId;
                        let modalImageViewerSrc = commentAttachmentImgData.src;
                        this.openImageViewer(
                          modalImageViewerHeader,
                          modalImageViewerSrc
                        );
                      },
                    },
                  },
                  imgUniqId
                ),
              ]);
            } else if (params.row.attachment.type === "media") {
              return h("div", [
                h("Icon", {
                  props: {
                    type: this.getCommentMediaIcon(params.row),
                    size: this.styling.commentTable.iconSize,
                  },
                  style: {
                    cursor: "pointer",
                  },
                  on: {
                    click: () => {
                      let modalMediaHeader = this.fmtSecondsToMMSS(params.row.time) + " " + params.row.text;
                      let modalMediaSrc = params.row.attachment.src;
                      this.openAttachedMedia(modalMediaHeader, modalMediaSrc);
                    },
                  },
                }),
              ]);
            } else {
              return h("div", "");
            }
          },
          maxWidth: this.isMobile ? 70 : 140,
        },
      ],
      normTableColumns: [
        // 項目
        {
          title: this.$t('observation.observation_norm_table_col.item.title'),
          key  : 'itemName',
          align: 'center',
        },
        // 教法應用
        {
          title   : this.$t('observation.observation_norm_table_col.group.teaching_method_app.title'),
          align   : 'center',
          children: [
            {
              title : this.$t('observation.observation_norm_table_col.group.teaching_method_app.item_1'),
              key   : 'p1',
              render: (h, item) => {
                return h('div', [
                  h('span', {
                    style: {
                      color: this.checkHighlightAddition(item.column.key, item.row),
                      fontWeight: "bold",
                    }
                  }, item.row.p1),
                ]);
              }
            },
            {
              title : this.$t('observation.observation_norm_table_col.group.teaching_method_app.item_2'),
              key   : 'p2',
              render: (h, item) => {
                return h('div', [
                  h('span', {
                    style: {
                      color: this.checkHighlightAddition(item.column.key, item.row),
                      fontWeight: "bold",
                    }
                  }, item.row.p2),
                ]);
              }
            },
            {
              title : this.$t('observation.observation_norm_table_col.group.teaching_method_app.item_3'),
              key   : 'p3',
              render: (h, item) => {
                return h('div', [
                  h('span', {
                    style: {
                      color: this.checkHighlightAddition(item.column.key, item.row),
                      fontWeight: "bold",
                    }
                  }, item.row.p3),
                ]);
              }
            },
            {
              title : this.$t('observation.observation_norm_table_col.group.teaching_method_app.item_4'),
              key   : 'p4',
              render: (h, item) => {
                return h('div', [
                  h('span', {
                    style: {
                      color: this.checkHighlightAddition(item.column.key, item.row),
                      fontWeight: "bold",
                    }
                  }, item.row.p4),
                ]);
              }
            },
            {
              title : this.$t('observation.observation_norm_table_col.group.teaching_method_app.item_5'),
              key   : 'p5',
              render: (h, item) => {
                return h('div', [
                  h('span', {
                    style: {
                      color: this.checkHighlightAddition(item.column.key, item.row),
                      fontWeight: "bold",
                    }
                  }, item.row.p5),
                ]);
              }
            },
            {
              title : this.$t('observation.observation_norm_table_col.group.teaching_method_app.item_6'),
              key   : 'p6',
              render: (h, item) => {
                return h('div', [
                  h('span', {
                    style: {
                      color: this.checkHighlightAddition(item.column.key, item.row),
                      fontWeight: "bold",
                    }
                  }, item.row.p6),
                ]);
              }
            },
          ]
        },
        // 差異化
        {
          title   : this.$t('observation.observation_norm_table_col.group.diff.title'),
          align   : 'center',
          children: [
            {
              title : this.$t('observation.observation_norm_table_col.group.diff.item_1'),
              key   : 'freq',
              render: (h, item) => {
                return h('div', [
                  h('span', {
                    style: {
                      color: this.checkHighlightAddition(item.column.key, item.row),
                      fontWeight: "bold",
                    }
                  }, item.row.freq),
                ]);
              }
            },
          ]
        },
        // 合併指數
        {
          title   : this.$t('observation.observation_norm_table_col.group.consolidation.title'),
          align   : 'center',
          children: [
            {
              title : this.$t('observation.observation_norm_table_col.group.consolidation.item_1'),
              key   : 't',
              render: (h, item) => {
                return h('div', [
                  h('span', {
                    style: {
                      color: this.checkHighlightAddition(item.column.key, item.row),
                      fontWeight: "bold",
                    }
                  }, item.row.t),
                ]);
              }
            },
            {
              title : this.$t('observation.observation_norm_table_col.group.consolidation.item_2'),
              key   : 'p',
              render: (h, item) => {
                return h('div', [
                  h('span', {
                    style: {
                      color: this.checkHighlightAddition(item.column.key, item.row),
                      fontWeight: "bold",
                    }
                  }, item.row.p),
                ]);
              }
            },
          ]
        },
        // 反饋次數
        {
          title : this.$t('observation.observation_norm_table_col.group.feedback'),
          key   : 'feedbackAvg',
          align : 'center',
          render: (h, item) => {
            return h('div', [
              h('span', {
                style: {
                  color: this.checkHighlightAddition(item.column.key, item.row),
                  fontWeight: "bold",
                }
              }, item.row.feedbackAvg),
            ]);
          }
        }
      ],
      normTableData   : [],
      resources       : {
        id                 : null,
        name               : null,
        teacher            : null,
        habook             : null,
        lecture_date       : null,
        update_time        : null,
        thum               : null,
        channelName        : null,
        lessonExample      : {
          type: String
        },
        description        : null,
        commentMark        : {},
        commentTotal       : null,
        courseCore         : null,
        observationFocus   : null,
        studentCount       : null,
        irsCount           : null,
        statistics         : [],
        globalNormRefData  : [],
      },
      commentResources: {
        observerComments: [],
        observerCommentTags: [],
        observers: [],
      },
      // Media Modal
      showMediaModal  : false,
      modalMediaHeader: "",
      modalMediaSrc   : "",
      // Image Viewer Modal
      showImageViewerModal: false,
      modalImageViewerHeader: "",
      modalImageViewerSrc: "",
    }
  },
  watch: {
    'commentResources.observerComments' (v) {
      let displayCtrlBtn = v.length > 0;
      this.$emit("obsrvDisplayCtrl", displayCtrlBtn);
    },
  },
  methods: {
    init() {
      this.getObservation(this.contentId, this.channelId);

      setTimeout(() => this.printSwitch ? window.print() : null, 3000);
    },
    getObservation(contentId, channelId) {
      let _this = this
      let url = `/exhibition/tbavideo/get-observation-info?contentId=${contentId}&channelId=${channelId}`;

      axios.get(url).then(response => {
        if (response.status !== 200) return response.statusText;

        // TbaInfo -> resources
        response.data.tbaInfo.forEach((item => {
          _this.resources.id = item.id !== undefined ? item.id : null
          _this.resources.name = item.name !== undefined ? item.name : null
          _this.resources.teacher = item.teacher !== undefined ? item.teacher : null
          _this.resources.habook = item.habook !== undefined ? item.habook : null
          _this.resources.lecture_date = item.lecture_date !== undefined ? item.lecture_date : null
          _this.resources.update_time = item.update_time !== undefined ? item.update_time : null
          _this.resources.thum = item.thum !== undefined ? item.thum : null
          _this.resources.channelName = item.channelName !== undefined ? item.channelName : null
          _this.resources.channelName = item.channelName !== undefined ? item.channelName : null
          _this.resources.lessonExample = item.lessonExample
          _this.resources.description = item.description
          _this.resources.courseCore = item.courseCore !== undefined ? item.courseCore : null
          _this.resources.observationFocus = item.observationFocus !== undefined ? item.observationFocus : null
          _this.resources.studentCount = item.studentCount !== undefined ? item.studentCount : null
          _this.resources.irsCount = item.irsCount !== undefined ? item.irsCount : null
          _this.resources.statistics = item.statistics !== undefined ? item.statistics : []
          _this.resources.globalNormRefData = item.globalNormRefData ? item.globalNormRefData : []
        }));

        // ObserverComments -> commentResources
        let commentInfo = response.data.commentInfo;
        _this.commentResources.observerComments = commentInfo.observerComments ? commentInfo.observerComments : [];
        _this.commentResources.observerCommentTags = commentInfo.observerCommentTags ? commentInfo.observerCommentTags : [];
        _this.commentResources.observers = commentInfo.observers ? commentInfo.observers : [];

        // Create norm table
        _this.createdNormTable();
      });
    },
    goToPlayer(s) {
      //  Minute convert second
      // let s = _.toNumber(_.find(v.split(':')) * 60) + _.toNumber(_.findLast(v.split(':')))
      let groupId = _.find(_.split(this.$route.query.groupIds, ','));
      let channelId = this.$route.query.channelId;

      let url = groupId
                ? `${process.env.MIX_APP_URL}/group/${groupId}/watch/channel/${channelId}/tbavideo?contentId=${this.resources.id}&groupIds=${groupId}&channelId=${channelId}&start=${s}`
                : `${process.env.MIX_APP_EXHIBITION_URL}tbavideo/watch-as-open?contentId=${this.resources.id}&start=${s}`
      window.open(url);
    },
    fmtSecondsToMMSS(seconds) {
      return TimeFormatterUtil.formatSecondsToMMSS(seconds);
    },
    getImgUniqId(commentIdx) {
      let commentAttachmentImgData = _.find(this.commentAttachmentDetail.imgList, [
        "commentIdx",
        commentIdx,
      ]);
      if (!commentAttachmentImgData) return null;
      return commentAttachmentImgData.uniqId;
    },
    toggleSectionContent(sectionContent) {
      let ref = sectionContent.ref;
      this.$refs[ref].$el.classList.toggle("is-hidden");
      sectionContent.isHidden = !sectionContent.isHidden;
    },
    openAttachedMedia(modalMediaHeader, modalMediaSrc) {
      if (!modalMediaHeader || !modalMediaSrc)
        return;
      this.modalMediaHeader = modalMediaHeader;
      this.modalMediaSrc = modalMediaSrc;
      this.showMediaModal = true;
    },
    closeAttachedMedia() {
      this.modalMediaHeader = "";
      this.modalMediaSrc = "";
      this.showMediaModal = false;
    },
    openImageViewer(modalImageViewerHeader, modalImageViewerSrc) {
      if (!modalImageViewerSrc) return;
      this.modalImageViewerHeader = modalImageViewerHeader;
      this.modalImageViewerSrc = modalImageViewerSrc;
      this.showImageViewerModal = true;
    },
    closeImageViewerModal() {
      this.modalImageViewerHeader = "";
      this.modalImageViewerSrc = "";
      this.showImageViewerModal = false;
    },
    displayAttachmentViewer(commentData) {
      if (!commentData || !commentData.attachment) return;

      let type = commentData.attachment.type;
      let src = commentData.attachment.src;
      let header = this.fmtSecondsToMMSS(commentData.time) + " " + commentData.text;
      switch (type) {
        case "media":
          this.openAttachedMedia(header, src);
          break;
        case "image":
          this.openImageViewer(header, src);
          break;
        default:
          break;
      }
    },
    getCommentColor(type) {
      switch (type) {
        case 0:
          return "#e72d41"; // red
        case 1:
          return "#8fbc8f"; // green
        default:
          return "#dedede"; // gray
      }
    },
    getCommentLabel(commentData) {
      return commentData.type + " - " + commentData.tag.name;
    },
    getCommentText(commentData) {
      // Get comment text for the comment table row
      let text = !_.isEmpty(commentData.text)
        ? commentData.text.replace(/\n|\r\n/g, "<br>")
        : "";
      return text;
    },
    getCommentTextMobile(commentData) {
      // This will return HTML string
      let htmlStr = `
        <span style='font-style: italic'>
          ${commentData.name}
        </span>
        <span>
          ${this.getCommentText(commentData)}
        </span>
      `;
      return htmlStr;
    },
    getCommentMediaIcon(commentData) {
      // This method will return iView icon for media file
      // Only support media type (video, audio)
      let icon = "";
      let attachmentData = commentData.attachment;
      if (!attachmentData || !attachmentData.ext) return icon;

      let ext = attachmentData.ext.toLowerCase();
      if (this.extList.audio.includes(ext)) icon = "volume-medium";
      else if (this.extList.video.includes(ext)) icon = "ios-videocam";

      return icon;
    },
    getCommentAttachmentIcon(commentData) {
      // This method will return iView icon for attachment file
      // Support all file types (image, video, audio)
      let icon = "";
      let attachmentData = commentData.attachment;
      if (!attachmentData || !attachmentData.ext) return icon;

      let ext = attachmentData.ext.toLowerCase();
      if (this.extList.img.includes(ext)) icon = "image";
      else if (this.extList.video.includes(ext)) icon = "ios-videocam";
      else if (this.extList.audio.includes(ext)) icon = "volume-medium";

      return icon;
    },
    createdNormTable() {
      // Create yearly data
      this.normTableData.push(this.yearlyNormData);

      // Create content statistical data
      let contentNormData = this._getNormTableTemplate();
      contentNormData["itemName"] = this.$t(
        "observation.observation_norm_table_col.item.content"
      );
      contentNormData["p1"] =
        _.get(_.find(this.resources.statistics, ["type", 49]), "idx", 0) * 100;
      contentNormData["p2"] =
        _.get(_.find(this.resources.statistics, ["type", 50]), "idx", 0) * 100;
      contentNormData["p3"] =
        _.get(_.find(this.resources.statistics, ["type", 53]), "idx", 0) * 100;
      contentNormData["p4"] =
        _.get(_.find(this.resources.statistics, ["type", 61]), "idx", 0) * 100;
      contentNormData["p5"] =
        _.get(_.find(this.resources.statistics, ["type", 52]), "idx", 0) * 100;
      contentNormData["p6"] =
        _.get(_.find(this.resources.statistics, ["type", 54]), "idx", 0) * 100;
      contentNormData["freq"] = this._convertNormTableFreq(
        _.get(_.find(this.resources.statistics, ["type", 31]), "idx", 0)
      );
      contentNormData["t"] = _.get(
        _.find(this.resources.statistics, ["type", 47]),
        "idx",
        0
      );
      contentNormData["p"] = _.get(
        _.find(this.resources.statistics, ["type", 48]),
        "idx",
        0
      );
      contentNormData["feedbackAvg"] =
        this.resources.irsCount && this.resources.studentCount
          ? this.resources.irsCount / this.resources.studentCount
          : 0;

      // One decimal format
      _.forEach(contentNormData, (v, k) => {
        if (k === "itemName") return;
        contentNormData[k] = this.formatOneDecimal(v);
      });

      this.normTableData.push(contentNormData);
    },
    _getNormTableTemplate() {
      // This is a template for adding row data to normTableData(Array)
      return {
        "itemName"           : "",
        "p1"                 : 0,
        "p2"                 : 0,
        "p3"                 : 0,
        "p4"                 : 0,
        "p5"                 : 0,
        "p6"                 : 0,
        "freq"               : 0,
        "t"                  : 0,
        "p"                  : 0,
        "feedbackAvg"        : 0
      };
    },
    _getNormTableTransposedTemplate(name, yearlyData = "", curLessonData = "") {
      // This is a template for adding row data to normTableTransposedData(Array)
      // Please refer to normTableMobileColumns
      return {
        "itemName": name,
        "yearlyNormData": yearlyData,
        "curLessonNormData": curLessonData,
      };
    },
    _convertNormTableFreq(freq) {
      if (freq >= 6) return 100;
      else if (freq === 5) return 99;
      else if (freq === 4) return 96;
      else if (freq === 3) return 90;
      else if (freq === 2) return 78;
      else if (freq === 1) return 60;
      else return 0;
    },
    formatOneDecimal(num) {
      if (!num) num = 0;
      return Number(num).toFixed(1);
    },
    checkHighlightAddition(key, data) {
      // Add highlight color (yellow) if content value is greater than yearly norm value
      let curValue = parseInt(data[key]);
      let yearlyValue = parseInt(this.yearlyNormData[key]);
      return curValue > yearlyValue ? "#8fbc8f" : "";
    },
    checkHighlightAdditionMobile(data) {
      // Add highlight color (yellow) if content value is greater than yearly norm value
      // This is used for the transposed norm table
      let curValue = parseInt(data.row.curLessonNormData);
      let yearlyValue = parseInt(data.row.yearlyNormData);
      return curValue >= yearlyValue ? "#8fbc8f" : "";
    },
  },
  created() {
    // Use created() to call APi data for filling data properties
    // ref: https://stackoverflow.com/questions/45813347/difference-between-the-created-and-mounted-events-in-vue-js
    this.init();
  }
}
</script>

<style lang="scss">
.obsrv-main-theme {
  color: #060400;
  background-color: #fbfbfb;
  border-radius: 20px;
  box-shadow: 0 5px 14px 0 rgba(0, 0, 0, 0.7);

  img {
    border-radius: 8px;
    width: 100%;
  }

  h2 {
    color: #060400;
  }

  .toggle-section-container {
    font-size: 12px;
    cursor: pointer;
  }

  .is-hidden {
    display: none;
  }

  .sub-title {
    font-weight: bold;
  }

  .limit-text {
    max-width: 15em;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .observer-container {
    padding: 2px;
    text-align: left;
    display: inline-block;

    .observer-total {
      background: #f0f8ff;
      color: #4682b4;
      font-weight: bold;
    }
  }

  .comment-tag-container {
    padding: 2px;
    text-align: left;
    display: inline-block;

    .comment-tag-total {
      background: #f0f8ff;
      color: #4682b4;
      font-weight: bold;
    }
  }

  .comment-table-mobile-container {

    .comment-table-mobile-item-striped {
      background-color: #efefef;
    }

    .comment-table-mobile-item {
      font-size: 0.8rem;
      text-align: justify;
      padding: 5px;

      .comment-table-mobile-item-text-wrapper {
        padding: 0 3px 0 3px;

        white-space: normal;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .comment-table-mobile-item-time-wrapper {
        font-size: 0.7rem;
        text-align: center;

        .comment-table-mobile-item-time {
          color: #4682b4;
          font-weight: bold;
          cursor: pointer;
        }
      }
    }
  }

  .photo-image-container {
    height: 250px;
    width: auto;
    text-align: center;
  }

  .photo-image-mobile-container {
    height: auto;
    width: auto;
    text-align: center;
  }

  .photo-image {
    max-width: 250px;
    max-height: 250px;
  }
  
  // iview
  .ivu-table td {
    color: #060400;
  }

  .ivu-table-header th {
    color: #060400;
  }

  .ivu-table-tbody {
    color: #060400;
  }

  .ivu-modal-content {
    background-color: #182328;
  }
}

.image-viewer-modal {
  .image-viewer-container {
    height: 250px;
    width: 100%;
    text-align: center;
    contain: content;

    .image-src {
      width: auto;
      height: 100%;
    }
  }
}
</style>
