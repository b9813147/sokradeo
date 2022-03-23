<template>
  <div
    class="eligible-videos-list-container"
    v-if="eventState.requirement && !eventState.deadline.isOver"
  >
    <!-- Header -->
    <Row style="padding: 5px">
      <!-- Details -->
      <Col span="20">
        <!-- Title -->
        <h1>
          {{ $t("activityChannel.myVideos") }}
          ({{ myEligibleVideos.list.length }})
        </h1>
        <!-- Requirements -->
        <div v-if="requirements && requirements.length > 0">
          <p>{{ $t("activityChannel.eligibilityRequirement.title") }} :</p>
          <p v-for="(requirementMsg, key) in requirements" :key="key">
            - {{ requirementMsg }}
          </p>
        </div>
      </Col>
      <!-- Search bar -->
      <Col span="4" v-if="myEligibleVideos.list.length > 0">
        <div class="searchbar-container">
          <Input
            class="searchbar"
            v-model="videoSearchQuery"
            icon="ios-search"
            :placeholder="$t('activityChannel.searchVideo')"
          >
          </Input>
        </div>
      </Col>
    </Row>
    <!-- Video List -->
    <Row v-if="myEligibleVideosChunk.curChunkVideoList.length > 0">
      <Col
        class="my-video-list"
        span="6"
        v-for="(item, index) in myEligibleVideosChunk.curChunkVideoList"
        :key="index"
      >
        <div class="my-video-card">
          <cpnt-thumb-content :item="item"></cpnt-thumb-content>
          <div class="my-video-card-btn">
            <Button
              type="primary"
              @click="selectVideo(item)"
              :disabled="!isVideoSelectable(item)"
              long
            >
              <Icon type="plus-round"></Icon>
              <span class="add-text">
                {{ $t("activityChannel.selectVideo") }}
              </span>
            </Button>
          </div>
        </div>
      </Col>
    </Row>
    <!-- Pager -->
    <Row
      v-if="myEligibleVideosChunk.chunkList.length > 0"
      type="flex"
      justify="center"
    >
      <Col
        span="24"
        class="pager-container"
        v-if="myEligibleVideosChunk.chunkTotal > 1"
      >
        <Page
          :current="myEligibleVideosChunk.curChunkPage"
          :total="myEligibleVideosChunk.chunkTotal"
          :page-size="1"
          @on-change="selectVideoChunk"
        ></Page>
      </Col>
    </Row>
    <!-- Loading Icon -->
    <Row v-show="isLoading">
      <Col class="demo-spin-col" span="24">
        <Spin fix>
          <Icon type="load-c" size="30" class="demo-spin-icon-load"></Icon>
        </Spin>
      </Col>
    </Row>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

import CpntThumbContent from "../../../app/components/thumb-content.vue";

export default {
  name: "eligible-video-list",
  components: {
    "cpnt-thumb-content": CpntThumbContent,
  },
  props: ["group", "eventState", "allowedSelection", "disallowedContentIds"],
  emits: ["select-video-from-list", "instructional-hint-update"],
  computed: _.merge(
    Vuex.mapState(["path", "user"]),
    Vuex.mapGetters(["logined"]),
    {
      requirements() {
        let submissionRequirementData = this.eventState.requirement;
        if (!submissionRequirementData) return;

        let requirementsList = [];
        _.forEach(submissionRequirementData, (value, key) => {
          if (!value) return; // continue
          let msg = "";
          if (key === "hasMaterial")
            msg = this.$t(
              "activityChannel.eligibilityRequirement.msg.hasMaterial"
            );
          if (key === "hasTPC")
            msg = this.$t("activityChannel.eligibilityRequirement.msg.hasTPC");
          if (key === "isDoubleGreen")
            msg = this.$t(
              "activityChannel.eligibilityRequirement.msg.isDoubleGreen"
            );
          requirementsList.push(msg);
        });
        return requirementsList;
      },
      instructionalHint() {
        // Give away a hint based on different states
        let msg = "- ";
        if (
          this.myEligibleVideos.list.length === 0 &&
          this.myVideos.list.length === 0
        )
          msg += this.$t("activityChannel.instruction.option3");
        else if (
          this.myEligibleVideos.list.length === 0 &&
          this.myVideos.list.length > 0
        )
          msg += this.$t("activityChannel.instruction.option2");
        else msg += this.$t("activityChannel.instruction.option1");
        return msg;
      },
    }
  ),
  watch: {
    instructionalHint(v) {
      this.$emit("instructional-hint-update", v);
    },
    // Create a filtered video list based on a given video name keyword
    // Search delay = 0.3 sec
    videoSearchQuery(v) {
      setTimeout(() => {
        this.myEligibleVideos.filtered = _.filter(
          this.myEligibleVideos.list,
          (video) => {
            if (v === "") return true;
            return _.includes(video.name, v);
          }
        );
      }, 300);
    },
    // Update chuncked video list when eligible video list is updated
    "myEligibleVideos.filtered"(v) {
      let videoListChunk = _.chunk(v, this.myEligibleVideosChunk.chunkSize);
      this.myEligibleVideosChunk.chunkList = videoListChunk;
      this.myEligibleVideosChunk.chunkTotal = videoListChunk.length;
      // This has to check for empty list since it is used for rendering
      this.myEligibleVideosChunk.curChunkVideoList =
        videoListChunk.length > 0
          ? videoListChunk[this.myEligibleVideosChunk.curChunkPage - 1]
          : [];
    },
  },
  data() {
    return {
      // Videos lists
      myVideos: {
        list: [],
      },
      myEligibleVideos: {
        list: [], // original (unfiltered)
        filtered: [],
      },
      // Video Chunk
      // Desc: this is used for presentation with <Page>
      myEligibleVideosChunk: {
        chunkList: [],
        chunkTotal: 0,
        curChunkPage: 1,
        curChunkVideoList: [],
        chunkSize: 20, // 20 videos per page
      },
      // Pagination
      loadMoreUrl: null,
      pager: {
        pageIndex: 1,
        lastPage: 0,
        total: 0,
        size: 100,
      },
      // Video Search
      videoSearchQuery: "",
      // Loading states
      isLoading: false,
    };
  },
  methods: {
    init() {
      this.getMyVideos();
    },
    getMyVideos() {
      // Get all movies (pagination considered)
      let _this = this;
      let url = "/exhibition/tbavideo/get-my-movies";

      axios
        .get(url, {
          params: {
            size: _this.pager.size,
          },
        })
        .then((response) => {
          if (!response.data.status) return;
          let result = response.data.data.list;

          // Add my videos and eligible videos
          _this.createVideoLists(result.data);

          // Assign pagination data and LoadMore data
          _this.pager.lastPage = result.last_page;
          _this.pager.total = result.total;
          _this.pager.pageIndex = 1;

          _this.loadMoreUrl = result.next_page_url;
          if (_this.loadMoreUrl) this.loadMore();
        })
        .catch((e) => {
          console.log(e);
        });
    },
    createVideoLists(myVideos) {
      // This method add videos to MyVideos and My Eligible Videos
      let _this = this;

      // Iterate throgh each video
      _.forEach(myVideos, function (videoData) {
        // My Videos
        _this.myVideos.list.push(videoData);

        // My Eligible Videos
        if (_this._checkVideoEligibility(videoData)) {
          _this.myEligibleVideos.list.push(videoData);
          _this.myEligibleVideos.filtered.push(videoData);
        }
      });
    },
    _checkVideoEligibility(videoData) {
      // Based on eventStage.submission.requirement
      let submissionRequirementData = this.eventState.requirement;
      let materialRequired = submissionRequirementData.hasMaterial;
      let TPCRequired = submissionRequirementData.hasTPC;
      let doubleGreenRequired = submissionRequirementData.isDoubleGreen;

      // Check annexes
      let curVideoAnnexes = _.map(videoData.tba_annexs, "type"); // ["LessonPlan", "Material"]
      if (materialRequired && !curVideoAnnexes.includes("Material"))
        return false;
      if (TPCRequired && !curVideoAnnexes.includes("LessonPlan")) return false;

      // Check double-green
      if (doubleGreenRequired && videoData.double_green_light_status !== 1)
        return false;

      // return true if all requirements are met
      return true;
    },
    loadMore() {
      let _this = this;
      let url = "/exhibition/tbavideo/get-my-movies";

      _this.pager.pageIndex++;
      if (_this.pager.pageIndex > _this.pager.lastPage) return false;

      _this.isLoading = true;
      axios
        .get(url, {
          params: {
            page: _this.pager.pageIndex,
            size: _this.pager.size,
          },
        })
        .then((response) => {
          let result = response.data.data.list;

          // Add my videos, eligible videos
          _this.createVideoLists(result.data);

          // Assign pagination data and LoadMore data
          _this.pager.lastPage = result.last_page;
          _this.pager.total = result.total;

          _this.loadMoreUrl = result.next_page_url;
          if (_this.loadMoreUrl) this.loadMore();
        })
        .finally(() => {
          _this.isLoading = false;
        });
    },
    selectVideoChunk(chunkPage) {
      this.myEligibleVideosChunk.curChunkPage = chunkPage;
      this.myEligibleVideosChunk.curChunkVideoList =
        this.myEligibleVideosChunk.chunkList[chunkPage - 1];
    },
    isVideoSelectable(videoData) {
      let isSelectable = true;
      if (!this.allowedSelection) isSelectable = false;
      if (this.disallowedContentIds.includes(videoData.id))
        isSelectable = false;
      return isSelectable;
    },
    selectVideo(videoData) {
      this.$emit("select-video-from-list", videoData);
    },
  },
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.eligible-videos-list-container {
  h1,
  h2 {
    color: #ffffff;
  }

  .searchbar-container {
    padding: 10px 10px 0 0;
    text-align: left;

    .searchbar {
      width: 100%;
    }
  }

  .my-video-list {
    padding-top: 5px;
  }

  .my-video-card-btn {
    text-align: center;
    padding: 10px;
  }

  .pager-container {
    text-align: center;
    padding: 10px;
  }
}
</style>
