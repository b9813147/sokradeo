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
        <!-- Task List -->
        <Row
          v-if="eventState.stage.isSubmission || eventState.stage.isReviewing"
        >
          {{ $t("activityChannel.taskStatus") }} :
          <div
            v-for="(task, taskId) in taskList"
            :key="'task_list_' + taskId"
            :class="[
              'task-menu-container',
              { 'task-active': taskId == taskState.curTaskId },
            ]"
            @click="selectTask(taskId)"
          >
            <span v-if="task.isDone">
              <Icon
                type="android-checkmark-circle"
                style="color: #00ff7f"
              ></Icon>
            </span>
            <span v-else>
              <Icon type="alert-circled" style="color: #fad859"></Icon>
            </span>
            {{ task.taskData.alias }}
          </div>
        </Row>
      </Col>
    </Row>
    <!-- Submission Block -->
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
        <!-- Current Task Done -->
        <Col span="24" v-if="taskState.isCurTaskDone">
          <Row
            type="flex"
            justify="center"
            align="middle"
            class="submission-area"
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
                  <Icon
                    type="load-c"
                    size="large"
                    class="spin-icon-load"
                  ></Icon>
                  <h4 style="color: #ffffff">
                    {{ $t("activityChannel.searching") }}
                  </h4>
                </Spin>
              </Row>
            </Col>
            <!-- Video Block -->
            <Col
              :xs="20"
              :sm="16"
              :md="12"
              :lg="6"
              v-if="curTaskSubmittedVideoData.videoData"
              type="flex"
              justify="center"
            >
              <Card :bordered="false" style="background-color: #1b1b1b">
                <div class="my-video-card" style="text-align: left">
                  <div
                    class="my-video-card-delete-icon"
                    v-if="eventState.stage.isSubmission"
                  >
                    <Icon
                      type="trash-a"
                      class="delete-icon"
                      @click="openModalDeletion"
                    >
                    </Icon>
                  </div>
                  <cpnt-thumb-content
                    :item="curTaskSubmittedVideoData.videoData"
                  ></cpnt-thumb-content>
                </div>
              </Card>
            </Col>
            <!-- Submitted Details -->
            <Col
              :xs="20"
              :sm="16"
              :md="12"
              :lg="8"
              v-if="curTaskSubmittedVideoData.submittedCategoryName"
              type="flex"
              justify="center"
            >
              <p>
                <Icon
                  type="android-checkmark-circle"
                  style="font-size: 3rem; color: #00ff7f"
                ></Icon>
              </p>
              <p class="text-annoucement">
                {{ $t("activityChannel.msg.completion") }}
              </p>
              <p class="text-annoucement">
                {{ $t("activityChannel.type") }} :
                {{ curTaskSubmittedVideoData.submittedCategoryName }}
              </p>
            </Col>
          </Row>
        </Col>
        <!-- Empty -->
        <Col
          span="24"
          v-else-if="!taskState.isCurTaskDone && !selected.videoData"
        >
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
        <Col
          span="24"
          v-else-if="!taskState.isCurTaskDone && selected.videoData"
        >
          <Row
            type="flex"
            justify="center"
            align="middle"
            class="submission-area"
          >
            <!-- Video Block -->
            <Col :xs="20" :sm="16" :md="12" :lg="6">
              <Card :bordered="false" style="background-color: #1b1b1b">
                <div class="my-video-card" style="text-align: left">
                  <cpnt-thumb-content
                    :item="selected.videoData"
                  ></cpnt-thumb-content>
                </div>
              </Card>
            </Col>
            <!-- Video Category (ratings) selection -->
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
              <Row style="padding: 20px; text-align: center">
                <Col span="6">
                  <span>{{ $t("activityChannel.type") }}</span>
                </Col>
                <Col span="18">
                  <Select v-model="selected.categoryId">
                    <Option
                      v-for="v in categoricalChoices"
                      :value="v.id"
                      :key="v.id"
                    >
                      {{ v.name }}
                    </Option>
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
                  <span :class="msg.submissionHint.class">
                    {{ msg.submissionHint.text }}
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
          :subjectChoices="taskChoices"
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

      <!-- Other States -->
      <Col span="24" v-else>
        <cpnt-thumb-others :eventState="eventState"></cpnt-thumb-others>
      </Col>
    </Row>

    <!-- Utilities -->
    <BackTop></BackTop>
    <!-- Video deletion Confirmation -->
    <Modal v-model="modal.deletion" @on-cancel="closeModalDeletion">
      <h3>
        {{ $t("activityChannel.modal.deletion.confirmation") }}
        <span style="color: #fad859">[{{ modal.deleteTaskName }}]</span>
      </h3>
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
  name: "training-submission-area",
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
    "taskChoices",
    "eventState",
    "selected",
    "instructionalHint",
  ],
  emits: [
    "clear-selected-video",
    "submit-selected-video-to-task",
    "update-user-id-list",
    "update-allowed-selection-status",
    "update-disallowed-content-ids",
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
      taskList() {
        let taskList = {};
        _.forEach(this.taskChoices, (task) => {
          let taskId = task.id;
          let isTaskDone = this.submittedVideoContentTaskIds.includes(taskId)
            ? true
            : false;
          let taskData = {
            taskData: task,
            isDone: isTaskDone,
            submittedContentId: this.submittedVideoContentIdsHash[taskId],
          };
          taskList[taskId] = taskData;
        });
        return taskList;
      },
      taskState() {
        return {
          initTaskId: this.taskChoices.length > 0 ? this.taskChoices[0].id : 0,
          curTaskId: parseInt(this.curTaskId),
          curTaskSubmittedVideoData: this.curTaskSubmittedVideoData,
          isCurTaskDone: this.submittedVideoContentTaskIds.includes(
            parseInt(this.curTaskId)
          ),
          allTasksDone:
            _.size(this.taskList) ===
            _.size(this.submittedVideoContentDataList),
        };
      },
      selectionState() {
        let contentIds = Object.values(this.submittedVideoContentIdsHash);
        let selectedVideoContentId = this.selected.videoData
          ? this.selected.videoData.group_channel_content.content_id
          : null;
        return {
          isSelectedVideoSubmitted: contentIds.includes(selectedVideoContentId),
        };
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

        // Not allow when the current task is already done
        if (this.taskState.isCurTaskDone) isSubmissionAllowed = false;

        // Not allow when the currently-selected video has already been submitted
        if (this.selectionState.isSelectedVideoSubmitted)
          isSubmissionAllowed = false;

        this.$emit(
          "update-allowed-selection-status",
          this.eventState.type,
          isSubmissionAllowed
        );

        return {
          isAllowed: isSubmissionAllowed,
        };
      },
      msg() {
        return {
          submissionHint: {
            class: this.selectionState.isSelectedVideoSubmitted
              ? "text-error"
              : "text-hint",
            text: this.selectionState.isSelectedVideoSubmitted
              ? this.$t("activityChannel.msg.alreadySubmitted")
              : this.$t("activityChannel.submissionReminder"),
          },
        };
      },
    }
  ),
  watch: {
    taskChoices: function(v) {
      // This is to ensure there's an active task
      this.updateActiveTask();
    },
    submittedVideoContentDataList: function(v) {
      this.updateActiveTask();

      // Update disallowed selection content ids
      let contentIds = _.map(v, "content_id");
      this.$emit("update-disallowed-content-ids", contentIds);

      // Update base allowed selelction status
      let isAllowed = this.submissionState.isAllowed;
      if (this.taskState.allTasksDone) isAllowed = false;
      this.$emit(
        "update-allowed-selection-status",
        this.eventState.type,
        isAllowed
      );
    },
    curTaskSubmittedVideoData: {
      handler(v) {
        let isAllowed = this.submissionState.isAllowed;
        if (v.videoData || v.submittedCategoryName) isAllowed = false;
        this.$emit(
          "update-allowed-selection-status",
          this.eventState.type,
          isAllowed
        );
      },
      deep: true,
    },
  },
  data() {
    return {
      // Channel properties
      channelId: this.$route.params.channelId,
      // Submission
      submittedVideoContentDataList: [],
      submittedVideoContentTaskIds: [],
      submittedVideoContentIdsHash: {}, // with taskId as key
      // Task props
      taskDataTemplate: {
        taskData: null,
        isDone: false,
        submittedContentId: null,
      },
      // Current data
      curTaskId: null,
      curTaskSubmittedVideoData: {
        videoData: null,
        submittedCategoryName: null,
      },
      // Modal
      modal: {
        deletion: false,
        deleteTaskId: null,
        deleteTaskName: null,
        deleteContentId: null,
      },
      // Loading states
      isSubmitting: false,
      isSearching: false,
    };
  },
  methods: {
    init() {
      this.selectTask(this.taskState.initTaskId); // init by selecting the first task
      this.getSubmittedVideoDataList();
    },
    updateUserIdList() {
      this.$emit("update-user-id-list");
    },
    updateActiveTask() {
      let _this = this;
      let activeTaskId = _this.curTaskId;
      if (!activeTaskId) activeTaskId = _this.taskState.initTaskId;
      _this.selectTask(activeTaskId);
    },
    selectTask(taskId) {
      this.curTaskId = taskId;
      this.clearSelectedVideo();
      this.curTaskSubmittedVideoData = {
        videoData: null,
        submittedCategoryName: null,
      };
      if (!this.taskState.isCurTaskDone) return;

      // If the current task is done, find its video data for presentation
      let submittedData = _.find(this.submittedVideoContentDataList, [
        "group_subject_fields_id",
        this.taskState.curTaskId,
      ]);
      this.getCurTaskSubmittedVideoData(submittedData);
    },
    getSubmittedVideoDataList() {
      let _this = this;
      let url = "/tbas/share/video/group/" + _this.channelId;

      // Clear existing data
      _this.submittedVideoContentDataList = [];
      _this.submittedVideoContentTaskIds = [];
      _this.submittedVideoContentIdsHash = {};

      axios
        .get(url, {
          params: {},
        })
        .then((data) => {
          if (data.status !== 200) return;

          // Create submittedVideoContentDataList
          _this.submittedVideoContentDataList = data.data;

          // Create submittedVideoContentTaskIds
          _this.submittedVideoContentTaskIds = _.map(
            _this.submittedVideoContentDataList,
            "group_subject_fields_id"
          );

          // Create submittedVideoContentIdsHash
          _.forEach(_this.submittedVideoContentDataList, (contentData) => {
            let taskId = contentData.group_subject_fields_id;
            _this.submittedVideoContentIdsHash[taskId] = contentData.content_id;
          });
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {});
    },
    getCurTaskSubmittedVideoData(submittedData) {
      if (!submittedData) return;
      let url = "/exhibition/tbavideo/get-my-movie";
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
          this.curTaskSubmittedVideoData.videoData = data.data.data[0];
          this.curTaskSubmittedVideoData.submittedCategoryName = this.convertTypeIDtoName(
            submittedData.ratings_id
          );
        })
        .catch((e) => {
          console.log(e);
        })
        .finally(() => {
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
          group_subject_fields_id: this.taskState.curTaskId,
          content_id: this.selected.videoData.group_channel_content.content_id,
        })
        .then((data) => {
          if (!data) throw null;

          // Update Task Ids
          this.submittedVideoContentTaskIds.push(this.taskState.curTaskId);

          // Update Content Id Hash with task id as a key
          this.submittedVideoContentIdsHash[
            this.taskState.curTaskId
          ] = this.selected.videoData.group_channel_content.content_id;

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
          this.getSubmittedVideoDataList();
          this.isSubmitting = false;
          this.$emit("submit-selected-video-to-task", isSuccessful);
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
    convertTypeIDtoName(typeID) {
      let _this = this;
      return _.find(_this.categoricalChoices, ["id", typeID]).name;
    },
    openModalDeletion() {
      this.modal.deleteTaskId = this.taskState.curTaskId;
      this.modal.deleteTaskName = _.find(this.taskChoices, [
        "id",
        this.taskState.curTaskId,
      ]).subject;
      this.modal.deleteContentId = this.curTaskSubmittedVideoData.videoData.id;
      this.modal.deletion = true;
    },
    closeModalDeletion() {
      this.modal.deleteTaskId = null;
      this.modal.deleteTaskName = null;
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

  .task-menu-container {
    display: inline-block;

    color: #ffffff;
    background-color: transparent;

    border-radius: 15px;
    padding: 5px;
    margin-left: 15px;

    opacity: 0.8;
    cursor: pointer;

    .delete-icon {
      color: #ee5b5b;
      cursor: pointer;
    }
  }

  .task-menu-container:hover {
    opacity: 1;

    background-color: #125dae;
    border-color: #2d8cf0;
  }

  .task-active {
    opacity: 1;

    background-color: #125dae;
    border-color: #2d8cf0;
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

  .text-hint {
    font-size: 14px;
    color: #00ffea;
  }

  .text-error {
    font-size: 14px;
    color: #ffa07a;
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
