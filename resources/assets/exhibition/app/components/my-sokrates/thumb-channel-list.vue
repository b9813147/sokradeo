<template>
  <div class="channel-list-container">
    <!-- Channel list -->
    <div class="channel-list-block" v-if="myChannelList.length > 0">
      <Row type="flex" align="middle">
        <Col
          :xs="12"
          :sm="12"
          :md="6"
          :lg="4"
          v-for="(channel, i) in myChannelList"
          :key="'channel_' + i"
        >
          <!-- execute prop has to be explicited used with 'v-on' -->
          <cpnt-thumb-channel
            :item="channel"
            v-on:execute="goToChannel(channel)"
          ></cpnt-thumb-channel>
        </Col>
      </Row>
    </div>
    <!-- Loading -->
    <div class="channel-list-loading-container" v-if="isLoading">
      <cpnt-thumb-loading></cpnt-thumb-loading>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

import CpntThumbChannel from "../thumb-channel.vue";
import CpntThumbLoading from "../thumb-loading.vue";

export default {
  name: "channel-list",
  components: {
    "cpnt-thumb-channel": CpntThumbChannel,
    "cpnt-thumb-loading": CpntThumbLoading,
  },
  computed: _.merge(
    Vuex.mapState(["path", "user"]),
    Vuex.mapGetters(["logined"])
  ),
  data() {
    return {
      myChannelList: [],
      isLoading: false,
    };
  },
  methods: {
    init() {
      this.getMyChannelList();
    },
    getMyChannelList() {
      this.isLoading = true;
      axios
        .get("/user/tbavideo/get-exhibit-info", {
          params: {
            dataReqd: false,
            totalReqd: false,
          },
        })
        .then((data) => {
          data = data.data;
          if (!data.status) return;
          this.myChannelList = _.values(data.data.channel.excs);
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    goToChannel(channel) {
      this.$router.push({
        path: `/myChannel/${channel.id}`,
      });
    },
  },
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
</style>
