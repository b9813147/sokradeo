<template>
  <div class="general-obsrv-video-list">
    <!-- General Observation Videos -->
    <div
      class="general-obsrv-video-list-block"
      v-if="generalObsrvVideoDataList.length > 0"
    >
      <Row>
        <!-- Content Grid -->
        <Col
          :xs="24"
          :sm="12"
          :md="8"
          :lg="6"
          v-for="(generalObsrvVideoData, i) in generalObsrvVideoDataList"
          :key="i"
        >
          <cpnt-thumb-content
            v-if="isExecutable(generalObsrvVideoData)"
            :item="generalObsrvVideoData"
            :isMobile="isMobileBrowser"
            @execute="exeContent(generalObsrvVideoData)"
          >
          </cpnt-thumb-content>
        </Col>
        <!-- Pager -->
        <Col span="24">
          <Page
            class="pager-center"
            :page-size="generalObsrvVideoPager.size"
            :total="generalObsrvVideoPager.total"
            :current="generalObsrvVideoPager.pageIndex"
            @on-change="generalObsrvVideoLoadMore"
          ></Page>
        </Col>
      </Row>
    </div>
    <!-- Loading -->
    <div class="noti-loading-container" v-show="generalObsrvVideoPager.busy">
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
  name: "general-obsrv-video-list",
  props: {
    tbaIdList: {
      type: Array,
      default: () => [],
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
      generalObsrvVideoDataList: [],
      generalObsrvVideoMoreUrl: null,
      generalObsrvVideoPager: {
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
      this.getGeneralObsrvVideoList();
    },
    getGeneralObsrvVideoList() {
      let _this = this;
      let url = "/exhibition/tbavideo/get-filter-movie/";

      _this.generalObsrvVideoPager.busy = true;
      axios
        .get(url, {
          params: {
            size: _this.generalObsrvVideoPager.size,
            tba_ids: _this.tbaIdList,
          },
        })
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(res.message);

          // Assign data list
          let data = res.data.data;
          _this.generalObsrvVideoDataList = data.data;

          // Pagination
          _this.generalObsrvVideoMoreUrl = data.next_page_url;
          _this.generalObsrvVideoPager.lastPage = data.last_page;
          _this.generalObsrvVideoPager.total = data.total;
          _this.generalObsrvVideoPager.pageIndex = 1;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          _this.generalObsrvVideoPager.busy = false;
        });
    },
    generalObsrvVideoLoadMore(p) {
      let _this = this;
      let url = _this.generalObsrvVideoMoreUrl;
      _this.generalObsrvVideoPager.pageIndex = p;

      if (
        _this.generalObsrvVideoPager.pageIndex >
        _this.generalObsrvVideoPager.lastPage
      )
        return;

      axios
        .get(url, {
          params: {
            page: _this.generalObsrvVideoPager.pageIndex,
            size: _this.generalObsrvVideoPager.size,
          },
        })
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(data.statusText);
          let data = res.data.data;
          _this.generalObsrvVideoDataList = data.data;
        });
    },
    exeContent(generalObsrvVideoData) {
      let groupIds = _.join(
        _.uniq(_.map(generalObsrvVideoData.group_channels, "group_id")),
        ","
      );
      let channelId = _.join(
        _.uniq(_.map(generalObsrvVideoData.group_channels, "id")),
        ","
      );
      let contentId = generalObsrvVideoData.id;

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
    isExecutable(generalObsrvVideoData) {
      return (
        generalObsrvVideoData &&
        generalObsrvVideoData.id &&
        Array.isArray(generalObsrvVideoData.group_channels) &&
        generalObsrvVideoData.group_channels.length > 0
      );
    },
  },
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.general-obsrv-video-list {
  .general-obsrv-video-list-block {
    .pager-center {
      padding-top: 30px;
      text-align: center;
    }
  }
}
</style>
