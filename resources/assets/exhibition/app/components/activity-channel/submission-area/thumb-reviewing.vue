<template>
  <div class="submission-area-reviewing-container">
    <Row type="flex" justify="center" align="middle" class="submission-area">
      <!-- Submitted Video List -->
      <Col span="24" v-if="submittedVideoDataList.length > 0">
        <Table
          border
          size="large"
          :columns="submittedVideoDataCols"
          :data="submittedVideoDataList"
        >
        </Table>
      </Col>
      <!-- Searching -->
      <Col class="spin-col" span="24" v-else-if="isSearching">
        <Spin fix>
          <Icon type="load-c" size="large" class="spin-icon-load"></Icon>
          <h4 style="color: #ffffff">
            {{ $t("activityChannel.searching") }}
          </h4>
        </Spin>
      </Col>
      <!-- Reviewing Stage Title -->
      <Col span="24" type="flex" justify="center" v-else>
        <Card
          style="
            background-color: transparent;
            text-align: center;
            border: none;
          "
        >
          <p>
            <Icon
              type="ios-compose"
              style="font-size: 3rem; color: #ffefd5"
            ></Icon>
          </p>
          <p class="text-annoucement">
            {{ $t("activityChannel.msg.review") }}
          </p>
        </Card>
      </Col>
    </Row>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";
import CpntThumbContent from "../../thumb-content.vue";

export default {
  name: "reviewing",
  components: {
    "cpnt-thumb-content": CpntThumbContent,
  },
  props: ["group", "categoricalChoices", "subjectChoices"],
  emits: [],
  computed: _.merge(
    Vuex.mapState(["path", "user"]),
    Vuex.mapGetters(["logined"]),
    {
      groupId() {
        return this.group.groupId;
      },
      userId() {
        return this.$store.state.user.id;
      },
      scoreVideoContentIdList() {
        return _.map(this.scoreDataList, "tba_id");
      },
      submittedVideoContentIdList() {
        return _.map(this.submittedVideoContentDataList, "content_id");
      },
    }
  ),
  watch: {
    submittedVideoContentIdList(v) {
      this.getSubmittedVideoDataList();
    },
  },
  data() {
    return {
      // Channel properties
      channelId: this.$route.params.channelId,
      // Reviewing properties
      scoreDataList: [],
      submittedVideoContentDataList: [],
      submittedVideoDataList: [], // for table
      submittedVideoDataCols: [
        {
          title: this.$t("activityChannel.reviewingComponent.col.index"),
          type: "index",
          align: "center",
          width: 100,
        },
        {
          title: this.$t("activityChannel.reviewingComponent.col.video"),
          key: "videoThumbnail",
          align: "center",
          render: (h, param) => {
            return h("div", [
              h("img", {
                attrs: {
                  src: param.row.videoThumbnail,
                },
                style: {
                  width: "130px",
                  height: "100px",
                  padding: "5px",
                  cursor: "pointer",
                },
                on: {
                  click: () => {
                    this.goToContentPage(param.row.videoData);
                  },
                },
              }),
            ]);
          },
        },
        {
          title: this.$t("activityChannel.reviewingComponent.col.type"),
          key: "categoryId",
          sortable: true,
          ellipsis: true,
          align: "center",
          render: (h, param) => {
            return h(
              "div",
              {
                style: {},
              },
              this.getCategoryNameFromId(param.row.categoryId)
            );
          },
        },
        {
          title: this.$t("activityChannel.reviewingComponent.col.class"),
          key: "subjectId",
          sortable: true,
          ellipsis: true,
          align: "center",
          render: (h, param) => {
            return h(
              "div",
              {
                style: {},
              },
              this.getSubjectNameFromId(param.row.subjectId)
            );
          },
        },
        {
          title: this.$t("activityChannel.reviewingComponent.col.reviewStatus"),
          key: "isReviewed",
          sortable: true,
          align: "center",
          width: 200,
          render: (h, param) => {
            if (param.row.isReviewed) {
              return h("div", [
                h(
                  "Tag",
                  {
                    props: {
                      color: "green",
                    },
                  },
                  this.$t("activityChannel.reviewingComponent.cell.reviewed")
                ),
              ]);
            } else {
              return h("div", [
                h(
                  "Tag",
                  {
                    props: {
                      color: "yellow",
                    },
                  },
                  this.$t("activityChannel.reviewingComponent.cell.pending")
                ),
              ]);
            }
          },
        },
      ],
      // Loading states
      isSearching: false,
    };
  },
  methods: {
    init() {
      this.getScoreDataList();
    },
    getScoreDataList() {
      let url = "/score/" + this.channelId;
      axios
        .get(url)
        .then((response) => {
          if (response.status !== 200) return;
          this.scoreDataList = [];
          this.scoreDataList = response.data;
          // Create presentation after fetching score data
          this.getSubmittedVideoContentDataList();
        })
        .catch((e) => {
          console.log(e.error);
        });
    },
    getSubmittedVideoContentDataList() {
      // Get data from group channel content
      let url = "/tbas/getVideo/group/" + this.channelId;
      axios
        .get(url, {
          params: {},
        })
        .then((response) => {
          if (response.status !== 200) return;
          this.submittedVideoContentDataList = response.data;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {});
    },
    getSubmittedVideoDataList() {
      // Get tba data for presentation
      let url = "/exhibition/tbavideo/get-filter-movie";
      this.isSearching = true;
      axios
        .get(url, {
          params: {
            size: 0,
            tba_ids: this.submittedVideoContentIdList,
          },
        })
        .then((response) => {
          if (response.status !== 200) return;
          let result = response.data.data;
          // Create submittedVideoDataList for tabular presentation
          this.submittedVideoDataList = [];
          this.submittedVideoDataList = _.map(
            result,
            this.createSubmittedVideoData
          );
        })
        .catch((e) => {
          console.log(e.error);
        })
        .finally(() => {
          this.isSearching = false;
        });
    },
    createSubmittedVideoData(videoData) {
      let submittedVideoContentData = _.find(
        this.submittedVideoContentDataList,
        ["content_id", videoData.id]
      );
      // Use this structure and keys for the table
      let submittedVideoData = {
        contentId: videoData.id,
        videoName: videoData.name,
        videoThumbnail:
          this.path.tba + videoData.id + "/" + videoData.thumbnail,
        videoData: videoData,
        categoryId: submittedVideoContentData.ratings_id,
        subjectId: submittedVideoContentData.group_subject_fields_id,
        isReviewed: this.checkReviewStatus(videoData.id),
      };
      return submittedVideoData;
    },
    checkReviewStatus(contentId) {
      return _.includes(this.scoreVideoContentIdList, contentId);
    },
    getCategoryNameFromId(categoryId) {
      return _.find(this.categoricalChoices, ["id", categoryId]).name;
    },
    getSubjectNameFromId(subjectId) {
      return _.find(this.subjectChoices, ["id", subjectId]).subject;
    },
    goToContentPage(videoData) {
      let groupId = this.groupId;
      let channelId = this.channelId;
      let routeData = this.$router.resolve({
        name: "content",
        params: { contentId: videoData.id },
        query: { groupIds: groupId, channelId: channelId },
      });
      window.open(routeData.href, "_blank").focus();
    },
  },
  created() {
    this.init();
  },
};
</script>

<style lang="scss">
.submission-area-reviewing-container {
  h1,
  h2 {
    color: #ffffff;
  }

  .text-annoucement {
    font-size: 1rem;
    color: #ffffff;
  }

  .submission-area {
    padding: 10px;
    background-color: #6d6d6d;
    text-align: center;
    text-align: -webkit-center;
    border-radius: 10px;

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

    .spin-col {
      height: 150px;
      position: relative;
      padding: 10px;
      border: 1px solid #eee;
      border-radius: 10px;
    }

    .spin-icon-load {
      font-size: 1.5rem;
      color: #ffffff;
      animation: ani-demo-spin 1s linear infinite;
    }

    @keyframes ani-demo-spin {
      from {
        transform: rotate(0deg);
      }
      50% {
        transform: rotate(180deg);
      }
      to {
        transform: rotate(360deg);
      }
    }

    .ivu-spin-fix {
      background-color: #6d6d6d;
    }
  }
}
</style>
