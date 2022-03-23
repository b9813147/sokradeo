<template>
  <div class="watch-history-list">
    <!-- Watch History -->
    <div
      class="watch-history-list-block"
      v-if="watchHistoryDataList.length > 0"
    >
      <!-- Optional Menu(s) -->
      <Row type="flex" justify="end">
        <Col class="clear-btn-container">
          <Button
            type="error"
            icon="trash-a"
            size="small"
            @click="displayDelModal"
          >
            {{ $t("clearWatchHistory").toUpperCase() }}
          </Button>
        </Col>
      </Row>
      <Row>
        <!-- Content Grid -->
        <Col
          :xs="24"
          :sm="12"
          :md="8"
          :lg="6"
          v-for="(watchHistoryData, i) in watchHistoryDataList"
          :key="i"
        >
          <cpnt-thumb-content
            :item="watchHistoryData.tba"
            :isMobile="isMobileBrowser"
            @execute="exeContentFromHistory(watchHistoryData)"
          >
          </cpnt-thumb-content>
        </Col>
        <!-- Pager -->
        <Col span="24">
          <Page
            class="pager-center"
            :page-size="watchHistoryPager.size"
            :total="watchHistoryPager.total"
            :current="watchHistoryPager.pageIndex"
            @on-change="watchHistoryLoadMore"
          ></Page>
        </Col>
      </Row>
    </div>
    <!-- Loading -->
    <div class="noti-loading-container" v-show="watchHistoryPager.busy">
      <cpnt-thumb-loading></cpnt-thumb-loading>
    </div>
    <!-- Del Modal -->
    <Modal v-model="delModalModalDisplay" width="360">
      <p>
        <Icon type="information-circled"></Icon>
        <span>{{ $t("baseModal.delConfirm") }}</span>
      </p>
      <div slot="footer">
        <Button @click="closeDelModal">{{ $t("baseModal.cancelBtn") }}</Button>
        <Button type="error" @click="delWatchHistoryList">
          {{ $t("baseModal.delBtn") }}
        </Button>
      </div>
    </Modal>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

import CpntThumbContent from "../thumb-content.vue";
import CpntThumbLoading from "../thumb-loading.vue";

export default {
  name: "watch-history-list",
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
      watchHistoryDataList: [],
      watchHistoryMoreUrl: null,
      watchHistoryPager: {
        busy: false,
        pageIndex: 1,
        lastPage: 0,
        total: 0,
        size: 100,
      },
      delModalModalDisplay: false,
    };
  },
  methods: {
    init() {
      this.getWatchHistoryList();
    },
    getWatchHistoryList() {
      let _this = this;
      let url = "/exhibition/tbavideo/get-hists";

      _this.watchHistoryPager.busy = true;
      axios
        .get(url, {
          params: {
            size: _this.watchHistoryPager.size,
          },
        })
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(data.statusText);

          // Assign data list
          let data = res.data.data;
          _this.watchHistoryDataList = data.data;

          // Pagination
          _this.watchHistoryMoreUrl = data.next_page_url;
          _this.watchHistoryPager.lastPage = data.last_page;
          _this.watchHistoryPager.total = data.total;
          _this.watchHistoryPager.pageIndex = 1;
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          _this.watchHistoryPager.busy = false;
        });
    },
    watchHistoryLoadMore(p) {
      let _this = this;
      let url = _this.watchHistoryMoreUrl;
      _this.watchHistoryPager.pageIndex = p;

      if (_this.watchHistoryPager.pageIndex > _this.watchHistoryPager.lastPage)
        return;

      axios
        .get(url, {
          params: {
            page: _this.watchHistoryPager.pageIndex,
            size: _this.watchHistoryPager.size,
          },
        })
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(data.statusText);

          let data = res.data.data;
          _this.watchHistoryDataList = data.data;
        });
    },
    exeContentFromHistory(watchHistoryData) {
      if (!watchHistoryData.url) return;

      let url = new URL(watchHistoryData.url);
      let contentId = url.searchParams.get("contentId");
      let channelId = url.searchParams.get("channelId");
      let groupIds = url.searchParams.get("groupIds");

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
    delWatchHistoryList() {
      let url = "/exhibition/tbavideo/delete-hists";
      axios
        .delete(url)
        .then((res) => {
          if (res.status !== 200 || !res.data.status)
            throw new Error(res.data.message);
          this.$Notice.success({
            title: this.$t("messages.success"),
          });
          this.$router.go();
        })
        .catch((e) => {
          console.log(e);
          this.$Notice.error({
            title: this.$t("messages.error"),
          });
        })
        .finally(() => {
          this.closeDelModal();
        });
    },
    displayDelModal() {
      this.delModalModalDisplay = true;
    },
    closeDelModal() {
      this.delModalModalDisplay = false;
    },
  },
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.watch-history-list {
  .watch-history-list-block {
    .clear-btn-container {
      padding: 0 10px;
    }
    .pager-center {
      padding-top: 30px;
      text-align: center;
    }
  }
}
</style>
