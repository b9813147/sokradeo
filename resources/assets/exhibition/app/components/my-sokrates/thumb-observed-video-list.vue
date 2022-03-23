<template>
  <div class="observed-video-list">
    <!-- Favorite Video -->
    <div
      class="observed-video-list-block"
      v-if="observedVideosDataList.length > 0"
    >
      <Row>
        <!-- Content Grid -->
        <Col
          :xs="24"
          :sm="12"
          :md="8"
          :lg="6"
          v-for="(observedVideoData, i) in observedVideosDataList"
          :key="i"
        >
          <cpnt-thumb-content
            :item="observedVideoData"
            :isMobile="isMobileBrowser"
            @execute="exeContentFromObsrv(observedVideoData)"
          >
          </cpnt-thumb-content>
        </Col>
        <!-- Pager -->
        <Col span="24">
          <Page
            class="pager-center"
            :page-size="observedVideosPager.size"
            :total="observedVideosPager.total"
            :current="observedVideosPager.pageIndex"
            @on-change="observedVideosLoadMore"
          ></Page>
        </Col>
      </Row>
    </div>
    <!-- Loading -->
    <div class="noti-loading-container" v-show="observedVideosPager.busy">
      <cpnt-thumb-loading></cpnt-thumb-loading>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

import CpntThumbContent from "../thumb-content.vue";
import CpntThumbLoading from "../thumb-loading.vue";

export default {
  name: "observed-video-list",
  components: {
    "cpnt-thumb-content": CpntThumbContent,
    "cpnt-thumb-loading": CpntThumbLoading,
  },
  computed: _.merge(
    Vuex.mapState(["path", "user", "isMobileBrowser"]),
    Vuex.mapGetters(["logined"])
  ),
  data() {
    return {
      observedVideosDataList: [],
      observedVideosMoreUrl: null,
      observedVideosPager: {
        busy: false,
        pageIndex: 1,
        lastPage: 0,
        total: 0,
        size: 100,
      },
    };
  },
  methods: {
    init() {
      this.getObservedVideoList();
    },
    getObservedVideoList() {
      let _this = this;
      let url = "/exhibition/tbavideo/get-my-observed-movies";

      _this.observedVideosPager.busy = true;
      axios
        .get(url, {
          params: {
            size: _this.observedVideosPager.size,
          },
        })
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(res.message);

          // Assign data list
          let data = res.data.data;
          _this.observedVideosDataList = data.data;

          // Pagination
          _this.observedVideosMoreUrl = data.next_page_url;
          _this.observedVideosPager.lastPage = data.last_page;
          _this.observedVideosPager.total = data.total;
          _this.observedVideosPager.pageIndex = 1;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          _this.observedVideosPager.busy = false;
        });
    },
    observedVideosLoadMore(p) {
      let _this = this;
      let url = _this.observedVideosMoreUrl;
      _this.observedVideosPager.pageIndex = p;

      if (
        _this.observedVideosPager.pageIndex > _this.observedVideosPager.lastPage
      )
        return;

      axios
        .get(url, {
          params: {
            page: _this.observedVideosPager.pageIndex,
            size: _this.observedVideosPager.size,
          },
        })
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(data.statusText);
          let data = res.data.data;
          _this.observedVideosDataList = data.data;
        });
    },
    exeContentFromObsrv(observedVideoData) {
      // 1.) Get groupId from comment data (if null, find it from group_channels)
      // 2.) Then use that groupId to find contentId in group_channels
      let contentId = observedVideoData.id;
      let groupId = this._getGroupId(observedVideoData);
      let channelId = this._getChannelId(observedVideoData, groupId);
      if (_.includes([contentId, channelId, groupId], null)) return;
      this.$router.push({
        name: "content",
        params: { contentId: contentId },
        query: {
          groupIds: _.join([groupId], ","),
          channelId: _.join([channelId], ","),
        },
      });
    },
    _getGroupId(observedVideoData) {
      if (!observedVideoData) return null;
      let groupId = observedVideoData.tba_comment
        ? observedVideoData.tba_comment[0].group_id
        : null;

      if (!groupId)
        groupId = observedVideoData.group_channels
          ? observedVideoData.group_channels[0].group_id
          : null;

      return groupId;
    },
    _getChannelId(observedVideoData, groupId) {
      if (!groupId) return null;
      let channelData = _.find(observedVideoData.group_channels, [
        "group_id",
        groupId,
      ]);
      return channelData ? channelData.id : null;
    },
  },
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.observed-video-list {
  .observed-video-list-block {
    .pager-center {
      padding-top: 30px;
      text-align: center;
    }
  }
}
</style>
