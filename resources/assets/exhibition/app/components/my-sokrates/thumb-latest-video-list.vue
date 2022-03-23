<template>
  <div class="latest-video-list">
    <!-- Latest Videos -->
    <div class="latest-video-list-block" v-if="latestVideosDataList.length > 0">
      <Row>
        <!-- Content Grid -->
        <Col
          :xs="24"
          :sm="12"
          :md="8"
          :lg="6"
          v-for="(latestVideoData, i) in latestVideosDataList"
          :key="i"
        >
          <cpnt-thumb-content
            :item="latestVideoData"
            :isMobile="isMobileBrowser"
            @execute="exeContentFromLatest(latestVideoData)"
          >
          </cpnt-thumb-content>
        </Col>
      </Row>
    </div>
    <!-- Loading -->
    <div class="noti-loading-container" v-show="loading">
      <cpnt-thumb-loading></cpnt-thumb-loading>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

import MySokratesService from "../../../../../services/my-sokrates";

import CpntThumbContent from "../thumb-content.vue";
import CpntThumbLoading from "../thumb-loading.vue";

export default {
  name: "latest-video-list",
  props: {
    limit: {
      type: Number,
      required: true,
    },
  },
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
      service: MySokratesService,
      loading: false,
      latestVideosDataList: [],
      latestVideosMoreUrl: null,
    };
  },
  methods: {
    init() {
      this.getLatestVideoList();
    },
    async getLatestVideoList() {
      let _this = this;

      _this.loading = true;
      await this.service
        .getLatestVideoList(_this.limit)
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(res.message);

          // Assign data list
          let data = res.data.data;
          _this.latestVideosDataList = data;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          _this.loading = false;
        });
    },
    exeContentFromLatest(latestVideoData) {
      // As same as home.vue
      let groupIds = _.join(
        _.uniq(_.map(latestVideoData.group_channels, "group_id")),
        ","
      );
      let channelId = _.join(
        _.uniq(_.map(latestVideoData.group_channels, "id")),
        ","
      );
      this.$router.push({
        name: "content",
        params: { contentId: latestVideoData.id },
        query: { groupIds: groupIds, channelId: channelId },
      });
    },
  },
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.latest-video-list {
  .latest-video-list-block {
    .pager-center {
      padding-top: 30px;
      text-align: center;
    }
  }
}
</style>
