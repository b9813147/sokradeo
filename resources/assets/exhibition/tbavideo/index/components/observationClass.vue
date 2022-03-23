<!--
  Page: Observation Class
  Description: This component is used to display the observation class page
  Author: Nuttaphat (Logan)
  Docs: http://192.168.1.2:10000/legend/sokradeo/wiki/Docs%3A+Observation+Class+Component+and+Services
-->
<template>
  <article class="obsrv-class-container" v-if="allowedToOperate">
    <!-- Control Panel -->
    <Row type="flex" align="middle" justify="center">
      <Col :xs="24" :sm="12" :md="10" :lg="6" class="obsrv-class-content">
        <h3>
          <span @click="debugMode.count++">
            {{ $t("obsrvClass.title") }}
          </span>
          <i-switch v-model="debugMode.enabled" v-if="debugMode.count >= 8">
            <Icon type="android-done" slot="open"></Icon>
            <Icon type="android-close" slot="close"></Icon>
          </i-switch>
        </h3>
        <p class="obsrv-class-content-subtitle" v-if="curChannelName">
          {{ curChannelName }}
        </p>
        <!-- Debug Mode -->
        <div
          class="obsrv-class-content-mode small-text"
          v-if="debugMode.enabled"
        >
          <p>Version: {{ version }}</p>
          <p>WS service URL: {{ url.wsUrl }}</p>
          <p>WS service enabled: {{ wsState.isConnected }}</p>
          <p>curObsrvClassData exists: {{ curObsrvClassData !== null }}</p>
        </div>
        <cpnt-thumb-rb-divider />
        <!-- Setting Mode -->
        <div class="obsrv-class-content-mode" v-if="mode.setting">
          <!-- Inputs -->
          <!-- Channel Logo Thumbnail -->
          <Row
            class="obsrv-class-content-mode-item hidden"
            v-if="inputModel.channelId && curChannelImg"
          >
            <Col span="24" class="obsrv-class-content-mode-item-img-container">
              <Img
                :src="curChannelImg"
                :alt="'thumb-channel-logo' + '?' + inputModel.channelId"
                class="obsrv-class-content-mode-item-img"
              />
            </Col>
          </Row>
          <!-- Channel (currently hidden) -->
          <Row class="obsrv-class-content-mode-item hidden">
            <Col span="10">{{ $t("obsrvClass.input.channel") }}</Col>
            <Col span="14">
              <select
                v-model="inputModel.channelId"
                :disabled="true"
                @on-change="refreshDependencies"
              >
                <option
                  v-for="channel in choice.channels"
                  :key="channel.id"
                  :value="channel.id"
                  :disabled="!channel.uploadStatus"
                  class="obsrv-class-content-mode-item-option"
                >
                  {{ channel.name }}
                </option>
              </select>
            </Col>
          </Row>
          <!-- Lesson Name -->
          <Row class="obsrv-class-content-mode-item">
            <Col span="10">{{ $t("obsrvClass.input.lessonName") }}</Col>
            <Col span="14">
              <Input
                v-model="inputModel.lessonName"
                :placeholder="$t('obsrvClass.input.lessonName')"
                type="textarea"
              ></Input>
            </Col>
          </Row>
          <!-- Lesson Sharing -->
          <Row class="obsrv-class-content-mode-item">
            <Col span="10">{{ $t("obsrvClass.input.lessonSharing") }}</Col>
            <Col span="14" v-if="choice.lessonSharing.length > 0">
              <RadioGroup
                v-model="inputModel.lessonSharing"
                class="obsrv-class-content-mode-item-radio"
              >
                <Radio
                  v-for="choice in choice.lessonSharing"
                  :key="choice.value"
                  :label="choice.value"
                >
                  {{ choice.label }}
                </Radio>
              </RadioGroup>
            </Col>
          </Row>
          <!-- Classification -->
          <Row class="obsrv-class-content-mode-item">
            <Col span="10">{{ $t("obsrvClass.input.classification") }}</Col>
            <Col span="14">
              <cpnt-thumb-selection-list
                v-model="inputModel.classificationId"
                :options="choice.classification"
                :optionValueKey="'id'"
                :optionLabelKey="'name'"
                :isMobile="isMobileBrowser"
              >
              </cpnt-thumb-selection-list>
            </Col>
          </Row>
          <!-- Subject -->
          <Row class="obsrv-class-content-mode-item">
            <Col span="10">{{ $t("obsrvClass.input.subject") }}</Col>
            <Col span="14">
              <cpnt-thumb-selection-list
                v-model="inputModel.subjectId"
                :options="choice.subjects"
                :optionValueKey="'id'"
                :optionLabelKey="'alias'"
                :isMobile="isMobileBrowser"
              >
              </cpnt-thumb-selection-list>
            </Col>
          </Row>
          <!-- Grade -->
          <Row class="obsrv-class-content-mode-item">
            <Col span="10">{{ $t("obsrvClass.input.grade") }}</Col>
            <Col span="14">
              <cpnt-thumb-selection-list
                v-model="inputModel.grade"
                :options="choice.grades"
                :isMobile="isMobileBrowser"
              >
              </cpnt-thumb-selection-list>
            </Col>
          </Row>
          <!-- Duration -->
          <Row class="obsrv-class-content-mode-item">
            <Col span="10">{{ $t("obsrvClass.input.duration") }}</Col>
            <Col span="14">
              <cpnt-thumb-selection-list
                v-model="inputModel.duration"
                :options="choice.durations"
                :optionValueKey="'value'"
                :optionLabelKey="'label'"
                :isMobile="isMobileBrowser"
              >
              </cpnt-thumb-selection-list>
            </Col>
          </Row>
          <!-- Operator(s) -->
          <Row>
            <Col span="24" class="obsrv-class-content-mode-operator">
              <Button
                type="primary"
                size="large"
                :disabled="!readyToConfirmSetup"
                :loading="isLoading"
                @click="confirmSetup"
              >
                {{ $t("obsrvClass.btn.setup") }}
              </Button>
            </Col>
          </Row>
        </div>
        <!-- Standby && Starting && Ended Modes -->
        <!-- This section consists a lof of conditional rendering -->
        <div
          class="obsrv-class-content-mode"
          v-else-if="mode.standby || mode.starting || mode.ended"
        >
          <!-- Presentational Inputs -->
          <Row
            class="obsrv-class-content-mode-item"
            v-for="(preModel, k) in presentationalModelList"
            v-show="preModel.display"
            :key="k"
          >
            <Col span="10">{{ preModel.label }}</Col>
            <Col span="14">
              <Input :value="preModel.value" readonly></Input>
            </Col>
          </Row>
          <!-- Content Extension Controller -->
          <Row class="obsrv-class-content-mode-item">
            <Col span="24">
              <div
                class="slider-toggler"
                @click="displayExtContent = !displayExtContent"
              >
                <Icon
                  :type="displayExtContent ? 'chevron-up' : 'chevron-down'"
                  class="slider-toggler-icon"
                ></Icon>
              </div>
            </Col>
          </Row>
          <!-- Timer -->
          <div class="obsrv-class-content-mode-timer">
            <Row class="obsrv-class-content-mode-timer-item">
              <Col span="10">{{ $t("obsrvClass.msg.startTime") }}</Col>
              <Col span="14">
                <span>{{ classStartDatetimeString }}</span>
              </Col>
            </Row>
            <Row class="obsrv-class-content-mode-timer-item">
              <Col span="10">{{ $t("obsrvClass.msg.elapsedTime") }}</Col>
              <Col
                span="14"
                class="obsrv-class-content-mode-timer-counter"
                :style="{ color: counterColor }"
              >
                <span v-if="curObsrvClassData">
                  {{ curTimerHHMMSS }} /
                  {{ fmtSecondsToHHMMSS(curObsrvClassData.duration) }}
                  <Icon
                    v-if="readyToAddExtraTime"
                    type="android-time"
                    class="obsrv-class-content-mode-timer-counter-icon"
                    @click="addExtraTime(extraTime)"
                  ></Icon>
                </span>
              </Col>
            </Row>
            <Row class="obsrv-class-content-mode-timer-item">
              <Col span="10">{{ $t("obsrvClass.msg.endTime") }}</Col>
              <Col span="14">
                <span> {{ classEndDatetimeString }}</span>
              </Col>
            </Row>
            <!-- Status -->
            <Row
              class="obsrv-class-content-mode-timer-item"
              v-if="mode.starting || mode.ended"
            >
              <Col span="10">{{ $t("obsrvClass.msg.obsrvNumber") }}</Col>
              <Col span="14">
                <span class="highlight-text">{{ obsrvCount }}</span>
              </Col>
            </Row>
          </div>

          <!-- QR Code and Joining & PIN Number -->
          <Row
            class="obsrv-class-content-mode-extra"
            v-if="mode.standby || mode.starting"
          >
            <Col span="12" v-if="sokAppUrlForQR">
              <qrcode-vue :value="sokAppUrlForQR" size="130" level="M" />
            </Col>
            <Col span="12">
              <Row>
                <Col span="24">
                  <p>
                    {{ $t("obsrvClass.msg.classNumber") }}
                  </p>
                  <p class="big-bold-text">
                    {{ curObsrvClassData.classroom_code }}
                  </p>
                </Col>
              </Row>
              <Row>
                <Col span="24">
                  <p>
                    {{ $t("obsrvClass.msg.pinNumber") }}
                  </p>
                  <p class="big-bold-text">{{ curObsrvClassData.pin_code }}</p>
                </Col>
              </Row>
            </Col>
          </Row>
          <!-- SokAPP Link -->
          <Row
            class="obsrv-class-content-mode-extra-small"
            align="middle"
            v-if="url.sokratesLink"
          >
            <Col span="24">
              <p class="clickable-text">
                <a class="medium-text" @click="loginToSokApp">
                  {{ url.sokratesLink }}
                </a>
                <Icon
                  type="ios-copy-outline"
                  size="24"
                  @click="copyToClipboard(clipboardContent)"
                  style="padding-left: 10px"
                ></Icon>
              </p>
            </Col>
          </Row>
          <!-- Operator(s) -->
          <Row type="flex" align="middle" justify="center">
            <div v-if="mode.standby">
              <Col span="24" class="obsrv-class-content-mode-operator">
                <Button
                  type="success"
                  icon="play"
                  size="large"
                  @click="startObsrvClass"
                  :loading="isLoading"
                  :disabled="!readyToStartObsrvClass"
                  long
                >
                  {{ $t("obsrvClass.btn.start") }}
                </Button>
                <p class="hint-text small-text">
                  {{ $t("obsrvClass.label.startHint") }}
                </p>
              </Col>
            </div>
            <div v-else-if="mode.starting || mode.ended">
              <Col span="24" class="obsrv-class-content-mode-operator">
                <Button
                  type="error"
                  icon="stop"
                  @click="endObsrvClassModal = true"
                  :disabled="!readyToEndObsrvClass"
                  long
                >
                  {{ $t("obsrvClass.btn.finish") }}
                </Button>
              </Col>
            </div>
          </Row>
        </div>
        <!-- Resuming (overlay) -->
        <div class="obsrv-class-content-overlay" v-show="isLoading">
          <cpnt-thumb-loading
            class="obsrv-class-content-overlay-content"
          ></cpnt-thumb-loading>
        </div>
      </Col>
    </Row>
    <!-- End Obsrv Class Modal -->
    <Modal v-model="endObsrvClassModal" width="360">
      <p slot="header" style="color: #f60; text-align: center">
        <Icon type="information-circled"></Icon>
        <span>{{ $t("obsrvClass.msg.confirmation") }}</span>
      </p>
      <div style="text-align: center">
        <p>{{ $t("obsrvClass.msg.classEndConfirmation") }}</p>
      </div>
      <div slot="footer">
        <Button
          type="error"
          size="large"
          icon="stop"
          :loading="isLoading"
          @click="endObsrvClass"
          long
        >
          {{ $t("obsrvClass.btn.finish") }}
        </Button>
      </div>
    </Modal>
  </article>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

import QrcodeVue from "qrcode.vue";
import VueClipboard from "vue-clipboard2";

import CpntThumbRbDivider from "../../../../../assets/exhibition/app/components/thumb-rainbow-divider.vue";
import CpntThumbLoading from "../../../../../assets/exhibition/app/components/thumb-loading.vue";
import CpntThumbSelectionList from "../../../../../assets/exhibition/app/components/thumb-selection-list.vue";

import Base64Utils from "../../../../../commons/base64.js";
import TimeFormatterUtil from "../../../../../commons/time-formatter.js";
import AuthService from "../../../../../services/auth.js";
import ChannelService from "../../../../../services/channel.js";
import ObsrvClassService from "../../../../../services/obsrv-class.js";
import ObsrvClassWsService from "../../../../../services/obsrv-class-ws.js";

Vue.use(VueClipboard);

export default {
  name: "observation-class",
  components: {
    "cpnt-thumb-rb-divider": CpntThumbRbDivider,
    "cpnt-thumb-loading": CpntThumbLoading,
    "cpnt-thumb-selection-list": CpntThumbSelectionList,
    "qrcode-vue": QrcodeVue,
  },
  computed: _.merge(
    Vuex.mapState(["path", "user", "isMobileBrowser"]),
    Vuex.mapGetters(["logined", "obsrvClassAllowed"]),
    {
      allowedToOperate() {
        // This combines all rules to detemine whether
        // a user is allowed to operate this component or not
        return this.logined && this.user && this.obsrvClassAllowed;
      },
      userId() {
        return this.user.id;
      },
      userGroupChannelList() {
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
                desc: channel.description,
                groupId: channel.group_id,
                thumbnail: channel.thumbnail,
                uploadStatus: group.school_upload_status,
              };
            });
            groupChannelList = groupChannelList.concat(channelList);
          }
        });

        // Clear out duplicated channels
        groupChannelList = _.uniqBy(groupChannelList, "id");

        return groupChannelList;
      },
      curMode() {
        let activeModeKey = null;
        _.each(this.mode, (v, k) => {
          if (v) activeModeKey = k;
        });
        return activeModeKey;
      },
      curChannelId() {
        return this.inputModel.channelId;
      },
      curChannelName() {
        let data = _.find(this.choice.channels, {
          id: this.inputModel.channelId,
        });
        return data ? data.name : "";
      },
      curChannelImg() {
        let url = "/images/app/tw/teammodel/original-black-small.png";
        let data = _.find(this.choice.channels, {
          id: this.inputModel.channelId,
        });
        if (data && data.thumbnail)
          url =
            this.path.groupChannel +
            data.id +
            "/" +
            data.thumbnail +
            "?" +
            Math.random();
        return url;
      },
      curTimerHHMMSS() {
        return this.fmtSecondsToHHMMSS(this.curTimer);
      },
      classStartDatetimeString() {
        return this.getLocaleTimeStringFromTimestamp(this.classStartTimestamp);
      },
      classEndDatetimeString() {
        return this.getLocaleTimeStringFromTimestamp(this.classEndTimestamp);
      },
      generalObsrvAuthStatus() {
        let label = this.generalObsrvAuthEnabled
          ? this.$t("obsrvClass.msg.on")
          : this.$t("obsrvClass.msg.off");
        let color = this.generalObsrvAuthEnabled ? "#19be6b" : "#f64747";
        let slot = this.generalObsrvAuthEnabled ? "open" : "close";
        return {
          label: label,
          color: color,
          slot: slot,
        };
      },
      settingInputModel() {
        return {
          userId: this.userId,
          channelId: this.inputModel.channelId,
          lessonName: this.inputModel.lessonName,
          lessonSharing: this.inputModel.lessonSharing,
          classificationId: this.inputModel.classificationId,
          subjectId: this.inputModel.subjectId,
          grade: this.inputModel.grade,
          duration: this.inputModel.duration,
        };
      },
      readyToConfirmSetup() {
        return (
          this.curMode === "setting" &&
          this.settingInputModel.userId &&
          this.settingInputModel.channelId &&
          this.settingInputModel.lessonName &&
          this.settingInputModel.lessonSharing &&
          this.settingInputModel.classificationId &&
          this.settingInputModel.subjectId &&
          this.settingInputModel.grade &&
          this.settingInputModel.duration &&
          this.wsState.isConnected
        );
      },
      standbyPresentationModel() {
        // Some of the choices depent on API calls
        // Therefore, we need to wait for the API calls to finish
        // and then we can show the presentation
        // while waiting, we can give empty string to the presentation
        return {
          channelName: this.curObsrvClassData.group_channel
            ? this.curObsrvClassData.group_channel.name
            : "",
          lessonName: this.curObsrvClassData.name || "",
          teacherName: this.curObsrvClassData.teacher || "",
          lessonSharing: this.curObsrvClassData.content_public
            ? _.find(this.choice.lessonSharing, {
                contentPublicTypeValue: this.curObsrvClassData.content_public,
              }).label
            : "",
          classification: this.curObsrvClassData.rating
            ? this.curObsrvClassData.rating.name
            : "",
          subject: this.curObsrvClassData.group_subject_field
            ? this.curObsrvClassData.group_subject_field.alias
            : "",
          grade: this.curObsrvClassData.grade_id || "",
        };
      },
      presentationalModelList() {
        // A list of inputs that are presented to the user (READ-ONLY)
        // This will be rendered with v-for and display conditionally
        return [
          {
            label: this.$t("obsrvClass.input.channel"),
            value: this.standbyPresentationModel.channelName,
            display: false,
          },
          {
            label: this.$t("obsrvClass.input.lessonName"),
            value: this.standbyPresentationModel.lessonName,
            display: true,
          },
          {
            label: this.$t("obsrvClass.input.teacher"),
            value: this.standbyPresentationModel.teacherName,
            display: true,
          },
          {
            label: this.$t("obsrvClass.input.lessonSharing"),
            value: this.standbyPresentationModel.lessonSharing,
            display: this.displayExtContent,
          },
          {
            label: this.$t("obsrvClass.input.classification"),
            value: this.standbyPresentationModel.classification,
            display: this.displayExtContent,
          },
          {
            label: this.$t("obsrvClass.input.subject"),
            value: this.standbyPresentationModel.subject,
            display: this.displayExtContent,
          },
          {
            label: this.$t("obsrvClass.input.grade"),
            value: this.standbyPresentationModel.grade,
            display: this.displayExtContent,
          },
        ];
      },
      readyToStartObsrvClass() {
        return (
          this.curMode === "standby" &&
          this.curObsrvClassData &&
          this.wsState.isConnected
        );
      },
      readyToAddExtraTime() {
        return (
          this.curMode === "starting" &&
          this.curObsrvClassData &&
          this.wsState.isConnected
        );
      },
      readyToEndObsrvClass() {
        return (
          this.curMode === "starting" &&
          this.curObsrvClassData &&
          this.wsState.isConnected
        );
      },
      encodedClassroomData() {
        // Base64 encoded classroom data
        if (
          !this.curObsrvClassData ||
          !this.curObsrvClassData.classroom_code ||
          !this.curObsrvClassData.pin_code
        )
          return "";
        return Base64Utils.encodeUrl(
          JSON.stringify({
            gn: this.curObsrvClassData.classroom_code,
            pin: this.curObsrvClassData.pin_code,
            ip: "127.0.0.1", // required for the API to work
          })
        );
      },
      sokAppUrlForQR() {
        // Create sok app url with base64-encoded URI data
        if (
          !this.curObsrvClassData ||
          !this.curObsrvClassData.classroom_code ||
          !this.curObsrvClassData.pin_code
        )
          return "";
        return this.url.sokratesLink + "?s=" + this.encodedClassroomData;
      },
      clipboardContent() {
        // Create the content for clipboard
        // Remove whitespace on newline with regex
        return `${this.$t("obsrvClass.label.sokAppInvitation")}

        ${this.$t("obsrvClass.input.channel")}: ${
          this.standbyPresentationModel.channelName
        }
        ${this.$t("obsrvClass.input.grade")}: ${
          this.standbyPresentationModel.grade
        }
        ${this.$t("obsrvClass.input.lessonName")}: ${
          this.standbyPresentationModel.lessonName
        }
        ${this.$t("obsrvClass.input.teacher")}: ${
          this.standbyPresentationModel.teacherName
        }

        ${this.$t("obsrvClass.label.sokAppJoining")}
        ${this.sokAppUrlForQR}

        ${this.$t("obsrvClass.label.sokAppUrlJoining")}
        ${this.$t("obsrvClass.label.url")}: ${this.url.sokratesLink}
        ${this.$t("obsrvClass.msg.classNumber")}: ${
          this.curObsrvClassData.classroom_code
        }
        ${this.$t("obsrvClass.msg.pinNumber")}: ${
          this.curObsrvClassData.pin_code
        }`.replace(/^[^\S\r\n]+|[^\S\r\n]+$/gm, "");
      },
      counterColor() {
        // Return the color of the timer
        // based on its status
        let green = "#19be6b";
        let yellow = "#f90";
        let red = "#fa5661";
        let color = green;

        if (
          this.curTimer >= this.curObsrvClassData.duration ||
          this.mode.ended
        ) {
          color = red;
        } else if (
          this.curObsrvClassData.duration - this.curTimer <=
          this.warningTimeThreshold
        ) {
          color = yellow;
        }

        return color;
      },
    }
  ),
  data() {
    return {
      version: "1.0.2",
      service: {
        auth: AuthService,
        channel: ChannelService,
        obsrvClass: ObsrvClassService,
        obsrvClassWs: null,
      },
      debugMode: {
        enabled: false,
        count: 0,
      },
      wsState: {
        isConnected: false,
        intervalFunc: null,
      },
      allowedWsMethods: ["GET", "POST", "PUT"],
      allowedWsActions: ["END", "START", "ET"],
      url: {
        sokratesLink: process.env.MIX_SOKAPP_URL,
        wsUrl: process.env.MIX_WS_URL,
      },
      isLoading: false,
      mode: {
        setting: true,
        standby: false,
        starting: false,
        ended: false,
      },
      modeKey: {
        standby: "R",
        starting: "S",
        ended: "E",
      },
      remainingSpace: 0,
      generalObsrvAuthEnabled: true,
      choice: {
        channels: [],
        lessonSharing: [
          {
            label: this.$t("obsrvClass.choiceInput.lessonSharing.private"),
            value: "private",
            contentPublicTypeValue: 4, // Based on ContentPublicType
            icon: "locked",
          },
          {
            label: this.$t("obsrvClass.choiceInput.lessonSharing.public"),
            value: "public",
            contentPublicTypeValue: 2, // Based on ContentPublicType
            icon: "unlocked",
          },
          {
            label: this.$t("obsrvClass.choiceInput.lessonSharing.global"),
            value: "global",
            contentPublicTypeValue: 3, // Based on ContentPublicType
            icon: "android-globe",
          },
        ],
        classification: [],
        subjects: [],
        grades: Array.from({ length: 12 }, (_, i) => i + 1), // 1 ~ 12
        durations: [
          {
            label: `1 ${this.$t("obsrvClass.choiceInput.duration.hour")}`,
            value: 60 * 60, // 3600 seconds
          },
          {
            label: `1.5 ${this.$t("obsrvClass.choiceInput.duration.hour")}`,
            value: 60 * 90, // 5400 seconds
          },
          {
            label: `2 ${this.$t("obsrvClass.choiceInput.duration.hour")}`,
            value: 60 * 120, // 7200 seconds
          },
          {
            label: `2.5 ${this.$t("obsrvClass.choiceInput.duration.hour")}`,
            value: 60 * 150, // 9600 seconds
          },
          {
            label: `3 ${this.$t("obsrvClass.choiceInput.duration.hour")}`,
            value: 60 * 180, // 10800 seconds
          },
        ],
      },
      inputModel: {
        channelId: null,
        lessonName: null,
        lessonSharing: null,
        classificationId: null,
        subjectId: null,
        grade: null,
        duration: null,
      },
      curObsrvClassData: null,
      curTimer: 0,
      curTimerIntervalFunc: null,
      extraTime: 300, // seconds
      warningTimeThreshold: 60, // seconds
      classEndingWarned: false,
      classStartTimestamp: null,
      classEndTimestamp: null,
      obsrvCount: 0,
      commentCount: 0,
      endObsrvClassModal: false,
      displayExtContent: false,
    };
  },
  watch: {
    curChannelId(v) {
      if (!v) return;
      this.getSubmissionChoices();
    },
    curTimer(curSeconds) {
      if (this.isLoading) return;

      // Refresh curTimer every second
      // This has to be done because the timer is not accurate
      // Due to browser's delay
      if (this.classStartTimestamp) {
        this.setOrRefreshCurTimer(this.classStartTimestamp);
      }

      // Get data from websocket every 10 seconds
      if (curSeconds % 10 === 0) {
        this.getObsrvDataFromWs();
      }

      // Warning msg before the class ends (60 seconds)
      if (
        this.curObsrvClassData &&
        this.curObsrvClassData.duration - curSeconds <=
          this.warningTimeThreshold &&
        !this.classEndingWarned
      ) {
        this.$Notice.warning({
          title: this.$t("obsrvClass.msg.classEnding"),
        });
        this.classEndingWarned = true;
      }

      // End class if the deadline is met
      if (
        this.curObsrvClassData &&
        curSeconds >= this.curObsrvClassData.duration
      ) {
        this.endObsrvClass();
      }
    },
  },
  methods: {
    init() {
      // Check auth
      if (!this.logined) {
        this.$emit("check-logined", true, false, false, false);
        return;
      }

      // Check if the user is allowed to use this component
      if (!this.allowedToOperate) {
        this.$router.push({ name: "home" });
        return;
      }

      // Set up WS
      this.setupWs().then(() => {
        this.setupWsReconnection();
      });

      // Set up data props (in-app)
      this.getChannels();
      this.setupDefaultInputModel();

      // Set up data props (API)
      this.resumeObsrvClass();
    },
    setupDefaultInputModel() {
      // Set default channelId
      // If user has default channel, and its upload status === 1
      if (this.choice.channels.length > 0 && this.user.group_channel_id) {
        let groupChannelData =
          _.find(this.userGroupChannelList, {
            id: this.user.group_channel_id,
            uploadStatus: 1,
          }) || {};
        this.inputModel.channelId = groupChannelData.id || null;
      }

      // Set default lessonName
      this.inputModel.lessonName =
        this.user.name + this.$t("obsrvClass.label.lessonExample");

      // Set default lessonSharing
      if (this.choice.lessonSharing.length > 1) {
        this.inputModel.lessonSharing = this.choice.lessonSharing[1].value;
      }

      // Set default grade
      if (this.choice.grades.length > 0) {
        this.inputModel.grade = this.choice.grades[0];
      }

      // Set default duration
      if (this.choice.durations.length > 0) {
        this.inputModel.duration = this.choice.durations[0].value;
      }
    },
    async setupWs() {
      if (!this.url.wsUrl) return;
      try {
        this.service.obsrvClassWs = null;
        this.service.obsrvClassWs = new ObsrvClassWsService(this.url.wsUrl);
        this.service.obsrvClassWs.init();
        this.service.obsrvClassWs.ws.onmessage = (evt) => {
          this.handleWsMsg(evt.data);
        };
        this.setupWsConnectionStatus();
      } catch (e) {
        console.error(e);
      }
    },
    setupWsConnectionStatus() {
      if (!this.service.obsrvClassWs) return;
      this.wsState.isConnected = this.service.obsrvClassWs.isOpen();
    },
    setupWsReconnection() {
      // Check WS connection every 5 seconds
      // If WS is not connected, try to reconnect
      clearInterval(this.wsState.intervalFunc);
      this.wsState.intervalFunc = setInterval(() => {
        this.setupWsConnectionStatus();
        if (!this.wsState.isConnected) {
          this.setupWs();
        }
      }, 5000);
    },
    handleWsMsg(resData) {
      if (!resData) return;
      resData = JSON.parse(resData);

      if (this.debugMode.enabled) {
        console.log("[ObsrvClass] WS msg:", resData);
      }

      let eventType = resData.event.toUpperCase();
      switch (eventType) {
        case "GET":
          if (resData.user_total) this.obsrvCount = resData.user_total;
          if (resData.tba && _.isObject(resData.tba))
            this.redirectToContent(resData.tba);
          break;
        case "POST":
          break;
        case "PUT":
          break;
        default:
          console.log(resData);
          break;
      }
    },
    getObsrvDataFromWs(action = null) {
      this.sendWsMsg("GET", action);
    },
    postObsrvDataToWs(action = null) {
      this.sendWsMsg("POST", action);
    },
    putObsrvDataToWs(action = null) {
      this.sendWsMsg("PUT", action);
    },
    sendWsMsg(method = "GET", action = null) {
      if (!this.wsState.isConnected || !this.curObsrvClassData) return;
      let msgData = {
        id: this.curObsrvClassData.id,
        method: method.toUpperCase(),
        action: this.allowedWsActions.includes(action)
          ? action.toUpperCase()
          : null,
      };
      this.service.obsrvClassWs.sendMsg(msgData, this.debugMode.enabled);
    },
    getChannels() {
      this.choice.channels = this.userGroupChannelList;
    },
    async resumeObsrvClass() {
      this.isLoading = true;
      try {
        // Get API data
        this.getChannels();
        let obsrvClassRes = await this.service.obsrvClass.resumeObsrvClass();
        if (!obsrvClassRes.status) throw obsrvClassRes.data.message;
        this.curObsrvClassData = obsrvClassRes.data;

        // Resume state based on the current obsrv class data
        if (!this.curObsrvClassData) return;
        switch (this.curObsrvClassData.status) {
          case this.modeKey.standby:
            await this.resumeToStandbyMode();
            break;
          case this.modeKey.starting:
            await this.resumeToStartingMode();
            break;
          default:
            break;
        }
      } catch (e) {
        console.error(e);
        this.$Notice.error({
          title: this.$t("obsrvClass.msg.error"),
        });
      } finally {
        this.isLoading = false;
      }
    },
    async resumeToStandbyMode() {
      if (!this.curObsrvClassData) return;

      // Resume the existing to the Standby mode
      this.inputModel.channelId = this.curObsrvClassData.channel_id;
      this.inputModel.lessonName = this.curObsrvClassData.name;
      this.inputModel.lessonSharing = this.getLessonSharingValFromTypeVal(
        this.curObsrvClassData.content_public
      );
      this.inputModel.classificationId = this.curObsrvClassData.rating_id;
      this.inputModel.subjectId = this.curObsrvClassData.group_subject_field_id;
      this.inputModel.grade = this.curObsrvClassData.grade_id;
      this.inputModel.duration = this.curObsrvClassData.duration;

      this.switchToMode("standby");
    },
    async resumeToStartingMode() {
      // Resume the existing to the Starting mode
      this.isLoading = true;
      await this.resumeToStandbyMode()
        .then(() => {
          // Set up UI timer
          this.classStartTimestamp = this.curObsrvClassData.timestamp;
          this.classEndTimestamp =
            this.classStartTimestamp + this.curObsrvClassData.duration;
          this.setOrRefreshCurTimer(this.classStartTimestamp);
          this.curTimerIntervalFunc = setInterval(() => {
            this.curTimer += 1;
          }, 1000);
          this.switchToMode("starting");
        })
        .catch((e) => {
          console.error(e);
          this.$Notice.error({
            title: this.$t("obsrvClass.msg.error"),
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    getLessonSharingValFromTypeVal(contentPublicTypeVal) {
      if (!this.choice.lessonSharing.length > 0) return;
      return _.find(this.choice.lessonSharing, (v) => {
        return v.contentPublicTypeValue === parseInt(contentPublicTypeVal);
      }).value;
    },
    async getSubmissionChoices() {
      if (!this.curChannelId) return;
      await this.service.channel
        .getSubmissionChoices(this.curChannelId)
        .then((res) => {
          if (!res.data.status) return;
          let data = res.data.data;
          this.choice.classification = data.ratings;
          this.choice.subjects = data.group_subject_fields;

          // Set default classification
          if (this.choice.classification.length > 0) {
            this.inputModel.classificationId = this.choice.classification[0].id;
          }

          // Set default subject
          if (this.choice.subjects.length > 0) {
            this.inputModel.subjectId = this.choice.subjects[0].id;
          }
        });
    },
    switchToMode(modeName) {
      // Set all modes to false
      _.each(this.mode, (v, k) => {
        this.mode[k] = false;
      });

      // Switch to mode
      modeName = modeName.toLowerCase() || "setting";
      switch (modeName) {
        case "setting":
          this.mode.setting = true;
          break;
        case "standby":
          this.mode.standby = true;
          break;
        case "starting":
          this.mode.starting = true;
          break;
        case "ended":
          this.mode.ended = true;
          break;
        default:
          this.mode.setting = true;
          break;
      }
    },
    refreshDependencies() {
      if (!this.inputModel.channelId) return;
      this.resetDependentChoicesAndInputs();
      this.getSubmissionChoices();
    },
    resetDependentChoicesAndInputs() {
      // These choices are depent on channelId
      // They need to be reset when channelId is changed
      this.choice.classification = [];
      this.choice.subjects = [];
      this.inputModel.classificationId = null;
      this.inputModel.subjectId = null;
    },
    async loginToSokApp() {
      let res = await this.service.auth.getSokAppLoginUrl();
      if (!res.status) {
        this.$Notice.error({
          title: this.$t("obsrvClass.msg.error"),
        });
        return;
      }
      // Create a link with classroom data and user ticket
      let url = res.url + "&s=" + this.encodedClassroomData;
      window.open(url, "_blank");
    },
    copyToClipboard(content) {
      this.$copyText(content)
        .then(() => {
          this.$Notice.success({
            title: this.$t("obsrvClass.msg.copySuccess"),
          });
        })
        .catch(() => {
          this.$Notice.error({
            title: this.$t("obsrvClass.msg.error"),
          });
        });
    },
    getLocaleTimeStringFromTimestamp(timestamp) {
      // Get 24-hour time string from timestamp
      // Note: convert unix timestamp to milliseconds by * 1000
      if (!timestamp) return "--";
      let locale = navigator.language || navigator.languages[0];
      let digit = "2-digit";
      return new Date(timestamp * 1000).toLocaleTimeString(locale, {
        hour12: false,
        hour: digit,
        minute: digit,
        second: digit,
      });
    },
    async confirmSetup() {
      this.isLoading = true;
      try {
        if (!this.readyToConfirmSetup) return;

        let obsrvClassRes = await this.service.obsrvClass.createObsrvClass(
          this.settingInputModel
        );
        if (!obsrvClassRes.status) throw obsrvClassRes.data.message;
        this.curObsrvClassData = obsrvClassRes.data;
        this.switchToMode("standby");
        this.$Notice.success({
          title: this.$t("obsrvClass.msg.setupSuccess"),
        });
        this.postObsrvDataToWs();
      } catch (e) {
        console.error(e);
        this.$Notice.error({
          title: this.$t("obsrvClass.msg.error"),
        });
      } finally {
        this.isLoading = false;
      }
    },
    fmtSecondsToHHMMSS(seconds) {
      return TimeFormatterUtil.formatSecondsToHHMMSS(seconds);
    },
    setOrRefreshCurTimer(startTimestamp) {
      // Set or Refresh current Timer (seconds)
      let curSysTimestamp = this.getCurSysTimestamp();
      this.curTimer = Math.abs(curSysTimestamp - startTimestamp);
    },
    getCurSysTimestamp() {
      return Math.floor(new Date().getTime() / 1000);
    },
    async startObsrvClass() {
      this.isLoading = true;
      try {
        if (!this.readyToStartObsrvClass) return;

        // Get API data
        let obsrvClassRes = await this.service.obsrvClass.startObsrvClass(
          this.curObsrvClassData.id
        );
        if (!obsrvClassRes.status) throw obsrvClassRes.data.message;
        this.curObsrvClassData = obsrvClassRes.data;

        // Set up UI timer
        this.classStartTimestamp = this.curObsrvClassData.timestamp;
        this.classEndTimestamp =
          this.classStartTimestamp + this.curObsrvClassData.duration;
        this.curTimerIntervalFunc = setInterval(() => {
          this.curTimer += 1;
        }, 1000);

        // Switch to starting mode
        this.switchToMode("starting");
        this.$Notice.success({
          title: this.$t("obsrvClass.msg.classStart"),
        });
        this.putObsrvDataToWs("START");
        this.getObsrvDataFromWs();
      } catch (e) {
        console.error(e);
        this.$Notice.error({
          title: this.$t("obsrvClass.msg.error"),
        });
      } finally {
        this.isLoading = false;
      }
    },
    async addExtraTime(seconds) {
      try {
        if (!this.readyToAddExtraTime) return;

        // Get API data
        let obsrvClassRes = await this.service.obsrvClass.addExtraTime(
          this.curObsrvClassData.id,
          seconds
        );
        if (!obsrvClassRes.status) throw obsrvClassRes.data.message;
        this.curObsrvClassData = obsrvClassRes.data;

        // Update UI timer
        this.classEndTimestamp =
          this.classStartTimestamp + this.curObsrvClassData.duration;
        this.$Notice.info({
          title: `${this.$t("obsrvClass.msg.extraTime")} (${this.extraTime})`,
        });
        this.classEndingWarned = false;
        this.putObsrvDataToWs("ET");
      } catch (e) {
        console.error(e);
        this.$Notice.error({
          title: this.$t("obsrvClass.msg.error"),
        });
      }
    },
    async endObsrvClass() {
      this.isLoading = true;
      try {
        if (!this.readyToEndObsrvClass) return;

        // Get API data
        let obsrvClassRes = await this.service.obsrvClass.endObsrvClass(
          this.curObsrvClassData.id
        );
        if (!obsrvClassRes.status) throw obsrvClassRes.data.message;
        this.curObsrvClassData = obsrvClassRes.data;

        // Stop Timer
        clearInterval(this.curTimerIntervalFunc);

        // Switch to ended mode
        this.switchToMode("ended");
        this.$Notice.info({
          title: this.$t("obsrvClass.msg.classEnd"),
        });
        this.putObsrvDataToWs("END");
        this.getObsrvDataFromWs();
      } catch (e) {
        console.error(e);
        this.$Notice.error({
          title: this.$t("obsrvClass.msg.error"),
        });
      } finally {
        this.isLoading = false;
        this.endObsrvClassModal = false;
      }
    },
    redirectToContent(tbaData) {
      // Redirect to content page
      if (!tbaData || !this.curObsrvClassData) return;
      setTimeout(() => {
        this.$router.push({
          name: "content",
          params: { contentId: tbaData.id },
          query: {
            groupIds: _.join([this.curObsrvClassData.group_id], ","),
            channelId: _.join([this.curObsrvClassData.channel_id], ","),
          },
        });
      }, 3000);
    },
  },
  mounted() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
</style>
