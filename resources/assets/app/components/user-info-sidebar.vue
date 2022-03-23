<template>
  <article class="user-info">
    <!-- User Profile -->
    <Card :bordered="false" :dis-hover="true" class="profile">
      <!-- User Img -->
      <Row>
        <Col span="24" justify="center" class="user-info-avatar-container">
          <Avatar class="user-info-avatar" :src="urlAvatar"></Avatar>
        </Col>
      </Row>
      <!-- User Details -->
      <Row>
        <Col span="24" justify="center">
          <!-- TMID -->
          <Row justify="center" class="user-info-item-container">
            <Col span="10" offset="1" class="user-info-item-title-container">
              <span class="user-info-item-title">
                {{ $t("user.teamModelId") }}
              </span>
            </Col>
            <Col span="10" offset="2">
              <span class="user-info-item-content">{{ user.habook }}</span>
            </Col>
          </Row>
          <!-- Username -->
          <Row justify="center" class="user-info-item-container">
            <Col span="10" offset="1" class="user-info-item-title-container">
              <span class="user-info-item-title">{{ $t("user.name") }}</span>
            </Col>
            <Col span="10" offset="2">
              <span class="user-info-item-content">{{ user.name }}</span>
            </Col>
          </Row>
          <!-- Default Channel -->
          <Row justify="center" class="user-info-item-container">
            <Col span="10" offset="1" class="user-info-item-title-container">
              <span class="user-info-item-title">
                {{ $t("user.defaultChannel") }}
              </span>
            </Col>
            <Col span="8" offset="2">
              <div class="user-info-item-content">
                <a v-if="myChannelLink" @click="goToMyChannel">
                  {{ user.group_channel_name }}
                </a>
                <span v-else>{{ $t("user.noChannel") }}</span>
              </div>
            </Col>
            <Col span="2" v-if="myGroupChannelList.length > 0">
              <div class="user-info-item-content">
                <Icon
                  type="edit"
                  class="clickable"
                  @click="toggleChannelSelection"
                ></Icon>
              </div>
            </Col>
          </Row>
          <!-- Default Channel Selection -->
          <Row
            v-if="channelSelection.display"
            type="flex"
            justify="center"
            class="user-info-channel-selection-container"
          >
            <Col span="16">
              <cpnt-thumb-selection-list
                v-model="channelSelection.selectedChannelId"
                :options="myGroupChannelList"
                :optionValueKey="'id'"
                :optionLabelKey="'name'"
                :isMobile="isMobileBrowser"
              >
              </cpnt-thumb-selection-list>
            </Col>
            <Col span="4" push="1">
              <Button
                icon="checkmark"
                type="success"
                :disabled="!channelSelection.selectedChannelId"
                :loading="isSetting"
                @click="setUserDefaultChannel"
              >
              </Button>
            </Col>
          </Row>
          <!-- Account Setting -->
          <Row
            type="flex"
            justify="center"
            size="small"
            class="user-info-sub-item-container"
          >
            <Col span="24">
              <Button
                type="info"
                class="user-info-sub-item-content"
                @click="userInfo"
                :loading="isLoading"
              >
                {{ $t("user.accountSetting") }}
              </Button>
            </Col>
          </Row>
        </Col>
      </Row>
    </Card>

    <!-- Sokrates -->
    <Card
      :bordered="false"
      :dis-hover="true"
      class="platform"
      v-if="url.sokratesLink"
    >
      <Row justify="center" class="platform-container">
        <Col span="4" class="platform-logo">
          <img
            src="/images/app/userinfo/sokrates_logo.svg"
            alt="Sokrates Logo"
          />
        </Col>
        <a @click="loginToSokApp">
          <Col span="18" class="platform-link">
            {{ $t("sokratesLink") }}
          </Col>
          <Col span="2">
            <Icon type="chevron-right"></Icon>
          </Col>
        </a>
      </Row>
    </Card>

    <!-- IES 5 -->
    <Card
      :bordered="false"
      :dis-hover="true"
      class="platform"
      v-if="url.iesLink"
    >
      <Row justify="center" class="platform-container">
        <Col span="4" class="platform-logo">
          <img src="/images/app/userinfo/ies_logo.svg" alt="IES Logo" />
        </Col>
        <a @click="loginToIES5">
          <Col span="18" class="platform-link">
            {{ $t("iesLink") }}
          </Col>
          <Col span="2">
            <Icon type="chevron-right"></Icon>
          </Col>
        </a>
      </Row>
    </Card>

    <!-- User Menu -->
    <Card :bordered="false" :dis-hover="true" class="profile-menu">
      <!-- Management -->
      <Row class="profile-menu-container" v-if="hasFullPrivilege">
        <Col span="24" class="profile-menu-content">
          <a @click="goToGlobalManagement">
            <Icon type="person" size="20"></Icon>
            <span class="profile-menu-item">
              {{ $t("globalManagement") }}
            </span>
          </a>
        </Col>
      </Row>
      <!-- Logout -->
      <Row class="profile-menu-container">
        <Col span="24" class="profile-menu-content">
          <a :href="url.logout">
            <Icon type="log-out" size="20"></Icon>
            <span class="profile-menu-item">
              {{ $t("logout") }}
            </span>
          </a>
        </Col>
      </Row>
    </Card>
  </article>
</template>

<script>
import Vuex from "vuex";
import base64 from "hi-base64";

import CpntThumbSelectionList from "../../../assets/exhibition/app/components/thumb-selection-list.vue";

import MemberService from "../../../services/member.js";
import AuthService from "../../../services/auth.js";

export default {
  name: "UserInfoSidebar",
  props: {
    area: {
      type: String,
      required: true,
      default: "exhibition", // exhibition, district
    },
  },
  components: {
    "cpnt-thumb-selection-list": CpntThumbSelectionList,
  },
  data() {
    return {
      memberSrv: null,
      authSrv: null,
      url: {
        sokratesLink: process.env.MIX_SOKAPP_URL,
        iesLink: process.env.MIX_IES5_URL,
        logout: "/auth/login/logout",
      },
      channelSelection: {
        display: false,
        selectedChannelId: null,
      },
      debouncingTimer: null,
      isLoading: false,
      isUpdating: false,
      isSetting: false,
    };
  },
  computed: _.merge(
    Vuex.mapState(["user", "isMobileBrowser"]),
    Vuex.mapGetters(["urlAvatar"]),
    {
      isSidebarOpen() {
        return !this.$store.state.sider.right.collapsed;
      },
      isRootUser() {
        let userRoleData = _.find(this.user.roles, ["type", "Root"]);
        return userRoleData !== undefined;
      },
      myChannelLink() {
        if (!this.user.group_channel_id) return "";
        return "/myChannel/" + this.user.group_channel_id;
      },
      myGroupChannelList() {
        if (this.user.groups.length < 1) return [];

        // Create group channel list from user state
        let groupChannelList = [];
        _.forEach(this.user.groups, (group) => {
          // Only groups whose public is 0
          if (group.public === 0) {
            let channelList = _.map(group.channels, (channel) => {
              return {
                id: channel.id,
                name: channel.name,
                groupId: channel.group_id,
                thumbnail: channel.thumbnail,
              };
            });
            groupChannelList = groupChannelList.concat(channelList);
          }
        });

        // Clear out duplicated channels
        groupChannelList = _.uniqBy(groupChannelList, "id");

        return groupChannelList;
      },
      hasFullPrivilege() {
        return this.area === "exhibition" && this.isRootUser;
      },
      areaFunc() {
        return {
          exhibition: {
            goToMyChannel: () => {
              this.$router.push(this.myChannelLink);
              this.$store.state.sider.right.collapsed = true;
            },
          },
          district: {
            goToMyChannel: () => {
              let baseUrl = window.location.origin;
              let url = this.myChannelLink;
              window.location.href = baseUrl + "/exhibition/tbavideo#" + url;
            },
          },
        };
      },
    }
  ),
  watch: {},
  methods: {
    init() {
      this.memberSrv = MemberService;
      this.authSrv = AuthService;
    },
    userInfo() {
      let url = "/auth/user/info";
      this.isLoading = true;
      axios
        .get(url)
        .then((response) => {
          window.open(
            response.data,
            "_blank",
            "toolbar=yes, width=700, height=700"
          );
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    toggleChannelSelection() {
      this.channelSelection.display = !this.channelSelection.display;
    },
    async setUserDefaultChannel() {
      let updateMemberData = {
        group_channel_id: this.channelSelection.selectedChannelId,
      };
      this.isSetting = true;
      try {
        await this.memberSrv.update(this.user.id, updateMemberData);
        // Update UI
        this.user.group_channel_id = this.channelSelection.selectedChannelId;
        this.user.group_channel_name = _.find(this.myGroupChannelList, [
          "id",
          parseInt(this.channelSelection.selectedChannelId),
        ]).name;
        this.channelSelection.display = false;
        this.$Notice.success({ title: this.$t("messages.success") });
      } catch (e) {
        this.$Notice.error({ title: this.$t("messages.error") });
      } finally {
        this.isSetting = false;
      }
    },
    async loginToSokApp() {
      await this.authSrv
        .getSokAppLoginUrl()
        .then((data) => {
          if (!data.status) throw new Error(data.msg);
          let url = data.url;
          window.open(url, "_blank");
        })
        .catch((e) => {
          console.log(e);
        });
    },
    async loginToIES5() {
      await this.authSrv
        .getIES5LoginUrl()
        .then((data) => {
          if (!data.status) throw new Error(data.msg);
          let url = data.url;
          window.open(url, "_blank");
        })
        .catch((e) => {
          console.log(e);
        });
    },
    async getTicket() {
      let ticket = "";
      await axios.get("/getTicket").then((response) => {
        ticket = response.data;
      });
      return ticket;
    },
    async goToGlobalManagement() {
      let ticket = await this.getTicket();
      if (!ticket) return;
      let userRoleData = _.find(this.user.roles, ["type", "Root"]);
      let globalType = userRoleData ? base64.encode(userRoleData.type) : null;
      window.open(
        `${process.env.MIX_APP_ADMIN_URL}?global=${globalType}&ticket=${ticket}`
      );
    },
    goToMyChannel() {
      this.areaFunc[this.area].goToMyChannel();
    },
  },
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.user-info {
  border: 10px solid transparent;
  border-radius: 25px;

  .profile {
    border-bottom: 1px solid #dddee1;

    .user-info-avatar-container {
      text-align: center;
      padding: 5px;
    }

    .user-info-avatar {
      width: 65px;
      height: 65px;
      line-height: 65px;
      border-radius: 65px;
    }

    .user-info-item-container {
      text-align: left;
      padding-top: 6px;

      max-width: 250px;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;

      .user-info-item-title-container {
        text-align: right;

        .user-info-item-title {
          font-size: 14px;
          color: #808080;
        }
      }

      .user-info-item-content {
        font-size: 14px;
        font-weight: bold;

        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;

        .clickable {
          color: #00a8ff;
          cursor: pointer;
        }
      }
    }

    .user-info-channel-selection-container {
      padding-top: 10px;
    }

    .user-info-sub-item-container {
      padding-top: 10px;
      text-align: center;

      .user-info-sub-item-content {
        font-size: 12px;
        font-weight: bold;
      }
    }
  }

  .platform {
    border-bottom: 1px solid #dddee1;

    .platform-container {
      padding: 5px;
      font-size: 12px;

      .platform-updating {
        padding: 10px;
      }

      .platform-logo img {
        height: auto;
        width: 40px;
      }

      .platform-link {
        text-align: center;
        font-weight: bold;
      }

      .platform-arrow {
        text-align: right;
      }

      .platform-qr-code {
        padding: 5px;
        text-align: center;
      }

      .platform-hint {
        color: #808080;
      }
    }
  }

  .profile-menu {
    .profile-menu-container {
      text-align: right;
      padding: 5px;

      .profile-menu-content {
        text-align: left;
        padding-left: 10px;
        font-weight: bold;

        .profile-menu-item {
          padding-left: 10px;
          font-size: 14px;
        }
      }
    }
  }

  .spin-icon-load {
    animation: ani-demo-spin 1s linear infinite;
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
