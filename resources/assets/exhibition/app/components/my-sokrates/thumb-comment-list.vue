<template>
  <div class="comment-list-container" v-if="mode">
    <!-- Search -->
    <div class="comment-search-container">
      <Row>
        <Col span="16">
          <i-input
            v-model="commentFilter"
            :placeholder="$t('comments.filter.search')"
          ></i-input>
        </Col>
        <Col span="4">
          <Icon type="search" class="search-btn" @click="getCommentList">
          </Icon>
        </Col>
      </Row>
    </div>
    <!-- Comments -->
    <div class="comment-content-container">
      <!-- Desktop -->
      <Table
        border
        size="large"
        :columns="commentTableColumns"
        :data="commentTableDataList"
        :loading="commentPager.busy"
        v-if="!isMobileBrowser"
      ></Table>
      <!-- Mobile -->
      <div class="comment-content-mobile-container" v-else-if="isMobileBrowser">
        <cpnt-loading v-if="commentPager.busy"></cpnt-loading>
        <div
          v-else
          v-for="(comment, k) in commentTableDataList"
          :key="k"
          :class="[
            'comment-content-mobile-item',
            { 'comment-content-mobile-item-striped': k % 2 === 0 },
          ]"
        >
          <Row type="flex" justify="center" align="top">
            <Col span="24" class="comment-content-mobile-item-text-wrapper">
              <p
                class="comment-content-mobile-item-text-title"
                @click="openVideoFromComment(comment)"
              >
                {{ comment.lessonName }}
              </p>
              <span
                class="comment-content-mobile-item-text-title"
                @click="openVideoFromComment(comment)"
              >
                {{ fmtSecondsToMMSS(comment.time) }}
              </span>
              <span v-html="getCommentItemTextMobile(comment)"></span>
              <span class="comment-content-mobile-item-attachment">
                <Icon
                  :type="getCommentMediaIcon(comment)"
                  @click="openAttachmentModal(comment)"
                  style="color: #1890ff; cursor: pointer"
                ></Icon>
              </span>
            </Col>
          </Row>
        </div>
      </div>
    </div>
    <!-- Comment Table Pager -->
    <Col span="24">
      <Page
        class="pager-center"
        :page-size="commentPager.size"
        :total="commentPager.total"
        :current="commentPager.pageIndex"
        @on-change="commentLoadMore"
      ></Page>
    </Col>
    <!-- Attachment Modal -->
    <Modal
      class="attachment-modal"
      style="background-color: #182328"
      v-model="showAttachmentModal"
      :loading="!modalAttachmentSrc"
      @on-cancel="closeAttachmentModal"
    >
      <section slot="header">
        <p style="color: #ffffff">{{ modalAttachmentHeader }}</p>
      </section>
      <section>
        <div
          class="media-player-container"
          v-if="modalAttachmentType === 'media'"
        >
          <cpnt-media-player :mediaSrc="modalAttachmentSrc"></cpnt-media-player>
        </div>
        <div
          class="photo-image-container"
          v-else-if="modalAttachmentType === 'image'"
        >
          <img class="photo-image" :src="modalAttachmentSrc" alt="" />
        </div>
      </section>
      <section slot="footer"><p></p></section>
    </Modal>
  </div>
</template>

<script>
function decode64(input) {
  try {
    return decodeURIComponent(escape(window.atob(unescape(input))));
  } catch (e) {
    return "";
  }
}

function encode64(input) {
  try {
    return escape(window.btoa(unescape(encodeURIComponent(input))));
  } catch (e) {
    return "";
  }
}

import _ from "lodash";
import Vuex from "vuex";
import VueClipboard from "vue-clipboard2";

import CpntMediaPlayer from "../../../../app/modules/tbaplayer/components/media-player.vue";
import CpntLoading from "../thumb-loading.vue";

import TimeFormatterUtil from "../../../../../commons/time-formatter";

Vue.use(VueClipboard);

export default {
  name: "comment-list",
  components: {
    "cpnt-media-player": CpntMediaPlayer,
    "cpnt-loading": CpntLoading,
  },
  props: ["mode"], // "isMark", "public", "private"
  computed: _.merge(
    Vuex.mapState(["path", "user", "extList", "isMobileBrowser"]),
    Vuex.mapGetters(["logined"]),
    {
      isPagerOutOfRange() {
        return this.commentPager.pageIndex > this.commentPager.lastPage;
      },
    }
  ),
  watch: {
    mode(v) {
      // This is to re-render and recall API when comment mode is changed
      this.commentFilter = "";
      this.getCommentList();
    },
    commentTableDataList(commentData) {
      this.createFilterList(3, commentData, "tag", "name"); // tag
    },
  },
  data() {
    return {
      commentTableDataList: [],
      commentFilter: "",
      commentCurMode: null,
      commentLoadMoreUrl: null,
      commentPager: {
        busy: false,
        pageIndex: 1,
        lastPage: 0,
        total: 0,
        size: 100,
      },
      commentTableColumns: [
        {
          title: this.$t("comments.col.index"),
          type: "index",
          align: "center",
          width: 100,
        },
        {
          title: this.$t("comments.col.lessonName"),
          key: "lessonName",
          sortable: true,
          ellipsis: true,
          width: 250,
        },
        // Disabled 'teacher' col
        // {
        //   title: this.$t("comments.col.teacher"),
        //   sortable: true,
        //   key: "teacher",
        //   width: 250,
        //   render: (h, param) => {
        //     return h("div", [
        //       h(
        //         "span",
        //         param.row.teacher.name + " " + "(" + param.row.teacher.habookId + ")"
        //       ),
        //     ]);
        //   },
        // },
        {
          title: this.$t("comments.col.time"),
          sortable: true,
          align: "center",
          key: "time",
          width: 100,
          renderHeader: (h, param) => {
            return h("span", [
              h("Icon", {
                props: {
                  type: "link",
                },
                style: {
                  "font-size": "18px",
                },
              }),
            ]);
          },
          render: (h, param) => {
            return h("div", [
              h(
                "span",
                {
                  style: {
                    color: "#04e2f6",
                    cursor: "pointer",
                  },
                  on: {
                    click: () => {
                      let url = this.getSharingUrl(param.row);
                      this.openVideoFromUrl(url);
                    },
                  },
                },
                this.fmtSecondsToMMSS(param.row.time)
              ),
            ]);
          },
        },
        {
          title: this.$t("comments.col.tag"),
          key: "tag",
          align: "center",
          sortable: true,
          width: 250,
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
          title: this.$t("comments.col.comment"),
          sortable: true,
          key: "text",
          render: (h, param) => {
            let text = !_.isEmpty(param.row.text)
              ? param.row.text.replace(/\n|\r\n/g, "<br>")
              : null;
            return h(
              "div",
              {
                domProps: {
                  innerHTML: text,
                },
                style: {
                  "font-size": "16px",
                  padding: "5px",
                },
              },
              param.row.text
            );
          },
        },
        {
          title: this.$t("comments.col.date"),
          sortable: true,
          key: "updatedAt",
          align: "center",
          width: 200,
          render: (h, param) => {
            return h(
              "div",
              {
                style: {},
              },
              this.formatDatetime(param.row.updatedAt)
            );
          },
        },
        {
          title: this.$t("comments.col.attachment"),
          sortable: true,
          align: "center",
          width: 100,
          renderHeader: (h, param) => {
            return h("span", [
              h("Icon", {
                props: {
                  type: "paperclip",
                },
                style: {
                  "font-size": "18px",
                },
              }),
            ]);
          },
          render: (h, param) => {
            if (param.row.attachment.type === "image") {
              return h("div", [
                h("Icon", {
                  props: {
                    type: "image",
                  },
                  style: {
                    cursor: "pointer",
                  },
                  on: {
                    click: () => {
                      this.openAttachmentModal(param.row);
                    },
                  },
                }),
              ]);
            } else if (param.row.attachment.type === "media") {
              return h("div", [
                h("Icon", {
                  props: {
                    type: this.getCommentMediaIcon(param.row),
                    size: "16",
                  },
                  style: {
                    cursor: "pointer",
                  },
                  on: {
                    click: () => {
                      this.openAttachmentModal(param.row);
                    },
                  },
                }),
              ]);
            } else return h("div", "");
          },
        },
        // Disabled 'link' col
        // {
        //   title: this.$t("comments.col.link"),
        //   key: "link",
        //   width: 100,
        //   render: (h, param) => {
        //     return h("div", [
        //       h("Icon", {
        //         props: {
        //           type: "link",
        //         },
        //         style: {
        //           cursor: "pointer",
        //         },
        //         on: {
        //           click: () => {
        //             let url = this.getSharingUrl(param.row);
        //             this.copyToClipBoard(url);
        //           },
        //         },
        //       }),
        //     ]);
        //   },
        // },
      ],
      showAttachmentModal: false,
      modalAttachmentHeader: "",
      modalAttachmentType: "",
      modalAttachmentSrc: "",
    };
  },
  methods: _.merge(Vuex.mapActions([]), {
    init() {
      this.getCommentList();
    },
    getCommentList() {
      this.commentCurMode = this.mode;
      this.commentPager.busy = true;
      axios
        .get("/user/tbavideo/get-comments", {
          params: {
            mode: this.commentCurMode,
            filter: this.commentFilter,
            size: this.commentPager.size,
          },
        })
        .then((data) => {
          if (data.status !== 200) return;
          let res = data.data;
          this.commentTableDataList = res.data; // Assign comment lists
          // Pagination
          this.commentLoadMoreUrl = res.links.next;
          this.commentPager.lastPage = res.meta.last_page;
          this.commentPager.total = res.meta.total;
          this.commentPager.pageIndex = 1;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.commentPager.busy = false;
        });
    },
    commentLoadMore(p) {
      this.commentPager.pageIndex = p;
      if (this.isPagerOutOfRange) return false;

      this.commentPager.busy = true;
      axios
        .get(this.commentLoadMoreUrl, {
          params: {
            mode: this.commentCurMode,
            filter: this.commentFilter,
            page: this.commentPager.pageIndex,
            size: this.commentPager.size,
          },
        })
        .then((data) => {
          if (data.status !== 200) return;
          let res = data.data;
          this.commentTableDataList = res.data; // Assign comment lists
        })
        .finally(() => {
          this.commentPager.busy = false;
        });
    },
    fmtSecondsToMMSS(seconds) {
      return TimeFormatterUtil.formatSecondsToMMSS(seconds);
    },
    formatDatetime(mysqlDatetime) {
      if (!mysqlDatetime) return "";
      return new Date(mysqlDatetime).toISOString().split("T")[0];
    },
    createFilterList(colIndex, commentData, key, subKey = null) {
      // Create a filter list for table
      // colIndex -> based on col index of a table
      this.commentTableColumns[colIndex].filters = []; // clear existing data
      // Iterate through a unique array to create iview filter
      this.commentTableColumns[colIndex].filters = _.map(
        _.uniqBy(commentData, (item) => {
          if (subKey) return item[key][subKey];
          return item[key];
        }),
        (v) => {
          if (subKey) return { label: v[key][subKey], value: v[key][subKey] };
          return { label: v[key], value: v[key] };
        }
      );
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
    openVideoFromUrl(url) {
      window.open(url, "_blank");
    },
    copyToClipBoard(content) {
      let _this = this;
      this.$copyText(content).then(
        function (e) {
          _this.$Message.info(_this.$t("comments.clipboard.success"));
        },
        function (e) {
          _this.$Message.error(_this.$t("comments.clipboard.error"));
        }
      );
    },
    getSharingUrl(commentData) {
      let start = commentData.time;
      let contentId = commentData.tbaId;
      let groupIds = commentData.groupId;
      let channelId = commentData.channelId;
      let memberChannel = 0;
      let url =
        document.location.origin +
        "/auth/login?to=" +
        encode64(
          "/Player?" +
            "contentId=" +
            contentId +
            "&groupIds=" +
            groupIds +
            "&channelId=" +
            channelId +
            "&start=" +
            start +
            "&memberChannel=" +
            memberChannel
        );
      return url;
    },
    getCommentMediaIcon(commentData) {
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
    getCommentText(commentData) {
      // Get comment text for the comment table row
      let text = !_.isEmpty(commentData.text)
        ? commentData.text.replace(/\n|\r\n/g, "<br>")
        : "";
      return text;
    },
    getCommentItemTextMobile(commentData) {
      // This will return HTML string
      // [name] [type tag] [text]
      let htmlStr = `
        <span style='font-style: italic'>
          ${commentData.user.name}
        </span>
        <span style='color: ${this.getCommentColor(commentData.isPositive)}'>
          [${this.getCommentLabel(commentData)}]
        </span>
        <span>
          ${this.getCommentText(commentData)}
        </span>
      `;
      return htmlStr;
    },
    openVideoFromComment(commentData) {
      // This method will open video from comment
      if (!commentData) return;
      let url = this.getSharingUrl(commentData);
      this.openVideoFromUrl(url);
    },
    openAttachmentModal(commentData) {
      // Add header
      this.modalAttachmentHeader =
        this.fmtSecondsToMMSS(commentData.time) + " " + commentData.text;
      // Determine type and source
      this.modalAttachmentType = commentData.attachment.type;
      this.modalAttachmentSrc = commentData.attachment.src;
      this.showAttachmentModal = true;
    },
    closeAttachmentModal() {
      this.modalAttachmentHeader = "";
      this.modalAttachmentType = "";
      this.modalAttachmentSrc = "";
      this.showAttachmentModal = false;
    },
  }),
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss">
.comment-list-container {
  .comment-search-container {
    width: 300px;

    .search-btn {
      font-size: 30px;
      cursor: pointer;
      padding-left: 10px;
    }

    .search-btn:hover {
      color: #acd6ff;
    }
  }

  .comment-content-container {
    /*外層table的border*/
    .ivu-table-wrapper {
      border: none;
      border-bottom: 1px solid #ffffff;
    }

    /*底色*/
    .ivu-table td {
      background-color: #182328;
      color: #fff;
    }

    /*每行的基本樣式*/
    .ivu-table-row td {
      color: #fff;
      border: none;
    }

    /*頭部th*/
    .ivu-table-header th {
      color: #fff;
      font-weight: bold;
      background-color: #212c31;
      border: 1px solid;
    }

    /*偶數行*/
    .ivu-table-stripe-even td {
      background-color: #434343 !important;
      border: 1px solid;
    }

    /*奇數行*/
    .ivu-table-stripe-odd td {
      background-color: #282828 !important;
    }

    /*選中某一行高亮*/
    .ivu-table-row-highlight td {
      background-color: #434343 !important;
    }

    /*浮在某行*/
    .ivu-table-row-hover td {
      background-color: #434343 !important;
    }

    .ivu-table:before {
      background-color: black;
    }

    .ivu-table:after {
      background-color: black;
    }

    .ivu-table-row td {
      border: 1px solid;
    }

    .ivu-modal-content {
      background-color: #182328;
    }

    .ivu-spin-fix {
      opacity: 0.8;
      background-color: #434343;
    }

    .comment-content-mobile-container {
      color: #ffffff;

      .comment-content-mobile-item-striped {
        background-color: #2e2e2e;
      }

      .comment-content-mobile-item {
        font-size: 0.8rem;
        padding: 5px;

        .comment-content-mobile-item-text-wrapper {
          padding: 0 3px 0 3px;
          white-space: normal;
          overflow: hidden;
          text-overflow: ellipsis;

          .comment-content-mobile-item-text-title {
            color: #1fa4e5;
            font-weight: bold;
            cursor: pointer;
          }
        }
      }
    }
  }

  .pager-center {
    padding-top: 30px;
    text-align: center;
  }
}

.attachment-modal {
  .photo-image-container {
    height: 250px;
    width: auto;
    text-align: center;

    .photo-image {
      max-width: 250px;
      max-height: 250px;
    }
  }
}
</style>
