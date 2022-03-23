<template>
  <article class="my-sokrates">
    <!-- Header -->
    <Row class="my-sokrates-info">
      <div id="my-sokrates-info-view">
        <!-- User Info -->
        <Row type="flex" justify="center" align="middle">
          <Col span="6" style="text-align: center">
            <Img class="my-avatar" :src="urlAvatar" />
          </Col>
          <Col span="8">
            <cpnt-thumb-user-info></cpnt-thumb-user-info>
          </Col>
        </Row>
        <!-- User Menu -->
        <Row type="flex" justify="center" align="middle">
          <Col span="2" style="text-align: right" v-if="isNarrowScreen">
            <Icon type="chevron-left"></Icon>
          </Col>
          <Col :lg="22" :md="22" :sm="20" :xs="20">
            <Tabs
              v-model="curMenuRef"
              @on-click="exeMenuTab"
              :class="{ 'narrowed-ivu-tabs': isNarrowScreen }"
            >
              <TabPane
                v-for="(userMenu, k) in userMenuList"
                :key="'menu_' + k"
                :name="userMenu.ref"
                :label="createMenuTabLabel(userMenu)"
              >
              </TabPane>
            </Tabs>
          </Col>
          <Col span="2" style="text-align: left" v-if="isNarrowScreen">
            <Icon type="chevron-right"></Icon>
          </Col>
        </Row>
      </div>
    </Row>

    <!-- General Observation -->
    <Row
      v-if="displayMode.generalObsrv"
      class="my-sokrates-gen-obsrv-class-container"
    >
      <hr />
      <Col span="24">
        <h1 :class="genObsrvClassState.titleStyle">
          {{ $t("myMovie.generalObsrv") }}
          ({{ exhibitInfo.channel.data.generalObsrv.total }})
        </h1>
        <div :class="genObsrvClassState.btnStyle" v-if="genObsrvClassAllowed">
          <router-link :to="genObsrvClassState.routerLink">
            <Button type="info" icon="play" :long="isMobileBrowser">
              {{ $t("sokObsrvClassModal.btn") }}
            </Button>
          </router-link>
        </div>
      </Col>
      <Col span="24">
        <cpnt-thumb-general-obsrv-video-list
          :tbaIdList="exhibitInfo.channel.data.generalObsrv.tba_ids"
        ></cpnt-thumb-general-obsrv-video-list>
      </Col>
    </Row>

    <!-- My Videos -->
    <Row v-show="displayMode.general">
      <Row>
        <hr>
        <Col>
          <span style="font-size: 2em;font-weight: bold; margin: 0 10px"> {{ type ? type : $t('channelTotal') }}   ({{ pager.total }})</span>
        </Col>
        <Col span="12">
<!--          <div style="text-align: left">-->
<!--            <Icon type="android-apps" style="font-size: 30px;padding-right: 10px; cursor:pointer" @click="display= false"></Icon>-->
<!--            <Icon type="android-menu" style="font-size: 30px; cursor:pointer" @click="display= true"></Icon>-->
<!--          </div>-->
        </Col>
      </Row>
      <!-- Grid list-->
      <Row v-if="!display" style="padding-top: 10px;">
        <Col
          :xs="24"
          :sm="12"
          :md="8"
          :lg="6"
          v-for="(v, i) in groupChannels.list"
          :key="i"
        >
          <cpnt-thumb-content
            v-if="!display"
            v-bind:item="v"
            v-bind:isMobile="isMobileBrowser"
            v-on:execute="exeContent"
          >
          </cpnt-thumb-content>
        </Col>
      </Row>
      <!--     Table list-->
      <Row v-if="display" style="padding-top: 10px;">
        <cpnt-thumb-table v-if="display" :item="groupChannels.list" :pager="pager" v-on:execute="exeContent"></cpnt-thumb-table>
      </Row>


      <Row v-if="pager.busy">
        <Col span="24">
          <Spin fix>
            <Icon type="load-c" size=30 class="spin-icon-load"></Icon>
          </Spin>
        </Col>
      </Row>
      <Row>
        <Col span="24">
          <Page class="pager-center" :page-size="pager.size" :total="pager.total" :current="pager.pageIndex" @on-change="loadMore"></Page>
        </Col>
      </Row>
    </Row>

    <!-- My Notification -->
    <Row v-if="displayMode.notifications">
      <hr>
      <h1>
        {{ $t('notifications.title') }} ({{ user.notifications.length }})
      </h1>
      <Col span="24">
        <cpnt-thumb-notification-list></cpnt-thumb-notification-list>
      </Col>
    </Row>

    <!-- My Comments -->
    <Row v-if="displayMode.publicComments || displayMode.privateComments || displayMode.isMarkComments">
      <hr>
      <h1>
        {{ curCommentDisplayMode.title}} ({{ curCommentDisplayMode.count }})
      </h1>
      <Col span="24">
        <cpnt-thumb-comment-list :mode="curCommentDisplayMode.apiMode"></cpnt-thumb-comment-list>
      </Col>
    </Row>

    <!-- My Channels -->
    <Row v-if="displayMode.channels">
      <hr>
      <h1>
        {{ $t("channels.my") }} ({{ exhibitInfo.channel.total }})
      </h1>
      <Col span="24">
        <cpnt-thumb-channel-list></cpnt-thumb-channel-list>
      </Col>
    </Row>

    <!-- My Favorite Videos -->
    <Row v-if="displayMode.favoriteVideos">
      <hr />
      <h1>
        {{ $t("favoriteVideos") }} ({{ exhibitInfo.channel.data.favoriteVideos.total }})
      </h1>
      <cpnt-thumb-favorite-video-list></cpnt-thumb-favorite-video-list>
    </Row>

    <!-- Watch History -->
    <Row v-if="displayMode.watchHistory">
      <hr />
      <h1>
        {{ $t("watchHistory") }} ({{ exhibitInfo.channel.data.watchHistory.total }})
      </h1>
      <cpnt-thumb-watch-history-list></cpnt-thumb-watch-history-list>
    </Row>

    <!-- Videos Observed -->
    <Row v-if="displayMode.videosObserved">
      <hr />
      <h1>
        {{ $t("videosObserved") }} ({{ exhibitInfo.channel.data.videosObserved.total }})
      </h1>
      <cpnt-thumb-observed-video-list></cpnt-thumb-observed-video-list>
    </Row>

    <!-- Recommeded Videos -->
    <Row v-if="displayMode.recVideos">
      <hr />
      <h1>
        {{ $t("sets.contents.recommended") }} ({{ exhibitInfo.channel.data.recommendedVideos.total }})
      </h1>
      <cpnt-thumb-rec-video-list></cpnt-thumb-rec-video-list>
    </Row>

    <!-- Latest Videos -->
    <Row v-if="displayMode.latestVideos">
      <hr />
      <h1>
        {{ $t("sets.contents.newest") }} ({{ exhibitInfo.channel.data.latestVideos.limit }})
      </h1>
      <cpnt-thumb-latest-video-list
        :limit="exhibitInfo.channel.data.latestVideos.limit"
      ></cpnt-thumb-latest-video-list>
    </Row>

    <!-- Back to Top -->
    <BackTop></BackTop>
  </article>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

import CpntThumbContent from "../../../app/components/thumb-content.vue";
import CpntThumbTable from "../../../app/components/thumb-table";

import CpntThumbUserInfo from "../../../app/components/my-sokrates/thumb-user-info.vue";
import CpntThumbUserMenuBlock from "../../../app/components/my-sokrates/thumb-user-menu-block.vue";
import CpntThumbChannelList from "../../../app/components/my-sokrates/thumb-channel-list.vue";
import CpntThumbCommentList from "../../../app/components/my-sokrates/thumb-comment-list.vue";
import CpntThumbNotificationList from "../../../app/components/my-sokrates/thumb-notification-list.vue";
import CpntThumbFavoriteVideoList from "../../../app/components/my-sokrates/thumb-favorite-video-list.vue";
import CpntThumbWatchHistoryList from "../../../app/components/my-sokrates/thumb-watch-history-list.vue";
import CpntThumbGeneralObsrvVideoList from "../../../app/components/my-sokrates/thumb-general-obsrv-video-list.vue";
import CpntThumbObservedVideoList from "../../../app/components/my-sokrates/thumb-observed-video-list.vue";
import CpntThumbRecVideoList from "../../../app/components/my-sokrates/thumb-recommended-video-list.vue";
import CpntThumbLatestVideoList from "../../../app/components/my-sokrates/thumb-latest-video-list.vue";

export default {
  components: {
    "cpnt-thumb-content": CpntThumbContent,
    "cpnt-thumb-table": CpntThumbTable,
    "cpnt-thumb-user-info": CpntThumbUserInfo,
    "cpnt-thumb-user-menu-block": CpntThumbUserMenuBlock,
    "cpnt-thumb-channel-list": CpntThumbChannelList,
    "cpnt-thumb-comment-list": CpntThumbCommentList,
    "cpnt-thumb-notification-list": CpntThumbNotificationList,
    "cpnt-thumb-favorite-video-list": CpntThumbFavoriteVideoList,
    "cpnt-thumb-watch-history-list": CpntThumbWatchHistoryList,
    "cpnt-thumb-general-obsrv-video-list": CpntThumbGeneralObsrvVideoList,
    "cpnt-thumb-observed-video-list": CpntThumbObservedVideoList,
    "cpnt-thumb-rec-video-list": CpntThumbRecVideoList,
    "cpnt-thumb-latest-video-list": CpntThumbLatestVideoList,
  },

  computed: _.merge(
    Vuex.mapState(["path", "user", "isMobileBrowser"]),
    Vuex.mapGetters(["urlAvatar", "isNarrowScreen", "genObsrvClassAllowed"]),
    {
      initDisplayModeNav() {
        // Return display mode ref on initial
        // Based on display query param
        let displayModeRef = null;
        switch (this.$route.query.display) {
          case "notifications":
            displayModeRef = "notifications";
            break;
        }
        return displayModeRef;
      },
      initDisplayMode() {
        // Return display mode ref on initial
        // Based on general landing without display query param
        let displayModeRef = "channelTotal";

        // If mobile and genObsrvClassAllowed, then set ref as "generalObsrv"
        if (this.genObsrvClassAllowed && this.isMobileBrowser)
          displayModeRef = "generalObsrv";

        return displayModeRef;
      },
      ivuTabsStyleSetting() {
        let countFontSize = 26;
        let labelFontSize = 14;
        if (this.isNarrowScreen) {
          countFontSize = 20;
          labelFontSize = 12;
        }
        return {
          "countFontSize": `${countFontSize}px`,
          "labelFontSize": `${labelFontSize}px`,
        }
      },
      userMenuList() {
        // This contains a list of user menu objects
        // which will be rendered in order
        return [
          {
            name: this.$t("myMovie.generalObsrv"),
            count: this.exhibitInfo.channel.data.generalObsrv.total,
            exeFunc: () => {
              this.toDisplayMode(
                "generalObsrv",
                this.$t("myMovie.generalObsrv")
              );
            },
            ref: "generalObsrv",
          },
          {
            name: this.$t("channels.my"),
            count: this.exhibitInfo.channel.total,
            exeFunc: () => {
              this.toDisplayMode(
                "channels",
                this.$t("channels.my")
              );
            },
            ref: "channels",
          },
          {
            name: this.$t("sets.contents.recommended"),
            count: this.exhibitInfo.channel.data.recommendedVideos.total,
            exeFunc: () => {
              this.toDisplayMode(
                "recVideos",
                this.$t("sets.contents.recommended")
              );
            },
            ref: "recVideos",
          },
          {
            name: this.$t("sets.contents.newest"),
            count: this.exhibitInfo.channel.data.latestVideos.limit,
            exeFunc: () => {
              this.toDisplayMode(
                "latestVideos",
                this.$t("sets.contents.newest")
              );
            },
            ref: "latestVideos",
          },
          {
            name: this.$t("channelTotal"),
            count: this.exhibitInfo.channel.data.all_total.total,
            exeFunc: () => {
              this.execFilterMovie(
                this.exhibitInfo.channel.data.all_total.tba_ids,
                this.$t("channelTotal")
              );
            },
            ref: "channelTotal",
          },
          {
            name: this.$t("videosObserved"),
            count: this.exhibitInfo.channel.data.videosObserved.total,
            exeFunc: () => {
              this.toDisplayMode(
                "videosObserved",
                this.$t("videosObserved")
              );
            },
            ref: "videosObserved",
          },
          {
            name: this.$t("watchHistory"),
            count: this.exhibitInfo.channel.data.watchHistory.total,
            exeFunc: () => {
              this.toDisplayMode(
                "watchHistory",
                this.$t("watchHistory")
              );
            },
            ref: "watchHistory",
          },
          {
            name: this.$t("publicChannel"),
            count: this.exhibitInfo.channel.data.public_total.total,
            exeFunc: () => {
              this.execFilterMovie(
                this.exhibitInfo.channel.data.public_total.tba_ids,
                this.$t("publicChannel")
              );
            },
            ref: "publicChannel",
          },
          {
            name: this.$t("doubleGreenLightTotal"),
            count: this.exhibitInfo.channel.data.doubleGreenLight_total.total,
            exeFunc: () => {
              this.execFilterMovie(
                this.exhibitInfo.channel.data.doubleGreenLight_total.tba_ids,
                this.$t("doubleGreenLightTotal")
              );
            },
            ref: "doubleGreenLightTotal",
          },
          {
            name: this.$t("myMovie.material"),
            count: this.exhibitInfo.channel.data.material.total,
            exeFunc: () => {
              this.execFilterMovie(
                this.exhibitInfo.channel.data.material.tba_ids,
                this.$t("myMovie.material")
              );
            },
            ref: "material",
          },
          {
            name: this.$t("myMovie.lessonPlan"),
            count: this.exhibitInfo.channel.data.lessonPlan.total,
            exeFunc: () => {
              this.execFilterMovie(
                this.exhibitInfo.channel.data.lessonPlan.tba_ids,
                this.$t("myMovie.lessonPlan")
              );
            },
            ref: "lessonPlan",
          },
          {
            name: this.$t("clickTotal"),
            count: this.exhibitInfo.channel.data.hits_total.total,
            exeFunc: () => {
              this.execFilterMovie(
                this.exhibitInfo.channel.data.hits_total.tba_ids,
                this.$t("clickTotal")
              );
            },
            ref: "hitsTotal",
          },
          {
            name: this.$t("tbaMarkerCount"),
            count: this.exhibitInfo.channel.data.total_mark.total,
            exeFunc: () => {
              this.toDisplayMode("isMarkComments", this.$t("tbaMarkerCount"));
            },
            ref: "tbaMarkerCount",
          },
          {
            name: this.$t("publicTbaMarkerCount"),
            count: this.exhibitInfo.channel.data.public_mark.total,
            exeFunc: () => {
              this.toDisplayMode(
                "publicComments",
                this.$t("publicTbaMarkerCount")
              );
            },
            ref: "publicTbaMarkerCount",
          },
          {
            name: this.$t("ownerTbaMarkerCount"),
            count: this.exhibitInfo.channel.data.private_mark.total,
            exeFunc: () => {
              this.toDisplayMode(
                "privateComments",
                this.$t("ownerTbaMarkerCount")
              );
            },
            ref: "ownerTbaMarkerCount",
          },
          {
            name: this.$t("notifications.title"),
            count: this.user.notifications.length,
            exeFunc: () => {
              this.toDisplayMode(
                "notifications",
                this.$t("notifications.title")
              );
            },
            ref: "notifications",
          },
          {
            name: this.$t("favoriteVideos"),
            count: this.exhibitInfo.channel.data.favoriteVideos.total,
            exeFunc: () => {
              this.toDisplayMode("favoriteVideos", this.$t("favoriteVideos"));
            },
            ref: "favoriteVideos",
          },
        ];
      },
      curDisplayMode() {
        return _.findKey(this.displayMode, (v) => {
          return v === true;
        });
      },
      curCommentDisplayMode() {
        // This is for comment Display and API usage
        let commentDisplayData = {
          mode: null,
          count: null,
          apiMode: null,
        };
        if (this.displayMode.publicComments) {
          commentDisplayData.title = this.$t("publicTbaMarkerCount");
          commentDisplayData.count = this.exhibitInfo.channel.data.public_mark.total;
          commentDisplayData.apiMode = "public";
        } else if (this.displayMode.privateComments) {
          commentDisplayData.title = this.$t("ownerTbaMarkerCount");
          commentDisplayData.count = this.exhibitInfo.channel.data.private_mark.total;
          commentDisplayData.apiMode = "private";
        } else if (this.displayMode.isMarkComments) {
          commentDisplayData.title = this.$t("tbaMarkerCount");
          commentDisplayData.count = this.exhibitInfo.channel.data.total_mark.total;
          commentDisplayData.apiMode = "isMark";
        }
        return commentDisplayData;
      },

      genObsrvClassState() {
        // Computed prop containing General Observation Class states
        let titleStyle = "my-sokrates-gen-obsrv-class-title";
        let btnStyle = "my-sokrates-gen-obsrv-class-btn";
        let routerLink = "/general-observation-class";
        if (this.isMobileBrowser) {
          titleStyle = "my-sokrates-gen-obsrv-class-title-mobile";
          btnStyle = "my-sokrates-gen-obsrv-class-btn-mobile";
          routerLink = "/observation-class";
        }
        return {
          titleStyle: titleStyle,
          btnStyle: btnStyle,
          routerLink: routerLink,
        };
      },
    }
  ),

  data() {
    return {
      type                     : null,
      displayMode              : {
        'generalObsrv'         : false,
        'general'              : false,
        'channels'             : false,
        'notifications'        : false,
        'publicComments'       : false,
        'privateComments'      : false,
        'isMarkComments'       : false,
        'favoriteVideos'       : false,
        'watchHistory'         : false,
        'videosObserved'       : false,
        'recVideos'            : false,
        'latestVideos'         : false,
      },
      curMenuRef               : null,
      groupChannels            : {
        list: [],
      },
      exhibitInfo              : {
        channel: {
          total: 0,
          data : {
            all_total             : {
              tba_ids: [],
              total  : 0
            },
            doubleGreenLight_total: {
              tba_ids: [],
              total  : 0
            },
            hits_total            : {
              tba_ids: [],
              total  : 0
            },
            private_mark          : {
              tba_ids: [],
              total  : 0
            },
            total_mark            : {
              tba_ids: [],
              total  : 0
            },
            public_mark           : {
              tba_ids: [],
              total  : 0
            },
            public_total          : {
              tba_ids: [],
              total  : 0
            },
            material              : {
              tba_ids: [],
              total  : 0
            },
            lessonPlan            : {
              tba_ids: [],
              total  : 0
            },
            favoriteVideos        : {
              total  : 0
            },
            watchHistory          : {
              total  : 0
            },
            generalObsrv          : {
              tba_ids: [],
              total  : 0
            },
            videosObserved        : {
              total  : 0
            },
            recommendedVideos      : {
              total  : 0
            },
            latestVideos           : {
              limit  : 0 // This is set by the API, refer to ExhibitionRepository.php
            },
          },
        }

      },
      list                     : {
        items: [],
        total: 0,
      },
      pager                    : {
        busy     : false,
        pageIndex: 1, // 請求頁數
        last_page: 0,
        total    : 0,
        size     : 100,
      },
      groupId                  : 0,
      display                  : false,
      loadMoreUrl              : null,
      selectList               : null,
      loading                  : false,
      mouseOver                : false,
    };
  },
  watch  : {
    '$route'   : 'init',
  },
  methods: {

    async init() {
      if (!document.cookie) location.reload();
      this.$emit('check-logined', true, false, false, false);

      await this.getExhibitInfo().then(() => {
        this.getGroupChannel();
        this.setupInitDisplayMode();
      });
    },

    setupInitDisplayMode() {
      // Setup initial display mode
      // initDisplayModeNav will override initDisplayMode
      this.resetDisplayMode();
      this.curMenuRef = this.initDisplayModeNav || this.initDisplayMode;
      this.toDisplayMode(this.curMenuRef);
    },

    resetDisplayMode() {
      // Assign False to all display modes
      _.each(this.displayMode, (value, key) => {
        this.displayMode[key] = false;
      });
    },

    toDisplayMode(displayModeName, type) {
      this.resetDisplayMode(); // Reset display
      this.type = type;
      let displayModeList = _.keys(this.displayMode);
      let displayModeKey = _.includes(displayModeList, displayModeName)
        ? displayModeName
        : "general";
      _.set(this.displayMode, displayModeKey, true);
    },

    getGroupChannel() {
      let _this = this;
      let url = '/exhibition/tbavideo/get-my-movies';
      _this.loadMoreUrl = url;

      axios.get(url, {
        params: {
          size: _this.pager.size
        },
      }).then((data) => {

        let result = data.data.data;
        if (!data.status) {
          return;
        }

        _this.pager.last_page = result.list.last_page
        _this.pager.total = result.list.total
        _this.groupChannels.list = result.list.data;
        _this.pager.pageIndex = 1;

      }).catch((e) => {

        console.log(e);
      });
    },

    exeUserMenu(userMenu) {
      userMenu.exeFunc();
      this.curMenuRef = userMenu.ref;
    },

    exeMenuTab() {
      let menuData = _.find(this.userMenuList, ["ref", this.curMenuRef]);
      if (menuData === undefined) return;
      this.exeUserMenu(menuData);
    },

    getMyAllowUploadChannel() {
      let _this = this;
      let url = '/exhibition/tbavideo/get-my-channel-info';
      axios.get(url).then((response) => {
        _this.selectList = _.map(response.data.data.allow_channel_upload.list, function (v) {
          return {
            id  : v.id,
            name: v.name,
          };
        })
      })
    },

    exeContent(content) {
      let groupIds = _.join(_.uniq(_.map(content.group_channels, 'group_id')), ',');
      let channelId = _.join(_.uniq(_.map(content.group_channels, 'id')), ',');
      this.$router.push({
        name  : "content",
        params: {contentId: content.id},
        query : {
          groupIds : groupIds,
          channelId: channelId
        },
      });
    },

    async getExhibitInfo() {
      await axios
          .get("/user/tbavideo/get-exhibit-info", {
            params: {
              excsReqd: false,
            }
          })
          .then((data) => {
            data = data.data
            if (!data.status) {
              return
            }
            this.exhibitInfo = data.data
          })
          .catch((e) => {
            console.log(e)
          })
    },

    loadMore(p) {
      let _this = this;
      _this.pager.pageIndex = p;
      if (_this.pager.pageIndex > _this.pager.last_page) return false;
      _this.pager.busy = true;
      axios
          .get(_this.loadMoreUrl, {
            params: {
              page: _this.pager.pageIndex,
              size: _this.pager.size,
            },
          })
          .then((response) => {
            let data = response.data.data.list;
            _this.groupChannels.list = data.data;
          })
          .finally(() => {
            _this.pager.busy = false;
          });
    },

    execFilterMovie(tba_ids, type) {
      let _this = this;
      let url = `/exhibition/tbavideo/get-filter-movie`;
      _this.toDisplayMode('general')
      _this.type = type;
      axios.get(url, {
        params: {
          size   : _this.pager.size,
          tba_ids: tba_ids
        }
      }).then((data) => {
        let result = data.data.data;
        if (!data.status) {
          return;
        }

        _this.pager.last_page = result.last_page
        _this.pager.total = result.total
        _this.groupChannels.list = result.data;
        _this.pager.pageIndex = 1;
        // console.log(response)
      }).catch((e) => {
        console.log(e.error)
      });
    },

    createMenuTabLabel(userMenuData) {
      return (h) =>
        h("div", [
          h(
            "p",
            {
              style: {
                "font-size": this.ivuTabsStyleSetting.countFontSize,
                "text-shadow": "1px 1px #2f4f4f",
              },
            },
            userMenuData.count
          ),
          h(
            "p",
            {
              style: {
                "font-size": this.ivuTabsStyleSetting.labelFontSize,
              },
            },
            userMenuData.name
          ),
        ]);
    },
  },

  mounted() {
    this.init();
  },

};


</script>

<style lang="scss">
.my-sokrates {
  .ivu-row {
    padding: 10px 0px;
  }

  /* Hide scrollbar for Chrome, Safari and Opera */
  .ivu-tabs::-webkit-scrollbar {
    display: none;
  }

  .narrowed-ivu-tabs {
    display: flex;
  }

  .ivu-tabs {
    padding: 10px 20px 10px 20px;
    overflow-x: auto;

    /* Hide Scrollbar IE, FireFox */
    -ms-overflow-style: none;
    scrollbar-width: none;

    .ivu-tabs-bar {
      all: unset;

      .ivu-tabs-nav-prev, .ivu-tabs-nav-next {
        font-size: 16px;
        color: #ffffff;
      }
    }

    .ivu-tabs-tab {
      font-size: 1em;
      font-weight: bold;
      color: #acd6ff;
      text-align: center;
    }
  }

  p {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
  }

  .my-avatar {
    width: 80%;
    max-width: 130px;
    height: 80%;

    display: inline-block;
    border-radius: 50%;

    padding: 10px;
  }

  .my-sokrates-info {
    line-height: 2;
  }

  .my-sokrates-gen-obsrv-class-container {
    .my-sokrates-gen-obsrv-class-title {
      display: inline-block;
    }

    .my-sokrates-gen-obsrv-class-btn {
      display: inline-block;
    }

    .my-sokrates-gen-obsrv-class-title-mobile {
      display: initial;
    }

    .my-sokrates-gen-obsrv-class-btn-mobile {
      padding: 5px 30px 5px 30px;
    }
  }

  .upload-video-icon, .obrsv-class-icon {
    padding: 5px;
    font-size: 30px;
    font-weight: bold;
  }

  .clickable:hover {
    color: #acd6ff;
    cursor: pointer;
  }

  .divider {
    margin-top: 32px;
    border-top: 2px solid #666;
    text-align: center;

    span {
      position: relative;
      top: -16px;
      padding: 0 20px;
      color: #fff;
      background: #1b1b1b;
      font-size: x-large;
    }
  }

  .editor-modal {
    font-size: 16px;

    .editor-item {
      font-size: 12px;
      padding-top: 10px;
    }

    .footer-text {
      font-size: 12px;
      padding: 5px;
    }
  }

  .pager-center {
    padding-top: 30px;
    text-align: center;
  }

  .spin-icon-load {
    animation: ani-spin 1s linear infinite;
  }

  @keyframes ani-spin {
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
}
</style>
