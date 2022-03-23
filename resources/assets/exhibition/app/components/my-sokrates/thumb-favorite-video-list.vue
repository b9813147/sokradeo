<template>
  <div class="favorite-video-list">
    <!-- Favorite Video -->
    <div
      class="favorite-video-list-block"
      v-if="favoriteVideosDataList.length > 0"
    >
      <Row>
        <!-- Content Grid -->
        <Col
          :xs="24"
          :sm="12"
          :md="8"
          :lg="6"
          v-for="(favoriteVideosData, i) in favoriteVideosDataList"
          :key="i"
        >
          <cpnt-thumb-content
            :item="favoriteVideosData.tba"
            :isMobile="isMobileBrowser"
            @execute="exeContentFromFavs(favoriteVideosData)"
          >
          </cpnt-thumb-content>
        </Col>
        <!-- Pager -->
        <Col span="24">
          <Page
            class="pager-center"
            :page-size="favoriteVideosPager.size"
            :total="favoriteVideosPager.total"
            :current="favoriteVideosPager.pageIndex"
            @on-change="favoriteVideosLoadMore"
          ></Page>
        </Col>
      </Row>
    </div>
    <!-- Loading -->
    <div class="noti-loading-container" v-show="favoriteVideosPager.busy">
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
  name: "favorite-video-list",
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
      favoriteVideosDataList: [],
      favoriteVideosMoreUrl: null,
      favoriteVideosPager: {
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
      this.getFavoriteVideoList();
    },
    getFavoriteVideoList() {
      let _this = this;
      let url = "/favorite";

      _this.favoriteVideosPager.busy = true;
      axios
        .get(url, {
          params: {
            size: _this.favoriteVideosPager.size,
          },
        })
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(res.message);

          // Assign data list
          let data = res.data.data;
          _this.favoriteVideosDataList = data.data;

          // Pagination
          _this.favoriteVideosMoreUrl = data.next_page_url;
          _this.favoriteVideosPager.lastPage = data.last_page;
          _this.favoriteVideosPager.total = data.total;
          _this.favoriteVideosPager.pageIndex = 1;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          _this.favoriteVideosPager.busy = false;
        });
    },
    favoriteVideosLoadMore(p) {
      let _this = this;
      let url = _this.favoriteVideosMoreUrl;
      _this.favoriteVideosPager.pageIndex = p;

      if (
        _this.favoriteVideosPager.pageIndex > _this.favoriteVideosPager.lastPage
      )
        return;

      axios
        .get(url, {
          params: {
            page: _this.favoriteVideosPager.pageIndex,
            size: _this.favoriteVideosPager.size,
          },
        })
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(data.statusText);
          let data = res.data.data;
          _this.favoriteVideosDataList = data.data;
        });
    },
    exeContentFromFavs(favoriteVideosData) {
      let groupIds = _.join([favoriteVideosData.group_id], ",");
      let channelId = _.join([favoriteVideosData.channel_id], ",");
      let contentId = favoriteVideosData.tba_id;

      if (_.includes([contentId, channelId, groupIds], null)) return;

      this.$router.push({
        name: "content",
        params: { contentId: contentId },
        query: {
          groupIds: groupIds,
          channelId: channelId,
        },
      });
    },
  },
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.favorite-video-list {
  .favorite-video-list-block {
    .pager-center {
      padding-top: 30px;
      text-align: center;
    }
  }
}
</style>
