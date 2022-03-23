<template>
  <div class="submission-area-container">
    <!-- Detail -->
    <Row>
      <Col span="18">
        <!-- Header and Deadline -->
        <h1>
          {{ eventState.header.title }}
          <span v-if="eventState.deadline.date">
            ({{ $t("activityChannel.deadline") }} :
            {{ eventState.deadline.date }})
          </span>
        </h1>
        <!-- Sub-title -->
        <p v-if="eventState.header.subTitle">
          {{ eventState.header.subTitle }}
        </p>
        <!-- Submission Status -->
        <p v-if="eventState.stage.isSubmission || eventState.stage.isReviewing">
          {{ $t("activityChannel.submissionStatus") }} :
          <span v-if="submittedVideoData">
            {{ $t("activityChannel.userStatus.submitted") }}
            <Icon type="android-checkmark-circle" style="color: #00ff7f"></Icon>
          </span>
          <span v-else>
            {{ $t("activityChannel.userStatus.empty") }}
            <Icon type="alert-circled" style="color: #fad859"></Icon>
          </span>
        </p>
      </Col>
    </Row>
    <!-- Block -->
    <Row class="submission-block">
      <!-- Registration Stage -->
      <Col
        span="24"
        v-if="eventState.stage.isRegistration && !eventState.deadline.isOver"
      >
        <cpnt-thumb-registration
          :group="group"
          :eventState="eventState"
          :userIdList="eventState.privilege.userIdList"
          @update-user-id-list="updateUserIdList"
        >
        </cpnt-thumb-registration>
      </Col>

      <!-- Submission Stage -->
      <Col
        span="24"
        v-else-if="
          (eventState.stage.isSubmission || eventState.stage.isReviewing) &&
            !eventState.deadline.isOver
        "
      >
        <!-- Searching -->
        <Col class="spin-col" span="24" v-if="isSearching">
          <Row
            type="flex"
            justify="center"
            align="middle"
            class="submission-area"
          >
            <Spin fix>
              <Icon type="load-c" size="large" class="spin-icon-load"></Icon>
              <h4 style="color: #ffffff">
                {{ $t("activityChannel.searching") }}
              </h4>
            </Spin>
          </Row>
        </Col>
        <!-- Submitted -->
        <Col span="24" v-else-if="submittedVideoData">
          <Row
            type="flex"
            justify="center"
            align="middle"
            class="submission-area"
          >
            <!-- Video Data -->
            <Col :xs="22" :sm="18" :md="14" :lg="10">
              <Card
                :bordered="false"
                style="width: 320px; background-color: #1b1b1b"
              >
                <div class="my-video-card" style="text-align: left">
                  <div
                    class="my-video-card-delete-icon"
                    v-if="eventState.stage.isSubmission"
                  >
                    <Icon type="trash-a" @click="openModalDeletion"></Icon>
                  </div>
                  <cpnt-thumb-content
                    :item="submittedVideoData"
                  ></cpnt-thumb-content>
                </div>
              </Card>
            </Col>
            <!-- Video Catergory (Type) & Subject (Class) -->
            <Col
              :xs="18"
              :sm="14"
              :md="10"
              :lg="6"
              type="flex"
              justify="center"
            >
              <!-- Type -->
              <Row
                v-if="submittedVideoType"
                style="padding: 10px; text-align: left"
              >
                <Col span="24">
                  <h4>
                    {{ $t("activityChannel.type") }} :
                    <span class="text-underline">{{ submittedVideoType }}</span>
                  </h4>
                </Col>
              </Row>
              <!-- Class -->
              <Row
                v-if="submittedVideoClass"
                style="padding: 10px; text-align: left"
              >
                <Col span="24">
                  <h4>
                    {{ $t("activityChannel.class") }} :
                    <span class="text-underline">{{
                      submittedVideoClass
                    }}</span>
                  </h4>
                </Col>
              </Row>
            </Col>
          </Row>
        </Col>
        <!-- Submission Block Empty -->
        <Col span="24" v-else-if="!selected.videoData">
          <Row class="submission-area">
            <Card style="background-color: transparent; text-align: center">
              <Icon
                type="social-dropbox-outline"
                style="font-size: 3rem"
              ></Icon>
              <div>
                <h4>{{ $t("activityChannel.instruction.title") }}</h4>
                <span v-if="instructionalHint" style="font-size: 1rem">{{
                  instructionalHint
                }}</span>
              </div>
            </Card>
          </Row>
        </Col>
        <!-- Submission Block Selected -->
        <Col span="24" v-else-if="selected.videoData">
          <Row
            type="flex"
            justify="center"
            align="middle"
            class="submission-area"
          >
            <Col :xs="20" :sm="16" :md="12" :lg="8">
              <Card :bordered="false" style="background-color: #1b1b1b">
                <div class="my-video-card" style="text-align: left">
                  <cpnt-thumb-content
                    :item="selected.videoData"
                  ></cpnt-thumb-content>
                </div>
              </Card>
            </Col>
            <!-- Video Catergory (Type) & Subject (Class) selection -->
            <Col
              :xs="20"
              :sm="16"
              :md="12"
              :lg="8"
              v-if="selected.videoData"
              type="flex"
              justify="center"
            >
              <!-- Category -->
              <Row style="padding: 10px; text-align: center">
                <Col span="6">
                  <span>{{ $t("activityChannel.type") }}</span>
                </Col>
                <Col span="18">
                  <Select v-model="selected.categoryId">
                    <Option
                      v-for="v in categoricalChoices"
                      :value="v.id"
                      :key="v.id"
                      >{{ v.name }}</Option
                    >
                  </Select>
                </Col>
              </Row>
              <!-- Subject -->
              <Row style="padding: 10px; text-align: center">
                <Col span="6">
                  <span>{{ $t("activityChannel.class") }}</span>
                </Col>
                <Col span="18">
                  <Select v-model="selected.subjectId">
                    <Option
                      v-for="v in subjectChoices"
                      :value="v.id"
                      :key="v.id"
                      >{{ v.alias }}</Option
                    >
                  </Select>
                </Col>
              </Row>
              <!-- Buttons -->
              <Row>
                <!-- Remove Btn -->
                <Button
                  type="error"
                  @click="clearSelectedVideo"
                  :disabled="!selected.videoData"
                  style="margin: 10px"
                >
                  {{ $t("activityChannel.removeSelectedVideo") }}
                </Button>
                <!-- Submission Btn -->
                <!-- Remark: Bypass submission Modal -->
                <Button
                  type="success"
                  @click="submitVideo"
                  :loading="isSubmitting"
                  :disabled="!submissionState.isAllowed || !selected.videoData"
                  style="margin: 10px"
                >
                  {{ $t("activityChannel.submitSelectedVideo") }}
                </Button>
              </Row>
              <!-- Reminder -->
              <Row style="padding-left: 4rem; text-align: left">
                <Col span="24">
                  <span style="font-size: 14px; color: #00ffea">
                    {{ $t("activityChannel.submissionReminder") }}
                  </span>
                </Col>
              </Row>
            </Col>
          </Row>
        </Col>
      </Col>

      <!-- Reviewing Stage -->
      <!-- Remark: Reviewing stage uses submission component without actions -->
      <!-- <Col
        span="24"
        v-else-if="eventState.stage.isReviewing && !eventState.deadline.isOver"
      >
        <cpnt-thumb-reviewing
          :group="group"
          :categoricalChoices="categoricalChoices"
          :subjectChoices="subjectChoices"
        >
        </cpnt-thumb-reviewing>
      </Col> -->

      <!-- Certification Stage -->
      <Col
        span="24"
        v-else-if="
          eventState.stage.isCertification && !eventState.deadline.isOver
        "
      >
        <cpnt-thumb-certification></cpnt-thumb-certification>
      </Col>

      <!-- Other State -->
      <Col span="24" v-else>
        <cpnt-thumb-others :eventState="eventState"></cpnt-thumb-others>
      </Col>
    </Row>

    <!-- Utilities -->
    <BackTop></BackTop>
    <!-- Video deletion Confirmation -->
    <Modal v-model="modal.deletion" @on-cancel="closeModalDeletion">
      <h3>{{ $t("activityChannel.modal.deletion.confirmation") }}</h3>
      <section slot="footer">
        <Button type="ghost" @click="closeModalDeletion">{{
          $t("activityChannel.modal.cancel")
        }}</Button>
        <Button type="primary" @click="deleteVideo">{{
          $t("activityChannel.modal.confirm")
        }}</Button>
      </section>
    </Modal>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";
import CpntThumbRegistration from "./thumb-registration.vue";
import CpntThumbReviewing from "./thumb-reviewing.vue";
import CpntThumbCertification from "./thumb-certification.vue";
import CpntThumbOthers from "./thumb-others.vue";
import CpntThumbContent from "../../../../app/components/thumb-content.vue";

export default {
  name: "activity-submission-area",
  components: {
    "cpnt-thumb-registration": CpntThumbRegistration,
    "cpnt-thumb-reviewing": CpntThumbReviewing,
    "cpnt-thumb-certification": CpntThumbCertification,
    "cpnt-thumb-others": CpntThumbOthers,
    "cpnt-thumb-content": CpntThumbContent,
  },
  props: [
    "group",
    "activeEventStageKey",
    "categoricalChoices",
    "subjectChoices",
    "eventState",
    "selected",
    "instructionalHint",
  ],
  emits: [
    "clear-selected-video",
    "submit-selected-video",
    "update-user-id-list",
    "update-allowed-selection-status",
  ],
  computed: _.merge(
    Vuex.mapState(["path", "user"]),
    Vuex.mapGetters(["logined"]),
    {
      groupId() {
        return this.group.groupId;
      },
      userId() {
        return this.$store.state.user.id;
      },
      submissionState() {
        let isSubmissionAllowed = true;

        // Not allow when the current stage is not submission
        if (!this.eventState.stage.isSubmission) isSubmissionAllowed = false;

        // Not allow if deadline has passed
        if (this.eventState.deadline.isOver) isSubmissionAllowed = false;

        // Not allow when registration is required and user is not in this group
        if (
          this.eventState.privilege.isMembershipRequired &&
          !this.eventState.privilege.hasUserRegistered
        )
          isSubmissionAllowed = false;

        // Not allow whee the submission area is occupied
        if (this.submittedVideoData) isSubmissionAllowed = false;

        this.$emit(
          "update-allowed-selection-status",
          this.eventState.type,
          isSubmissionAllowed
        );

        return {
          isAllowed: isSubmissionAllowed,
        };
      },
      submittedVideoType() {
        return _.find(this.categoricalChoices, [
          "id",
          this.submittedVideoCategoryId,
        ]).name;
      },
      submittedVideoClass() {
        return _.find(this.subjectChoices, ["id", this.submittedVideoSubjectId])
          .alias;
      },
    }
  ),
  watch: {
    selected: {
      handler(v) {
        if (!v.videoData) return;

        // Check selection allowance
        let isSelectionAllowed = true;
        let errorMsg = "";

        // Check if this selected video has alrready been submitted
        let selectedContentId = v.videoData.group_channel_content.content_id;
        let contentIds = Object.values(this.submittedVideoContentIds);
        if (contentIds.includes(selectedContentId)) {
          isSelectionAllowed = false;
          errorMsg = this.$t("activityChannel.msg.alreadySubmitted");
        } else if (this.submittedVideoData) {
          // Check if submitted area is occupied
          isSelectionAllowed = false;
          errorMsg = this.$t("activityChannel.msg.notAllowed");
        }

        if (!isSelectionAllowed) {
          this.$Notice.error({ title: errorMsg });
          this.clearSelectedVideo();
        }
      },
      deep: true,
    },
    submittedVideoData: function(v) {
      // If there's a submitted video -> not allowed, otherwise allowed
      let isAllowed = v ? false : true;
      this.$emit(
        "update-allowed-selection-status",
        this.eventState.type,
        isAllowed
      );
    },
  },
  data() {
    return {
      // Channel properties
      channelId: this.$route.params.channelId,
      // Submission
      submittedVideoContentDataList: [],
      submittedVideoContentIds: [],
      submittedVideoContentData: null,
      submittedVideoData: null,
      submittedVideoCategoryId: null,
      submittedVideoSubjectId: null,
      // Modal
      modal: {
        deletion: false,
        deleteContentId: null,
      },
      // Loading state
      isSubmitting: false,
      isSearching: false,
    };
  },
  methods: {
    init() {
      if (
        this.eventState.stage.isSubmission ||
        this.eventState.stage.isReviewing
      )
        this.getSubmittedVideoDataList();
    },
    updateUserIdList() {
      this.$emit("update-user-id-list");
    },
    getSubmittedVideoDataList() {
      let _this = this;
      let url = "/tbas/share/video/group/" + _this.channelId;
      axios
        .get(url, {
          params: {},
        })
        .then((data) => {
          if (data.status !== 200) return;

          // Create submitted data and id list
          _this.submittedVideoContentDataList = data.data;
          _this.submittedVideoContentIds = _.map(data.data, (v) => {
            return v.content_id;
          });

          // Find its video data, type, and subject for presentation
          if (_this.submittedVideoContentDataList.length > 0) {
            let videoContentData = _this.submittedVideoContentDataList[0];
            _this.submittedVideoContentData = videoContentData;
            _this.getCurTaskSubmittedVideoData(videoContentData);
            _this.submittedVideoCategoryId = videoContentData.ratings_id;
            _this.submittedVideoSubjectId =
              videoContentData.group_subject_fields_id;
          }
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {});
    },
    getCurTaskSubmittedVideoData(submittedData) {
      if (!submittedData) return;
      let url = "/exhibition/tbavideo/get-my-movie";
      let videoData = {};
      this.isSearching = true;
      axios
        .get(url, {
          params: {
            contentId: submittedData.content_id,
          },
        })
        .then((data) => {
          if (!data.data.status) return;
          if (_.size(data.data.data) < 1) return;
          videoData = data.data.data[0];
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
          this.submittedVideoData = videoData;
          this.isSearching = false;
        });
    },
    clearSelectedVideo() {
      this.$emit("clear-selected-video", true);
    },
    submitVideo() {
      let url = "/tbas/share/video/group/" + this.channelId;
      let isSuccessful = false;
      this.isSubmitting = true;
      axios
        .post(url, {
          subject: this.selected.videoData.subject,
          grade: this.selected.videoData.grade,
          ratings_id: this.selected.categoryId,
          group_subject_fields_id: this.selected.subjectId,
          content_id: this.selected.videoData.group_channel_content.content_id,
        })
        .then((data) => {
          if (!data) throw null;
          isSuccessful = true;
          this.$Notice.success({
            title: this.$t("activityChannel.msg.success"),
          });
        })
        .catch((e) => {
          console.log(e);
          this.$Notice.error({
            title: this.$t("activityChannel.msg.error"),
          });
        })
        .finally(() => {
          this.isSubmitting = false;
          this.$emit("submit-selected-video", isSuccessful);
          this.getSubmittedVideoDataList();
        });
    },
    deleteVideo() {
      if (!this.modal.deleteContentId) return;
      let url = "/tbas/share/video/group/" + this.channelId;
      axios
        .delete(url, {
          data: {
            content_id: this.modal.deleteContentId,
          },
        })
        .then((data) => {
          if (!data.status) throw data.message;

          // Remove submitted data
          this.submittedVideoContentDataList = [];
          this.submittedVideoContentIds = [];
          this.submittedVideoContentData = null;
          this.submittedVideoData = null;
          this.submittedVideoCategoryId = null;
          this.submittedVideoSubjectId = null;

          this.$Notice.success({
            title: this.$t("activityChannel.msg.success"),
          });
        })
        .catch((e) => {
          console.log(e);
          this.$Notice.error({
            title: this.$t("activityChannel.msg.error"),
          });
        })
        .finally(() => {
          this.getSubmittedVideoDataList();
          this.closeModalDeletion();
        });
    },
    openModalDeletion() {
      this.modal.deleteContentId = this.submittedVideoContentData.content_id;
      this.modal.deletion = true;
    },
    closeModalDeletion() {
      this.modal.deleteContentId = null;
      this.modal.deletion = false;
    },
  },
  created() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.submission-area-container {
  h1,
  h2 {
    color: #ffffff;
  }

  .submission-block {
    padding: 20px 50px 20px 50px;
  }

  .submission-block-disabled {
    pointer-events: none;
    cursor: not-allowed;
    opacity: 0.7;
  }

  .submission-area {
    padding: 10px;
    background-color: #6d6d6d;
    text-align: center;
    text-align: -webkit-center;
    border-radius: 10px;
  }

  .submission-selection-block {
    padding: 3rem;
  }

  .submission-selection {
    font-size: 1rem;
    color: #ffffff;

    text-align: right;
    overflow-wrap: break-word;
    padding-right: 10px;
  }

  .submission-area-btn {
    padding: 10px 50px 10px 50px;
  }

  .text-underline {
    text-align: center;

    border-bottom: 2px solid #ffffff;
    display: inline-block;
    line-height: 1.5;

    width: 200px;
  }

  .text-annoucement {
    font-size: 1rem;
    color: #ffffff;
  }

  .my-video-card-delete-icon {
    color: #ee5b5b;
    text-align: right;
    font-size: 1rem;
    cursor: pointer;
  }

  .spin-col {
    height: 150px;
    position: relative;
    padding: 10px;
    border: 1px solid #eee;
    border-radius: 10px;
  }

  .spin-icon-load {
    font-size: 1.5rem;
    color: #ffffff;
    animation: ani-demo-spin 1s linear infinite;
  }

  @keyframes ani-demo-spin {
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

  .ivu-spin-fix {
    background-color: #6d6d6d;
  }
}
</style>
