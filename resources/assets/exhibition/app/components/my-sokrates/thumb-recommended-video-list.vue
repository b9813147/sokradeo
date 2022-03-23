<template>
  <div class="rec-video-list">
    <!-- Recommended Videos -->
    <div class="rec-video-list-block" v-if="recVideosDataList.length > 0">
      <Row>
        <!-- Content Grid -->
        <Col
          :xs="24"
          :sm="12"
          :md="8"
          :lg="6"
          v-for="(recVideoData, i) in recVideosDataList"
          :key="i"
        >
          <cpnt-thumb-content
            :item="recVideoData.tba"
            :isMobile="isMobileBrowser"
            @execute="exeContentFromRec(recVideoData.tba)"
          >
          </cpnt-thumb-content>
        </Col>
        <!-- Pager -->
        <Col span="24">
          <Page
            class="pager-center"
            :page-size="recVideosPager.size"
            :total="recVideosPager.total"
            :current="recVideosPager.pageIndex"
            @on-change="recVideosLoadMore"
          ></Page>
        </Col>
      </Row>
    </div>
    <!-- Loading -->
    <div class="noti-loading-container" v-show="recVideosPager.busy">
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
  name: "rec-video-list",
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
      recVideosDataList: [],
      recVideosMoreUrl: null,
      recVideosPager: {
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
      this.getRecVideoList();
    },
    getRecVideoList() {
      let _this = this;
      let url = "/exhibition/tbavideo/get-recommended-movies";

      _this.recVideosPager.busy = true;
      axios
        .get(url, {
          params: {
            size: _this.recVideosPager.size,
          },
        })
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(res.message);

          let localeId = 65
          switch (this.$i18n.locale) {
            case 'tw':
              localeId = 40
              break
            case 'cn':
              localeId = 37
              break
            case 'en':
              localeId = 65
              break
          }

          // Assign data list
           let data = res.data.data;
          // _this.recVideosDataList = data.data;
          data.data.forEach(function (item) {
            if(item.locales_id === localeId) {
              _this.recVideosDataList.push(item)
            }
          })

          // Pagination
          _this.recVideosMoreUrl = data.next_page_url;
          _this.recVideosPager.lastPage = data.last_page;
          _this.recVideosPager.total = data.total;
          _this.recVideosPager.pageIndex = 1;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          _this.recVideosPager.busy = false;
        });
    },
    recVideosLoadMore(p) {
      let _this = this;
      let url = _this.recVideosMoreUrl;
      _this.recVideosPager.pageIndex = p;

      if (_this.recVideosPager.pageIndex > _this.recVideosPager.lastPage)
        return;

      axios
        .get(url, {
          params: {
            page: _this.recVideosPager.pageIndex,
            size: _this.recVideosPager.size,
          },
        })
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(data.statusText);
          let data = res.data.data;
          _this.recVideosDataList = data.data;
        });
    },
    exeContentFromRec(recVideoData) {
      // As same as home.vue
      let groupIds = _.join(
        _.uniq(_.map(recVideoData.group_channels, "group_id")),
        ","
      );
      let channelId = _.join(
        _.uniq(_.map(recVideoData.group_channels, "id")),
        ","
      );
      this.$router.push({
        name: "content",
        params: { contentId: recVideoData.id },
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
.rec-video-list {
  .rec-video-list-block {
    .pager-center {
      padding-top: 30px;
      text-align: center;
    }
  }
}
</style>
