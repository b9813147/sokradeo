<template>
  <div class="thumb">
    <!-- Video Img and Tag -->
    <div class="video-img-container">
      <!-- Tag -->
      <div v-if="tag.text">
        <Tag :color="tag.color" class="video-tag">{{ tag.text }}</Tag>
      </div>
      <!-- Img -->
      <img :class="videoImgSrcClass" :src="thumbnailImgSrc" @click="handle" />
      <!-- Time Tag -->
      <div v-if="timeTag">
        <time class="time-tag">{{ timeTag }}</time>
      </div>
    </div>

    <!-- Video Info -->
    <div style="position: relative; color: #32c2f2">
      <!-- Name -->
      <p>
        <span v-if="hasGroupChannels">
          {{ item.group_channels[0].name }}
        </span>
        <Tooltip v-if="hasMultipleGroupChannels" placement="right">
          <Icon type="plus" class="icon-border-radius"></Icon>
          <div slot="content">
            <span class="channel-name" v-for="channelName in channelNameList">
              {{ channelName }}
            </span>
          </div>
        </Tooltip>
      </p>
      <div style="position: absolute; right: 0; top: 0; color: #fff">
        <!-- Playlist -->
        <span v-if="item.playlisted === 1">
          <Icon type="ios-list-outline"></Icon>
        </span>
        <!-- Content Status-->
        <span v-if="hasGroupChannels">
          <Icon
            :title="$t('notReady')"
            type="locked"
            style="color: yellow"
            v-if="item.group_channels[0].pivot.content_status === 2"
          ></Icon>
          <Icon
            type="ios-world-outline"
            style="color: rgb(100, 250, 1)"
            v-if="item.group_channels[0].pivot.content_public === 1"
          ></Icon>
        </span>
        <!-- Playlist Count -->
        <span v-if="hasPlaylist">
          {{ item.tba_playlist_tracks.length }}
        </span>
        <!-- Double-green Status -->
        <span v-if="hasStatistics">
          <Icon
            type="ios-circle-filled"
            style="color: rgb(100, 250, 1)"
            v-if="isDoubleGreen"
          ></Icon>
        </span>
        <!-- Views -->
        <span>
          <Icon type="eye"></Icon>
          {{ item.hits }}
        </span>
      </div>
    </div>
    <!-- Video Name -->
    <p style="color: #fff; margin-top: 0.5em">{{ item.name }}</p>
    <!-- Subject and Grade -->
    <p style="color: #858181; margin-top: 0.5em">{{ subject }} - {{ grade }}</p>
    <!-- Teacher and Lecture Date -->
    <div style="color: #64fa01; font-size: 0.875em">
      <span> {{ $t("teacher") }}: {{ teacherName }} </span>
      <span style="float: right">{{ item.lecture_date }}</span>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

import TimeFormatter from "../../../../commons/time-formatter";

export default {
  props: {
    item: Object,
    filter: Object,
    tag: {
      type: Object,
      default: () => {
        return {
          text: "",
          color: "default", // blue, green, red, yellow, default
        };
      },
    },
    isMobile: {
      type: Boolean,
      default: false,
    },
  },
  computed: _.merge(Vuex.mapState(["path"]), {
    thumbnailImgSrc() {
      let src = "/storage/default.png";
      if (this.item.thumbnail)
        src =
          this.path.tba +
          this.item.id +
          "/" +
          this.item.thumbnail +
          "?" +
          Math.random();
      return src;
    },
    videoImgSrcClass() {
      return this.isMobile ? "video-img-src-mobile" : "video-img-src";
    },
    timeTag() {
      // Get rData from videos
      // Ex.) videos[0].resource.vod.rdata
      // And Transform JSON string to Object
      let rData = JSON.parse(
        _.get(this.item, ["videos", "0", "resource", "vod", "rdata"], null)
      );
      if (!_.has(rData, "duration")) return null;

      // Formatting time -> Ex.) seconds -> hh:mm:ss
      let durationSecs = TimeFormatter.formatSecondsToHHMMSS(
        _.get(rData, "duration", null)
      );
      return durationSecs;
    },
    hasPlaylist() {
      return (
        this.item.playlisted === 1 &&
        this.item.hasOwnProperty("tba_playlist_tracks")
      );
    },
    teacherName() {
      if (this.item.teacher !== null) return this.item.teacher;
      if (this.item.hasOwnProperty("user")) return this.item.user.name;
      return null;
    },
    hasGroupChannels() {
      return (
        this.item.hasOwnProperty("group_channels") &&
        this.item.group_channels.length > 0
      );
    },
    hasMultipleGroupChannels() {
      return (
        this.item.hasOwnProperty("group_channels") &&
        this.item.group_channels.length > 1
      );
    },
    channelNameList() {
      if (!this.hasGroupChannels) return [];
      return _.map(this.item.group_channels, "name");
    },
    hasStatistics() {
      return (
        this.item.hasOwnProperty("tba_statistics") &&
        this.item.tba_statistics.length > 0
      );
    },
    isDoubleGreen() {
      if (!this.hasStatistics) return;
      let threshold = 70;
      return (
        this.item.tba_statistics[0].T >= threshold &&
        this.item.tba_statistics[0].P >= threshold
      );
    },
  }),
  watch: {
    item() {
      this.gradeName();
      this.subjectName();
    },
  },
  data() {
    return {
      grade: this.$t("annexes.other"),
      subject: this.$t("annexes.other"),
    };
  },
  methods: {
    init() {
      this.gradeName();
      this.subjectName();
    },
    handle() {
      this.$emit("execute", this.item);
    },
    parseDateTimeToDate(datetime) {
      let date = new Date(datetime);
      return (
        date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate()
      );
    },
    gradeName() {
      let _this = this;
      let group_channel_content = _.isObjectLike(
        _this.item.group_channel_content
      )
        ? _this.item.group_channel_content
        : null;

      if (group_channel_content !== null) {
        if (typeof _this.filter === "object") {
          if (_this.filter.grades === "Other") {
            return (_this.grade =
              group_channel_content.grades_id === null
                ? this.$t("annexes.other")
                : group_channel_content.grade.name);
          } else if (_this.filter.grades === group_channel_content.grades_id) {
            return (_this.grade = group_channel_content.grade
              ? group_channel_content.grade.name
              : this.$t("annexes.other"));
          } else {
            return (_this.grade = group_channel_content.grade
              ? group_channel_content.grade.name
              : this.$t("annexes.other"));
          }
        }
        return (_this.grade = group_channel_content.grade
          ? group_channel_content.grade.name
          : this.$t("annexes.other"));
      }
      return (_this.grade = this.$t("annexes.other"));
    },
    subjectName() {
      let _this = this;
      let group_channel_content = _.isObjectLike(
        _this.item.group_channel_content
      )
        ? _this.item.group_channel_content
        : null;

      if (group_channel_content !== null) {
        if (typeof _this.filter === "object") {
          if (_this.filter.districtSubjectFields === "Other") {
            return (_this.subject =
              group_channel_content.group_subject_fields_id === null
                ? this.$t("annexes.other")
                : group_channel_content.group_subject_fields.subject);
          } else if (
            _this.filter.districtSubjectFields ===
            group_channel_content.group_subject_fields_id
          ) {
            return (_this.subject = group_channel_content.group_subject_fields
              ? group_channel_content.group_subject_fields.subject
              : this.$t("annexes.other"));
          } else {
            return (_this.subject = group_channel_content.group_subject_fields
              ? group_channel_content.group_subject_fields.subject
              : this.$t("annexes.other"));
          }
        }
        return (_this.subject = group_channel_content.group_subject_fields
          ? group_channel_content.group_subject_fields.subject
          : this.$t("annexes.other"));
      }
      return (_this.subject = this.$t("annexes.other"));
    },
  },
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.thumb {
  height: 100%;
  padding: 10px;

  p {
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    width: 70%;
  }

  .channel-name {
    display: block;
  }

  .video-img-container {
    position: relative;

    .video-tag {
      position: absolute;
      right: 10px;
      top: 10px;
    }

    .video-img-src {
      width: 100%;
      object-fit: contain;
    }

    .video-img-src-mobile {
      width: 100%;
      height: 150px;
      max-height: fit-content;
      object-fit: cover;
    }

    .time-tag {
      position: absolute;
      right: 10px;
      bottom: 15px;

      font-size: 12px;
      background: rgba(12, 12, 12, 0.7);
      color: #ffffff;
      padding: 2px;
    }
  }

  .icon-border-radius {
    padding: 2px;
    border-style: solid;
    border-width: 1px;
    border-radius: 2px;
  }
}
</style>
