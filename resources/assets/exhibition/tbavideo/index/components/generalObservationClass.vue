<template>
  <Row
    type="flex"
    justify="center"
    align="middle"
    class="general-obsrv-class-container"
    v-if="logined && genObsrvClassAllowed"
  >
    <Col :xs="22" :sm="12" :md="10" :lg="6" class="general-obsrv-class-content">
      <!-- Title -->
      <Row type="flex" justify="center" align="middle">
        <Col span="24" class="general-obsrv-class-title">
          <h2>{{ $t("genObsrvClass.title") }}</h2>
          <p
            class="general-obsrv-class-subtitle highlight"
            v-if="user.group_channel_name"
          >
            {{ user.group_channel_name }}
          </p>
        </Col>
      </Row>

      <cpnt-thumb-rb-divider />

      <!-- Synchronous Observation -->
      <Row type="flex" justify="center" align="middle">
        <Col span="24" class="general-obsrv-class-sync-container">
          <h3 class="general-obsrv-class-sync-title">
            {{ $t("genObsrvClass.sync.desc") }}
          </h3>
          <router-link
            to="/observation-class"
            :disabled="!obsrvClassAllowed"
            :event="obsrvClassAllowed ? 'click' : ''"
          >
            <img
              class="general-obsrv-class-sync-img"
              :src="sokLogoPath"
              alt="sokrates-obsrv-class-logo"
              :style="!obsrvClassAllowed ? 'filter: grayscale(1)' : ''"
            />
          </router-link>
          <p class="general-obsrv-class-sync-note">
            {{ $t("genObsrvClass.sync.note") }}
          </p>
        </Col>
      </Row>

      <cpnt-thumb-rb-divider />

      <!-- Asynchronous Observation -->
      <Row type="flex" justify="center" align="middle">
        <Col span="24" class="general-obsrv-class-async-container">
          <h3 class="general-obsrv-class-async-title">
            {{ $t("genObsrvClass.async.desc") }}
          </h3>
          <Icon
            type="upload"
            size="70"
            class="general-obsrv-class-async-icon"
          ></Icon>
          <Form id="video-upload" v-if="userGroupChannelData">
            <label for="files">
              {{ $t("genObsrvClass.async.note") }}
            </label>
            <input
              type="file"
              accept="video/*"
              id="videoUploadFile"
              name="videoUploadFile"
              ref="videoUploadFile"
              @change="videoFileHandler"
              multiple
              style="padding-bottom: 5px"
            />
          </Form>
          <div class="general-obsrv-class-async-btn">
            <Button
              icon="upload"
              type="info"
              :loading="loading"
              @click="uploadVideo"
              v-if="videoFile"
            >
              {{ $t("editorVideoModal.upload") }}
            </Button>
          </div>
          <p class="general-obsrv-class-async-note">
            {{ $t("editorVideoModal.remark") }}
          </p>
        </Col>
      </Row>
    </Col>
  </Row>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

import CpntThumbRbDivider from "../../../app/components/thumb-rainbow-divider.vue";

export default {
  name: "generalObservationClass",
  components: {
    "cpnt-thumb-rb-divider": CpntThumbRbDivider,
  },
  computed: _.merge(
    Vuex.mapState(["path", "user"]),
    Vuex.mapGetters([
      "logined",
      "userGroupChannelData",
      "genObsrvClassAllowed",
      "obsrvClassAllowed",
    ])
  ),
  data() {
    return {
      loading: false,
      sokLogoPath: "/images/app/common/sokrates_obsrv_class.svg",
      videoTypesAllowed: ["video/mp4"],
      videoExtsAllowed: ["mp4"],
      videoSizeAllowed: 1610611911, // 1,610,611,911 (B) -> 1.5 (GB)
      videoFile: null,
    };
  },
  methods: {
    videoFileHandler(event) {
      this.videoFile = null;
      let videoFile = event.target.files[0];

      // Check file existence
      if (videoFile === undefined) return;

      // Check video file size
      if (videoFile.size > this.videoSizeAllowed) {
        this.$Message.error(this.$t("editorVideoModal.file_limit"));
        this.$refs.videoUploadFile.value = null;
        return;
      }

      // Check video file extensions and types
      let ext = videoFile.name.split(".").pop().toLowerCase();
      if (
        !this.videoTypesAllowed.includes(videoFile.type) ||
        !this.videoExtsAllowed.includes(ext)
      ) {
        this.$Message.error(this.$t("editorVideoModal.format_error"));
        this.$refs.videoUploadFile.value = null;
        return;
      }

      // Assign video file to data prop
      this.videoFile = videoFile;
    },
    uploadVideo() {
      if (!this.userGroupChannelData || !this.videoFile) return;
      let channelId = this.userGroupChannelData.id;
      let groupId = this.userGroupChannelData.group_id;
      let url = `/tbas/uploadVideo/group/${channelId}`;
      let config = {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      };

      let formData = new FormData();
      formData.append("video", this.videoFile);

      // POST video
      this.$Loading.start();
      this.loading = true;
      axios
        .post(url, formData, config)
        .then((response) => {
          if (response.status !== 200) throw "error";
          if (response.data.status === false) throw "error";
          setTimeout(() => {
            throw "timeout";
          }, 180000); // timeout -> 3 min

          // Successful operations
          this.$Loading.finish();
          this.$Message.info(this.$t("editorVideoModal.success"));

          // 排除畫面鎖定
          setTimeout(() => {
            this.$router.push({
              path: `/content/${response.data.id}`,
              query: {
                groupIds: `${groupId}`,
                channelId: `${channelId}`,
              },
            });
          }, 1800); // timeout -> 3 min
        })
        .catch((error) => {
          console.log(error);
          this.$Loading.error();
          this.$Message.error(this.$t("editorVideoModal.error"));
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
  mounted() {
    if (!this.logined) this.$emit("check-logined", true, false, false, false);
    if (!this.genObsrvClassAllowed) this.$router.push({ path: "/" });
  },
};
</script>

<style lang="scss" scoped>
</style>