<template>
  <article class="activity-channel-container">
    <!-- Activity Information and Status -->
    <section class="activity-info-status">
      <cpnt-thumb-info-status
        :group="group"
        :eventType="curEventType"
        :activeEventStageKey="activeEventStageKey"
      >
      </cpnt-thumb-info-status>
    </section>

    <!-- Submission Area -->
    <section class="channel-submission-area" ref="channel-submission-area">
      <hr />
      <!-- Activity Type -->
      <cpnt-thumb-activity-submission-area
        v-if="curEventType === 'activity'"
        :group="group"
        :activeEventStageKey="activeEventStageKey"
        :categoricalChoices="categoricalChoices"
        :subjectChoices="subjectChoices"
        :eventState="eventState"
        :selected="selected"
        :instructionalHint="instructionalHint"
        @clear-selected-video="clearSelectedVideo"
        @submit-selected-video="submitVideo"
        @update-user-id-list="getUserIdList"
        @update-allowed-selection-status="updateAllowedSelectionStatus"
      >
      </cpnt-thumb-activity-submission-area>

      <!-- Training Type -->
      <cpnt-thumb-training-submission-area
        v-else-if="curEventType === 'training'"
        :group="group"
        :activeEventStageKey="activeEventStageKey"
        :categoricalChoices="categoricalChoices"
        :taskChoices="subjectChoices"
        :eventState="eventState"
        :selected="selected"
        :instructionalHint="instructionalHint"
        @clear-selected-video="clearSelectedVideo"
        @submit-selected-video-to-task="submitVideoToTask"
        @update-user-id-list="getUserIdList"
        @update-allowed-selection-status="updateAllowedSelectionStatus"
        @update-disallowed-content-ids="updateDisallowedContentIds"
      >
      </cpnt-thumb-training-submission-area>
    </section>

    <!-- My Eligible Video List -->
    <section class="activity-eligible-video-list" v-if="allowedSubmission">
      <hr />
      <cpnt-thumb-eligible-video-list
        :group="group"
        :eventState="eventState"
        :allowedSelection="allowedSelection"
        :disallowedContentIds="disallowedContentIds"
        @select-video-from-list="addMyVideo"
        @instructional-hint-update="updateInstructionalHint"
      >
      </cpnt-thumb-eligible-video-list>
    </section>
  </article>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";
import CpntInfoStatus from "../../../app/components/activity-channel/thumb-info-status.vue";
import CpntActivitySubmissionArea from "../../../app/components/activity-channel/submission-area/thumb-activity-base.vue";
import CpntTrainingSubmissionArea from "../../../app/components/activity-channel/submission-area/thumb-training-base.vue";
import CpntEligibleVideoList from "../../../app/components/activity-channel/thumb-eligible-video-list.vue";

export default {
  components: {
    "cpnt-thumb-info-status": CpntInfoStatus,
    "cpnt-thumb-activity-submission-area": CpntActivitySubmissionArea,
    "cpnt-thumb-training-submission-area": CpntTrainingSubmissionArea,
    "cpnt-thumb-eligible-video-list": CpntEligibleVideoList,
  },
  data() {
    return {
      // Group and Channel properties
      channelId: this.$route.params.channelId,
      group: {
        groupId: null,
        name: null,
        description: null,
        thumbnail: null,
        abbr: null,
        district: null,
        status: null,
        stage: null,
        eventData: {
          eventType: null,
          startDate: null,
          endDate: null,
          maxParticipant: null,
          stageCount: null,
          enableTrial: null,
          trialDeadline: null,
          eventStage: {},
        },
      },
      // Choices
      categoricalChoices: [], // rating
      subjectChoices: [], // group_subject_fields
      // Privilege
      userIdList: [],
      // Video list properties
      instructionalHint: null,
      // Selected properties
      selected: {
        videoData: null,
        categoryId: null,
        subjectId: null,
      },
      // States
      curEventType: null,
      activeEventStageKey: null,
      curEventData: null,
      eventSelectionAllowed: {
        activity: true,
        training: true,
      },
      disallowedContentIds: [],
    };
  },

  computed: _.merge(Vuex.mapState(["path"]), Vuex.mapGetters(["logined"]), {
    groupId() {
      return this.group.groupId;
    },
    userId() {
      return this.$store.state.user.id;
    },
    eventState() {
      return {
        type: this.curEventType,
        data: this.group.eventData,
        stage: this.eventStateStage,
        header: this.eventStateHeader,
        deadline: this.eventStateDeadline,
        privilege: this.eventStatePrivilege,
        requirement: this.eventStateRequirement,
      };
    },
    eventStateStage() {
      return {
        isRegistration: this.activeEventStageKey === "registration",
        isSubmission: this.activeEventStageKey === "submission",
        isReviewing: this.activeEventStageKey === "reviewing",
        isCertification: this.activeEventStageKey === "certification",
      };
    },
    eventStateHeader() {
      let i18nHeaderString = "activityChannel.activityStatus";
      if (this.curEventType === "activity") i18nHeaderString += ".activity";
      else if (this.curEventType === "training")
        i18nHeaderString += ".training";

      let title = "";
      let subTitle = "";
      if (this.eventStateStage.isRegistration) {
        title = this.$t(i18nHeaderString + ".registration.title");
        subTitle = this.$t(i18nHeaderString + ".registration.subTitle");
      } else if (this.eventStateStage.isSubmission) {
        title = this.$t(i18nHeaderString + ".submission.title");
        subTitle = this.$t(i18nHeaderString + ".submission.subTitle");
      } else if (this.eventStateStage.isReviewing) {
        title = this.$t(i18nHeaderString + ".reviewing.title");
        subTitle = this.$t(i18nHeaderString + ".reviewing.subTitle");
      } else if (this.eventStateStage.isCertification) {
        title = this.$t(i18nHeaderString + ".certification.title");
        subTitle = this.$t(i18nHeaderString + ".certification.subTitle");
      }
      return {
        title: title,
        subTitle: subTitle,
      };
    },
    eventStateDeadline() {
      if (!this.curEventData) return;
      let isOver = false;
      let today = new Date().setHours(0, 0, 0);
      let deadline = this.curEventData.endDate
        ? new Date(this.curEventData.endDate)
        : null;

      // If there's a deadline, check whether it has passed or not
      if (deadline && today > deadline) isOver = true;

      return {
        date: this.curEventData.endDate,
        isOver: isOver,
      };
    },
    eventStatePrivilege() {
      if (!this.userIdList) return;
      let hasUserRegistered = this.userIdList.includes(this.userId);
      return {
        userIdList: this.userIdList,
        hasUserRegistered: hasUserRegistered,
        isMembershipRequired: this.eventStateStage.isSubmission
          ? this.group.eventData.eventStage.submission.isGroupUser
          : false, // for checking during submission
      };
    },
    eventStateRequirement() {
      if (!this.group.eventData.eventStage.submission) return;
      return this.group.eventData.eventStage.submission.requirement;
    },
    allowedSubmission() {
      return this.eventState.stage.isSubmission;
    },
    allowedDeletion() {
      return this.allowedSubmission;
    },
    allowedSelection() {
      let isAllowed = true;

      // Define dynamic rules based on the current event type
      switch (this.curEventType) {
        case "activity":
          isAllowed = this.eventSelectionAllowed["activity"];
          break;
        case "training":
          isAllowed = this.eventSelectionAllowed["training"];
          break;
      }

      // Define general rules
      if (!this.allowedSubmission) isAllowed = false;
      if (this.selected.videoData) isAllowed = false;

      return isAllowed;
    },
  }),

  watch: {
    group: {
      handler(v) {
        // Get eventType
        this.curEventType = v.eventData.eventType.toLowerCase();

        // Find curently active eventStage
        this.activeEventStageKey = this.getActiveEventStageKey(
          v.eventData.eventStage
        );

        // Find currrently active event data
        this.curEventData = v.eventData.eventStage[this.activeEventStageKey];
      },
      deep: true,
    },
  },

  methods: _.merge(
    Vuex.mapActions([]),

    {
      init() {
        // Check login state
        if (!this.logined) {
          this.$emit("check-logined", true, false, false, false);
          return;
        }
        // Get group data, submission choices
        this.getGroupData();
        this.getSubmissionChoices();
      },
      getGroupData() {
        if (!document.cookie) location.reload();
        let channelId = this.channelId;
        let url = "/exhibition/tbavideo/get-channel-info";
        axios
          .get(url, {
            params: {
              channelId: channelId,
            },
          })
          .then((data) => {
            data = data.data;
            // If there is no info about this channel
            if (!data.status || !data.data || !data.data.group.event_data) {
              this.redirectToHomeWithErrorMsg(
                this.$t("activityChannel.msg.missing")
              );
              return;
            }

            // Set up group info
            this.group.groupId = data.data.group_id;
            this.group.name = data.data.name;
            this.group.description = data.data.description;
            this.group.thumbnail = data.data.thumbnail;
            this.group.district = data.data.district_group;
            this.group.status = data.data.status;
            this.group.stage = data.data.stage;
            this.group.eventData = this.transformEventData(
              data.data.group.event_data
            );

            // Initiate getting essential data
            this.getUserIdList();
          })
          .catch((e) => {
            console.log(e);
          });
      },
      transformEventData(rawEventData) {
        let eventData = JSON.parse(rawEventData); // JSON string -> object

        // Sort eventStage ASC
        eventData.eventStage = Object.fromEntries(
          Object.entries(eventData.eventStage).sort(
            ([, a], [, b]) => parseInt(a.stageOrder) - parseInt(b.stageOrder)
          )
        );
        return eventData;
      },
      getActiveEventStageKey(eventStage) {
        // Event is active when group_channels.stage === stageOrder
        let activeEventStageKey = _.findKey(eventStage, (eventStageData) => {
          return (
            parseInt(eventStageData.stageOrder) === parseInt(this.group.stage)
          );
        });
        return activeEventStageKey.toLowerCase();
      },
      getSubmissionChoices() {
        let url = "/exhibition/tbavideo/get-submission-choices";
        axios
          .get(url, {
            params: {
              channel_id: this.channelId,
            },
          })
          .then((data) => {
            data = data.data;
            if (!data.status) return;
            this.categoricalChoices = data.data.ratings;
            this.subjectChoices = data.data.group_subject_fields;
          })
          .catch((e) => {
            console.log(e);
          });
      },
      getUserIdList() {
        let _this = this;
        let url = "/group/" + _this.groupId + "/users";

        axios
          .get(url, {
            params: {},
          })
          .then((data) => {
            data = data.data;
            if (!data.status) return;
            _this.userIdList = data.user_list;
          })
          .catch((e) => {
            console.log(e);
          });
      },
      addMyVideo(videoData) {
        this.selected.videoData = videoData;
        this.selected.subjectId = this.subjectChoices[0].id; // default -> first subject of choices
        this.selected.categoryId = this.categoricalChoices[0].id; // default -> first category of choices
        this.scrollToElementByRef("channel-submission-area"); // focus on submission area when video is added
      },
      updateInstructionalHint(hint) {
        this.instructionalHint = hint;
      },
      clearSelectedVideo() {
        this.selected.videoData = null;
      },
      submitVideo(isSuccessful) {
        if (isSuccessful) this.selected.videoData = null;
      },
      submitVideoToTask(isSuccessful) {
        if (isSuccessful) this.selected.videoData = null;
      },
      updateAllowedSelectionStatus(eventType, isAllowed) {
        this.eventSelectionAllowed[eventType] = isAllowed;
      },
      updateDisallowedContentIds(disallowedContentIds) {
        this.disallowedContentIds = disallowedContentIds;
      },
      redirectToHomeWithErrorMsg(msg) {
        this.$Notice.error({ title: msg });
        this.$router.push({ path: "/" });
      },
      scrollToElementByRef(refName) {
        let el = this.$refs[refName];
        if (el) el.scrollIntoView({ behavior: "smooth" });
      },
    }
  ),
  created() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.activity-channel-container {
  section {
    padding-top: 20px;
  }

  hr {
    opacity: 0.2;
  }
}
</style>