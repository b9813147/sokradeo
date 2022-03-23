<template>
  <div class="notification-list">
    <Row
        type="flex"
        justify="center"
        class="notilist-block"
        v-if="isDataPresentable"
    >
      <Col :lg="12" :md="12" :sm="20" :xs="20">
        <Row
            v-for="(item, k) in notificationDataList"
            :key="k"
            class="noti-item"
        >
          <!-- Img -->
          <Col :lg="4" :md="4" :sm="4" :xs="4" align="middle">
            <Img :class="notiImgClass" :src="getThumbnailImgSrc(item)"/>
          </Col>
          <!-- Content Section -->
          <Col :lg="18" :md="18" :sm="18" :xs="18">
            <Row>
              <Collapse
                  class="noti-content-container"
                  v-model="curNotificationDetail"
                  @click="displayNotificationDetail(k, item)"
                  accordion
              >
                <Panel :name="getNotiUniqueKey(k, item)">
                  <!-- Title -->
                  <Row class="noti-header">
                    <p :class="notiHeaderClass.notiSender">
                      {{ item.channel }}
                    </p>
                    <p :class="notiHeaderClass.notiTitle">
                      {{ item.title }}
                    </p>
                  </Row>
                  <!-- Content -->
                  <Row class="noti-content" slot="content">
                    <Col :lg="24" :md="24" :sm="24" :xs="24">
                      <div
                          style="font-size: 12px; white-space: pre-wrap"
                          v-html="item.content"
                      />
                      <a :href="item.url" target="_blank">
                        <div
                            class="noti-sender"
                            style="white-space: pre-wrap"
                            v-html="item.url"
                        />
                      </a>
                      <!-- Activity Btn -->
                      <div class="noti-activity-btn" v-if="item.isOperating">
                        <Button
                            type="primary"
                            @click="goToActivityChannel(item)"
                        >
                          {{ $t("notifications.apply") }}
                        </Button>
                      </div>
                    </Col>
                  </Row>
                </Panel>
              </Collapse>
            </Row>
          </Col>
          <!-- View -->
          <Col :lg="2" :md="2" :sm="2" :xs="2" align="middle">
            <div class="noti-view-btn">
              <Icon
                  type="eye"
                  size="18"
                  @click="displayNotificationDetail(k)"
              ></Icon>
            </div>
          </Col>
        </Row>
      </Col>
    </Row>
    <!-- Loading -->
    <Row class="noti-loading-container" v-else-if="isLoading">
      <Col span="24">
        <cpnt-thumb-loading></cpnt-thumb-loading>
      </Col>
    </Row>
    <!-- Pager -->
    <Row v-if="isPagerPresentable">
      <Col span="24">
        <Page
            class="pager-center"
            :page-size="notificationPager.perPage"
            :total="notificationPager.total"
            :current="notificationPager.pageIndex"
            @on-change="loadMoreNotificationList"
        ></Page>
      </Col>
    </Row>
  </div>
</template>

<script>
import _    from "lodash";
import Vuex from "vuex";

import CpntThumbLoading from "../thumb-loading.vue";

export default {
  name      : "notification-list",
  components: {
    "cpnt-thumb-loading": CpntThumbLoading,
  },
  computed  : _.merge(
      Vuex.mapState(["path", "user"]),
      Vuex.mapGetters(["logined", "isNarrowScreen"]),
      {
        notiImgClass() {
          return this.isNarrowScreen ? "noti-img-mobile" : "noti-img";
        },
        notiHeaderClass() {
          let mobileClass = " mobile";
          let notiSender = "noti-sender";
          let notiTitle = "noti-title";
          if (this.isNarrowScreen) {
            notiSender += mobileClass;
            notiTitle += mobileClass;
          }
          return {
            notiSender: notiSender,
            notiTitle : notiTitle,
          };
        },
        pathGroupChannel() {
          return this.path.groupChannel;
        },
        notiDetailPrefix() {
          return "noti_detail_";
        },
        isDataPresentable() {
          return this.notificationDataList.length > 0 && !this.isLoading;
        },
        isPagerPresentable() {
          return this.notificationPager.perPage;
        },
      }
  ),
  data() {
    return {
      notificationDataList : [],
      notificationPager    : {
        perPage  : null,
        pageIndex: 1,
        lastPage : 0,
        total    : 0,
      },
      curNotificationDetail: "",
      isLoading            : false,
    };
  },
  methods: _.merge(Vuex.mapActions([]), {
    init() {
      this.getNotificationList();
    },
    getNotificationList() {
      this.isLoading = true;
      axios
          .get("/exhibition/notification")
          .then((data) => {
            if (data.status !== 200) throw new Error(data.status);
            let notificationData = data.data;
            this.notificationDataList = notificationData.data;
            // Pagination
            this.notificationPager.perPage = notificationData.per_page;
            this.notificationPager.lastPage = notificationData.last_page;
            this.notificationPager.total = notificationData.total;
            // Assume all notifications are read
            this.user.notification_count = 0;
          })
          .catch((e) => {
            console.log(e);
          })
          .finally(() => {
            this.isLoading = false;
          });
    },
    loadMoreNotificationList(pageIndex) {
      this.notificationPager.pageIndex = pageIndex;
      this.isLoading = true;
      axios
          .get("/exhibition/notification", {
            params: {
              page: pageIndex,
            },
          })
          .then((data) => {
            if (data.status !== 200) throw new Error(data.status);
            let notificationData = data.data;
            this.notificationDataList = notificationData.data;
          })
          .catch((e) => {
            console.log(e);
          })
          .finally(() => {
            this.isLoading = false;
          });
    },
    getThumbnailImgSrc(notificationData) {
      let src = "/images/app/tw/teammodel/original-black-small.png";
      if (notificationData.thumbnail)
        src =
            this.pathGroupChannel +
            notificationData.channel_id +
            "/" +
            notificationData.thumbnail +
            "?" +
            Math.random();
      return src;
    },
    getNotiUniqueKey(key, notificationData) {
      return (
          this.notiDetailPrefix +
          "_" +
          key +
          "_" +
          notificationData.notification_message_id
      );
    },
    displayNotificationDetail(key, notificationData) {
      let notiDetailKey = this.getNotiUniqueKey(key, notificationData);
      if (this.curNotificationDetail == notiDetailKey)
        this.curNotificationDetail = "";
      else this.curNotificationDetail = notiDetailKey;
    },
    goToActivityChannel(notificationData) {
      let link = notificationData.link;
      this.$router.push(link);
    },
  }),
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss">
.notification-list {
  position: relative;

  .notilist-block {
    margin-top: 10px;

    .title {
      color: white;
    }

    .noti-item {
      padding: 0;

      .noti-img {
        margin-top: 10px;
        border-radius: 50%;
        max-width: 50px;
        min-width: 40px;
      }

      .noti-img-mobile {
        margin-top: 10px;
        border-radius: 50%;
        max-width: 40px;
        min-width: 40px;
      }

      .noti-content-container {
        color: #ffffff !important;
        background-color: transparent !important;
        border: none !important;
      }

      .noti-header {
        font-weight: bold;
        line-height: 1.5;

        .noti-sender {
          font-size: 12px;
          color: #86d3ff;

          &.mobile {
            font-size: 10px;
          }
        }

        .noti-title {
          font-size: 16px;
          color: #ffffff;

          &.mobile {
            font-size: 14px;
          }
        }
      }

      .noti-content {
        padding: 10px;
      }

      .noti-view-btn {
        text-align: center;

        margin-top: 30px;
        border-radius: 50%;
        max-width: 60px;
        min-width: 40px;

        cursor: pointer;
      }

      .noti-activity-btn {
        padding: 20px 0;
        text-align: center;
      }

      // Adjust iView style (this has to be without scoped)
      .ivu-row {
        padding: 5px 0px;
      }

      .ivu-collapse-header {
        height: 100%;
        line-height: none;
        padding-left: none;
        transition: all 0.2s ease-in-out;

        .ivu-icon {
          display: none;
        }
      }

      .ivu-collapse-header:hover {
        transform: scale(1.01);
      }

      .ivu-collapse-content {
        color: #ffffff;
        background-color: transparent;
      }
    }
  }

  .pager-center {
    padding-top: 10px;
    text-align: center;
  }
}
</style>
