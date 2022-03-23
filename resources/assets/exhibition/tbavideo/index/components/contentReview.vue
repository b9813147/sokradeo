<!--
  Page: Content Review
  Description: This page is used to review the content submitted from acitivity channel
  Author: Nuttaphat (Logan)
  Notes:
    - Init will get the base data from group.event_data (channel_id -> group_id -> event_data)
    - [16-DEC-2021] It IGNORES the current event stage and only concerns the reviewing event data (eventStage -> reviewing)
    - ** Sometimes, categorising pending and reviewed videos is too slow, please considering add loading state or delay
-->
<template>
  <article class="content-review-container">
    <!-- Activity Information and Status -->
    <section class="activity-info-status">
      <cpnt-thumb-info-status :group="group"> </cpnt-thumb-info-status>
    </section>

    <!-- Reviewing Stage -->
    <section class="reviwing-stage-container" v-if="eventState.reviewingData">
      <!-- Pending Review -->
      <section class="pending-review-video-list">
        <hr />
        <!-- Title -->
        <Row>
          <Col span="24">
            <h1>
              {{ $t("contentReview.pending.title") }}
              <span
                :class="[
                  'text-small',
                  { 'text-danger': eventState.deadline.isOver },
                ]"
                v-if="eventState.deadline.date"
              >
                ({{ $t("contentReview.deadline") }} :
                {{ eventState.deadline.date }})
              </span>
            </h1>
          </Col>
        </Row>
        <!-- Video List -->
        <Row v-if="pendingReviewVideoList.length > 0">
          <Col
            class="my-video-list"
            span="6"
            v-for="(divisionVideoData, index) in pendingReviewVideoList"
            :key="index"
          >
            <div class="my-video-card">
              <cpnt-thumb-content
                class="my-video-card-item"
                :item="divisionVideoData.videoData"
                :tag="getVideoTag(divisionVideoData)"
                @execute="goToContentPage(divisionVideoData)"
              ></cpnt-thumb-content>
              <!-- Actions -->
              <Row>
                <!-- Review -->
                <Col span="24">
                  <div class="my-video-card-btn">
                    <Button
                      type="primary"
                      icon="ios-compose"
                      @click="
                        displayContentReviewModal(divisionVideoData, 'create')
                      "
                      :disabled="!eventState.inProgress"
                      long
                    >
                      <span class="add-text">
                        {{ $t("contentReview.pending.btn") }}
                      </span>
                    </Button>
                  </div>
                </Col>
              </Row>
            </div>
          </Col>
        </Row>
        <!-- Loading -->
        <Row type="flex" justify="center" align="middle" v-else-if="isLoading">
          <Col class="spin-col" span="24">
            <Spin fix>
              <Icon type="load-c" size="large" class="spin-icon-load"></Icon>
              <h4 style="color: #ffffff">
                {{ $t("contentReview.msg.loading") }}
              </h4>
            </Spin>
          </Col>
        </Row>
        <!-- Empty -->
        <Row
          type="flex"
          justify="center"
          align="middle"
          class="submission-area"
          v-else
        >
          <p class="text-annoucement">{{ $t("contentReview.msg.empty") }}</p>
        </Row>
      </section>

      <!-- Reviewed -->
      <section class="reviewed-video-list">
        <hr />
        <!-- Title -->
        <Row>
          <Col span="24">
            <h1>
              {{ $t("contentReview.reviewed.title") }}
              <span
                :class="[
                  'text-small',
                  { 'text-danger': eventState.deadline.isOver },
                ]"
                v-if="eventState.deadline.date"
              >
                ({{ $t("contentReview.deadline") }} :
                {{ eventState.deadline.date }})
              </span>
            </h1>
          </Col>
        </Row>
        <!-- Video List -->
        <Row v-if="reviewedVideoList.length > 0">
          <Col
            class="my-video-list"
            span="6"
            v-for="(divisionVideoData, index) in reviewedVideoList"
            :key="index"
          >
            <div class="my-video-card">
              <cpnt-thumb-content
                class="my-video-card-item"
                :item="divisionVideoData.videoData"
                :tag="getVideoTag(divisionVideoData)"
                @execute="goToContentPage(divisionVideoData)"
              ></cpnt-thumb-content>
              <!-- Actions -->
              <Row>
                <!-- Review -->
                <Col span="24">
                  <div class="my-video-card-btn">
                    <!-- Editable -->
                    <Button
                      v-if="isVideoEditable(divisionVideoData)"
                      type="warning"
                      icon="edit"
                      @click="
                        displayContentReviewModal(divisionVideoData, 'update')
                      "
                      :disabled="!eventState.inProgress"
                      long
                    >
                      <span class="add-text">
                        {{ $t("contentReview.reviewed.editBtn") }}
                      </span>
                    </Button>
                    <!-- Already Reviewed -->
                    <Button v-else icon="locked" :disabled="true" long>
                      <span class="add-text">
                        {{ $t("contentReview.reviewed.btn") }}
                      </span>
                    </Button>
                  </div>
                </Col>
              </Row>
            </div>
          </Col>
        </Row>
        <!-- Loading -->
        <Row type="flex" justify="center" align="middle" v-else-if="isLoading">
          <Col class="spin-col" span="24">
            <Spin fix>
              <Icon type="load-c" size="large" class="spin-icon-load"></Icon>
              <h4 style="color: #ffffff">
                {{ $t("contentReview.msg.loading") }}
              </h4>
            </Spin>
          </Col>
        </Row>
        <!-- Empty -->
        <Row
          type="flex"
          justify="center"
          align="middle"
          class="submission-area"
          v-else
        >
          <p class="text-annoucement">{{ $t("contentReview.msg.empty") }}</p>
        </Row>
      </section>
    </section>

    <!-- Content Review Modal -->
    <Modal
      class="content-review-modal"
      v-model="contentReviewModal.display"
      @on-cancel="closeContentReviewModal"
    >
      <!-- Content Review Modal Title -->
      <section slot="header">
        <Icon type="ios-compose"></Icon>
        <span style="font-size: 20px; padding: 5px">
          {{ $t("contentReview.modal.title") }}
        </span>
        <!-- Tags -->
        <span v-if="contentReviewModal.curReviewingDivisionVideoData">
          <!-- Video name -->
          <Tag color="blue">
            {{
              contentReviewModal.curReviewingDivisionVideoData.videoData.name
            }}
          </Tag>
          <!-- Division -->
          <Tag color="green">
            {{ contentReviewModal.curReviewingDivisionVideoData.divisionTitle }}
          </Tag>
        </span>
      </section>

      <!--  Content Review Modal Editor Body -->
      <Form id="content-review">
        <!-- Input Mode -->
        <Row class="content-review-item">
          <RadioGroup v-model="contentReviewModal.inputMode">
            <Radio label="single">
              <span>{{ $t("contentReview.modal.inputMode.single") }}</span>
            </Radio>
            <Radio label="all">
              <span>{{ $t("contentReview.modal.inputMode.all") }}</span>
            </Radio>
          </RadioGroup>
        </Row>

        <!-- contentPractice -->
        <Row class="content-review-item" style="padding-top: 15px">
          <Col span="8">
            <span>{{ $t("contentReview.modal.contentPractice") }} : </span>
          </Col>
          <Col span="16">
            <input
              type="number"
              id="cPractice"
              name="cPractice"
              ref="cPractice"
              v-model="contentReviewData.cPractice"
              @change="contentReviewInputHandler('cPractice')"
              class="editor-input"
              :min="contentReviewScoreRange.min"
              :max="contentReviewScoreRange.max"
              :disabled="!modalState.inputMode.all"
            />
            <label for="scores" class="content-review-label">
              {{ $t("contentReview.modal.scoreRange") }}
            </label>
          </Col>
        </Row>
        <!-- teachingProcess -->
        <Row class="content-review-item" style="padding-top: 15px">
          <Col span="8">
            <span>{{ $t("contentReview.modal.teachingProcess") }} : </span>
          </Col>
          <Col span="16">
            <input
              type="number"
              id="tProcess"
              name="tProcess"
              ref="tProcess"
              v-model="contentReviewData.tProcess"
              @change="contentReviewInputHandler('tProcess')"
              class="editor-input"
              :min="contentReviewScoreRange.min"
              :max="contentReviewScoreRange.max"
              :disabled="!modalState.inputMode.single"
            />
            <label for="scores" class="content-review-label">
              {{ $t("contentReview.modal.scoreRange") }}
            </label>
          </Col>
        </Row>
        <!-- teachingDesign -->
        <Row class="content-review-item" style="padding-top: 15px">
          <Col span="8">
            <span>{{ $t("contentReview.modal.teachingDesign") }} : </span>
          </Col>
          <Col span="16">
            <input
              type="number"
              id="tDesign"
              name="tDesign"
              ref="tDesign"
              v-model="contentReviewData.tDesign"
              @change="contentReviewInputHandler('tDesign')"
              class="editor-input"
              :min="contentReviewScoreRange.min"
              :max="contentReviewScoreRange.max"
              :disabled="!modalState.inputMode.single"
            />
            <label for="scores" class="content-review-label">
              {{ $t("contentReview.modal.scoreRange") }}
            </label>
          </Col>
        </Row>
        <!-- innovation -->
        <Row class="content-review-item" style="padding-top: 15px">
          <Col span="8">
            <span>{{ $t("contentReview.modal.innovation") }} : </span>
          </Col>
          <Col span="16">
            <input
              type="number"
              id="inno"
              name="inno"
              ref="inno"
              v-model="contentReviewData.inno"
              @change="contentReviewInputHandler('inno')"
              class="editor-input"
              :min="contentReviewScoreRange.min"
              :max="contentReviewScoreRange.max"
              :disabled="!modalState.inputMode.single"
            />
            <label for="scores" class="content-review-label">
              {{ $t("contentReview.modal.scoreRange") }}
            </label>
          </Col>
        </Row>
        <!-- teachingApplication -->
        <Row class="content-review-item" style="padding-top: 15px">
          <Col span="8">
            <span>{{ $t("contentReview.modal.teachingApplication") }} : </span>
          </Col>
          <Col span="16">
            <input
              type="number"
              id="tApp"
              name="tApp"
              ref="tApp"
              v-model="contentReviewData.tApp"
              @change="contentReviewInputHandler('tApp')"
              class="editor-input"
              :min="contentReviewScoreRange.min"
              :max="contentReviewScoreRange.max"
              :disabled="!modalState.inputMode.single"
            />
            <label for="scores" class="content-review-label">
              {{ $t("contentReview.modal.scoreRange") }}
            </label>
          </Col>
        </Row>
        <!-- teachingEffect -->
        <Row class="content-review-item" style="padding-top: 15px">
          <Col span="8">
            <span>{{ $t("contentReview.modal.teachingEffect") }} : </span>
          </Col>
          <Col span="16">
            <input
              type="number"
              id="tEffect"
              name="tEffect"
              ref="tEffect"
              v-model="contentReviewData.tEffect"
              @change="contentReviewInputHandler('tEffect')"
              class="editor-input"
              :min="contentReviewScoreRange.min"
              :max="contentReviewScoreRange.max"
              :disabled="!modalState.inputMode.single"
            />
            <label for="scores" class="content-review-label">
              {{ $t("contentReview.modal.scoreRange") }}
            </label>
          </Col>
        </Row>
        <!-- comment -->
        <Row class="content-review-item" style="padding-top: 15px">
          <Col span="8">
            <span>{{ $t("contentReview.modal.comment") }} : </span>
          </Col>
          <Col span="16">
            <Input
              type="textarea"
              :rows="4"
              v-model="contentReviewData.comment"
              class="editor-input editor-item-input"
              :maxlength="contentReviewWordLimit"
              spellcheck
            />
            <span class="text-hint">{{ commentHint }}</span>
          </Col>
        </Row>
      </Form>
      <!-- Editor Footer -->
      <section slot="footer">
        <Button @click="closeContentReviewModal">{{
          $t("contentReview.modal.cancel")
        }}</Button>
        <Button type="primary" @click="reviewVideo" :loading="isSubmitting">
          {{ $t("contentReview.modal.submit") }}
        </Button>
      </section>
    </Modal>
  </article>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

import CpntThumbContent from "../../../app/components/thumb-content.vue";
import CpntInfoStatus from "../../../app/components/activity-channel/thumb-info-status.vue";

export default {
  name: "content-review",
  components: {
    "cpnt-thumb-info-status": CpntInfoStatus,
    "cpnt-thumb-content": CpntThumbContent,
  },
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
      eventState() {
        return {
          type: this.curEventType,
          data: this.group.eventData,
          reviewingData: this.reviewingEventData,
          stage: this.eventStateStage,
          start: this.eventStateStart,
          deadline: this.eventStateDeadline,
          inProgress: this.eventStateInProgress,
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
      eventStateStart() {
        // Check reviewing starting date
        if (!this.reviewingEventData) return;

        let hasStarted = false;
        let today = new Date().setHours(0, 0, 0);
        let startDate = this.reviewingEventData.startDate
          ? new Date(this.reviewingEventData.startDate).setHours(0, 0, 0)
          : null;

        if (startDate && today >= startDate) hasStarted = true;

        return {
          date: this.reviewingEventData.startDate,
          hasStarted: hasStarted,
        };
      },
      eventStateDeadline() {
        // Check reviewing deadline
        if (!this.reviewingEventData) return;

        let isOver = false;
        let today = new Date().setHours(0, 0, 0);
        let deadline = this.reviewingEventData.endDate
          ? new Date(this.reviewingEventData.endDate).setHours(0, 0, 0)
          : null;

        // If there's a deadline, check whether it has passed or not
        if (deadline && today > deadline) isOver = true;

        return {
          date: this.reviewingEventData.endDate,
          isOver: isOver,
        };
      },
      eventStateInProgress() {
        // Check whether reviewing is in progress or not
        if (!this.eventStateStart || !this.eventStateDeadline) return;
        return (
          this.eventStateStart.hasStarted && !this.eventStateDeadline.isOver
        );
      },
      modalState() {
        return {
          inputMode: {
            single: this.contentReviewModal.inputMode === "single",
            all: this.contentReviewModal.inputMode === "all",
          },
          submissionMode: {
            create: this.contentReviewModal.submissionMode === "create",
            update: this.contentReviewModal.submissionMode === "update",
          },
        };
      },
      pendingReviewVideoList() {
        let _this = this;
        if (_.size(_this.assignedDivisionVideoDataList) < 1) return [];
        // Create list with condition
        let pendingReviewVideoList = _.filter(
          _this.assignedDivisionVideoDataList,
          (divisionVideoData) => {
            return _this.videoReviewRequired(divisionVideoData);
          }
        );
        return pendingReviewVideoList;
      },
      reviewedVideoList() {
        let _this = this;
        if (_.size(_this.assignedDivisionVideoDataList) < 1) return [];
        // Create list with condition
        let reviewedVideoList = _.filter(
          _this.assignedDivisionVideoDataList,
          (divisionVideoData) => {
            return !_this.videoReviewRequired(divisionVideoData);
          }
        );
        return reviewedVideoList;
      },
      generalColorList() {
        // based on iview <Tag>
        return ["blue", "green", "yellow", "red", "default"];
      },
      divisionColorScheme() {
        // Assign unique to each division
        // key: divisionId, value: color
        let colorList = JSON.parse(JSON.stringify(this.generalColorList));
        let colorScheme = {};
        _.forEach(this.divisions, (divisionData) => {
          let isAssignedToUser = this.isDivisionAssignedToCurUser(divisionData);
          if (!isAssignedToUser) return;
          // Get the color and Assign it with divisionId
          if (colorList.length < 1)
            colorList = JSON.parse(JSON.stringify(this.generalColorList));
          let color = colorList.shift();
          colorScheme[divisionData.id] = color;
        });
        return colorScheme;
      },
      commentHint() {
        let hint = this.$t("contentReview.modal.commentHint");
        return hint.replace("{wordLimit}", this.contentReviewWordLimit);
      },
      scoreFormData() {
        return {
          comment: this.contentReviewData.comment,
          scoreData: JSON.stringify({
            cPractice: parseInt(this.contentReviewData.cPractice),
            tProcess: parseInt(this.contentReviewData.tProcess),
            tDesign: parseInt(this.contentReviewData.tDesign),
            inno: parseInt(this.contentReviewData.inno),
            tApp: parseInt(this.contentReviewData.tApp),
            tEffect: parseInt(this.contentReviewData.tEffect),
          }),
        };
      },
    }
  ),
  watch: {
    group: {
      handler(v) {
        // Get eventType
        this.curEventType = v.eventData.eventType.toLowerCase();

        // Find curently active eventStage
        this.activeEventStageKey = this.getActiveEventStageKey(
          v.eventData.eventStage
        );

        // Get Reviewing event data
        this.reviewingEventData =
          v.eventData.eventStage[this.reviewingEventStageKey];
      },
      deep: true,
    },
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
      // Divisions
      divisions: [],
      // Scores
      scores: [],
      // Assigned Division Video List
      divisionVideoDataTemplate: {
        divisionId: null,
        divisionTitle: null,
        groupId: null,
        videoData: null,
      },
      assignedDivisionVideoDataList: [],
      // Content Review Modal
      contentReviewModal: {
        display: false,
        curReviewingDivisionVideoData: null,
        curReviewingScoreId: null, // existing score id for update
        inputMode: "single", // "single" or "all"
        submissionMode: "create", // "create" or "update"
      },
      contentReviewScoreRange: {
        min: 0,
        max: 100,
      },
      contentReviewWordLimit: 200,
      contentReviewData: {
        cPractice: 0,
        tProcess: 0,
        tDesign: 0,
        inno: 0,
        tApp: 0,
        tEffect: 0,
        comment: null,
      },
      // States
      curEventType: null,
      activeEventStageKey: null,
      reviewingEventStageKey: "reviewing",
      reviewingEventData: null,
      // Loading
      isLoading: false,
      isSubmitting: false,
    };
  },
  methods: {
    /*
     * -------------------------
     * Initial Method
     * -------------------------
     */
    init() {
      // Check login state
      if (!this.logined) {
        this.$emit("check-logined", true, false, false, false);
        return;
      }
      // Get group data
      this.getGroupData();
    },
    /*
     * -------------------------
     * Video Presentation Methods
     * -------------------------
     */
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
              this.$t("contentReview.msg.missing")
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

          // Load Essential Data
          this.getDivisions();
          this.getScores();
        })
        .catch((e) => {
          console.log(e);
        });
    },
    transformEventData(rawEventData) {
      let eventData = JSON.parse(rawEventData); // JSON string -> object
      return eventData;
    },
    getActiveEventStageKey(eventStage) {
      // Event is active when group_channels.stage === stageOrder
      let activeEventStageKey = _.findKey(eventStage, (eventStageData) => {
        return (
          parseInt(eventStageData.stageOrder) === parseInt(this.group.stage)
        );
      });
      return activeEventStageKey ? activeEventStageKey.toLowerCase() : null;
    },
    getDivisions() {
      let url = "/division/" + this.channelId;
      this.isLoading = true;
      axios
        .get(url)
        .then((response) => {
          if (response.status !== 200) return;
          let divisions = response.data;
          this.divisions = divisions;
          this.createAssignedDivisionVideoDataList();
        })
        .catch((e) => {
          console.log(e.error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    createAssignedDivisionVideoDataList() {
      // Iterate though each division
      // Check if this user is assigned to this division or not
      // If yes, get this division's tbas
      this.assignedDivisionVideoDataList = [];
      _.forEach(this.divisions, (divisionData) => {
        let isAssignedToUser = this.isDivisionAssignedToCurUser(divisionData);
        if (!isAssignedToUser) return;

        // Create divisionVideoDataList
        let divisionVideoDataList = [];
        divisionVideoDataList = _.map(divisionData.tbas, (tba) => {
          // Based on divisionVideoDataTemplate
          return {
            divisionId: divisionData.id,
            divisionTitle: divisionData.title,
            groupId: divisionData.group_id,
            videoData: tba,
          };
        });

        // Assign divisionVideoDataList to assignedDivisionVideoDataList
        this.assignedDivisionVideoDataList = _.concat(
          this.assignedDivisionVideoDataList,
          divisionVideoDataList
        );
      });
    },
    isDivisionAssignedToCurUser(divisionData) {
      // Division must have user assigned and tba data
      let isAssignedToUser = true;
      let curUser = _.find(divisionData.users, ["id", this.userId]);
      if (!curUser || _.size(divisionData.tbas) < 1) isAssignedToUser = false;
      return isAssignedToUser;
    },
    getScores() {
      let url = "/score/" + this.channelId;
      axios
        .get(url)
        .then((response) => {
          if (response.status !== 200) return;
          this.scores = response.data;
        })
        .catch((e) => {
          console.log(e.error);
        });
    },
    videoReviewRequired(divisionVideoData) {
      // Determine whether this video can be reviwed or not
      // Rules for Required Review:
      //   1. If this video DOES NOT have 2 sets of scores
      //   2. And this user HAS NOT reviewed this video
      let reviewRequired = true;
      let divisionVideoScoreList = _.filter(this.scores, {
        tba_id: parseInt(divisionVideoData.videoData.id),
        group_id: parseInt(this.groupId),
      });
      let divisionVideoUserScoreData =
        this.findUserScoreData(divisionVideoData);
      if (divisionVideoScoreList.length >= 2) reviewRequired = false;
      if (divisionVideoUserScoreData !== undefined) reviewRequired = false;
      return reviewRequired;
    },
    goToContentPage(divisionVideoData) {
      let groupId = divisionVideoData.groupId; // Use division -> group_id
      let channelId = this.channelId;
      let routeData = this.$router.resolve({
        name: "content",
        params: { contentId: divisionVideoData.videoData.id },
        query: { groupIds: groupId, channelId: channelId },
      });
      window.open(routeData.href, "_blank").focus();
    },
    isVideoEditable(divisionVideoData) {
      // Only allow when this user has rated this video
      let isEditable = false;
      let scoreData = this.findUserScoreData(divisionVideoData);
      if (scoreData) isEditable = true;
      return isEditable;
    },
    /*
     * -------------------------
     * Content Review Modal Methods
     * -------------------------
     */
    displayContentReviewModal(divisionVideoData, submissionMode) {
      if (!this.eventState.inProgress) return;

      this.contentReviewModal.curReviewingDivisionVideoData = divisionVideoData;
      this.contentReviewModal.curReviewingScoreId = null;
      this.contentReviewModal.submissionMode = submissionMode;
      this.setContentReviewData(); // set up after getting curData and before showing modal
      this.contentReviewModal.display = true;
    },
    closeContentReviewModal() {
      this.contentReviewModal.curReviewingDivisionVideoData = null;
      this.contentReviewModal.curReviewingScoreId = null;
      this.contentReviewModal.submissionMode = "create";
      this.contentReviewModal.display = false;
    },
    contentReviewInputHandler(ref) {
      this.contentReviewInputValidator(ref);
      // Check Input Mode and Assign values accordingly
      if (this.modalState.inputMode.single)
        this.contentReviewContentPracticeCalc();
      else if (this.modalState.inputMode.all)
        this.contentReviewAssignAllScores();
    },
    contentReviewInputValidator(ref) {
      let itemRef = this.$refs[ref].name;
      let curValue = this.contentReviewData[itemRef];
      let min = this.contentReviewScoreRange.min; // 0
      let max = this.contentReviewScoreRange.max; // 100
      if (!curValue || !_.inRange(curValue, min, max + 1))
        // 0, 101 -> 0 - 100
        this.contentReviewData[itemRef] = 0;
    },
    contentReviewContentPracticeCalc() {
      if (!this.modalState.inputMode.single) return;
      // Calculate value for content practice
      // Content Practice (Avg) = Others / n
      let scoreList = [
        parseInt(this.contentReviewData.tProcess),
        parseInt(this.contentReviewData.tDesign),
        parseInt(this.contentReviewData.inno),
        parseInt(this.contentReviewData.tApp),
        parseInt(this.contentReviewData.tEffect),
      ];
      let summation = _.sum(scoreList);
      this.contentReviewData.cPractice = _.round(summation / scoreList.length);
    },
    contentReviewAssignAllScores() {
      if (!this.modalState.inputMode.all) return;
      let itemRef = this.$refs["cPractice"].name;
      let cPracticevalue = this.contentReviewData[itemRef];
      this.contentReviewData.tProcess = cPracticevalue;
      this.contentReviewData.tDesign = cPracticevalue;
      this.contentReviewData.inno = cPracticevalue;
      this.contentReviewData.tApp = cPracticevalue;
      this.contentReviewData.tEffect = cPracticevalue;
    },
    reviewVideo() {
      // Main method to create and update review score
      if (!this.eventState.inProgress) return;

      if (this.modalState.submissionMode.create) this.createReview();
      else if (this.modalState.submissionMode.update) this.updateReview();
    },
    createReview() {
      if (!this.contentReviewModal.curReviewingDivisionVideoData) return;
      let curDivisionVideoData =
        this.contentReviewModal.curReviewingDivisionVideoData;
      let url = "/score";

      this.isSubmitting = true;
      axios
        .post(url, {
          user_id: this.userId, // Expert userId
          group_id: this.groupId, // activity groupId
          tba_id: curDivisionVideoData.videoData.id,
          comment: this.scoreFormData.comment,
          score_data: this.scoreFormData.scoreData,
        })
        .then((response) => {
          if (response.status !== 200) throw response.statusText;
          this.displaySuccessMsg(this.$t("contentReview.msg.success"));
          this.getScores();
        })
        .catch((e) => {
          console.log(e);
          this.displayErrorMsg(this.$t("contentReview.msg.error"));
        })
        .finally(() => {
          this.isSubmitting = false;
          this.closeContentReviewModal();
        });
    },
    updateReview() {
      if (!this.contentReviewModal.curReviewingScoreId) return;
      let url = "/score/" + this.contentReviewModal.curReviewingScoreId;

      this.isSubmitting = true;
      axios
        .put(url, {
          comment: this.scoreFormData.comment,
          score_data: this.scoreFormData.scoreData,
        })
        .then((response) => {
          if (response.status !== 200) throw response.statusText;
          this.displaySuccessMsg(this.$t("contentReview.msg.success"));
          this.getScores();
        })
        .catch((e) => {
          console.log(e);
          this.displayErrorMsg(this.$t("contentReview.msg.error"));
        })
        .finally(() => {
          this.isSubmitting = false;
          this.closeContentReviewModal();
        });
    },
    setContentReviewData() {
      // Set up default values
      this.contentReviewData.cPractice = 0;
      this.contentReviewData.tProcess = 0;
      this.contentReviewData.tDesign = 0;
      this.contentReviewData.inno = 0;
      this.contentReviewData.tApp = 0;
      this.contentReviewData.tEffect = 0;
      this.contentReviewData.comment = null;

      // Get existing scores if update is needed
      if (this.modalState.submissionMode.update)
        this.getExistingContentReviewData();
    },
    getExistingContentReviewData() {
      // Update Scores from API
      this.getScores();

      // Set up content review data based on newly acquired Scores
      let curDivisionVideoData =
        this.contentReviewModal.curReviewingDivisionVideoData;
      if (!curDivisionVideoData) return;
      let scoreData = this.findUserScoreData(curDivisionVideoData);
      if (!scoreData) return;

      // Set up socre Id for [PUT]
      this.contentReviewModal.curReviewingScoreId = scoreData.id;

      // Update Modal items
      let scoreDataItems = JSON.parse(scoreData.score_data);
      this.contentReviewData.cPractice = scoreDataItems.cPractice;
      this.contentReviewData.tProcess = scoreDataItems.tProcess;
      this.contentReviewData.tDesign = scoreDataItems.tDesign;
      this.contentReviewData.inno = scoreDataItems.inno;
      this.contentReviewData.tApp = scoreDataItems.tApp;
      this.contentReviewData.tEffect = scoreDataItems.tEffect;
      this.contentReviewData.comment = scoreData.comment;
    },
    findUserScoreData(divisionVideoData) {
      return _.find(this.scores, {
        group_id: this.groupId,
        tba_id: divisionVideoData.videoData.id,
        user_id: this.userId,
      });
    },
    /*
     * -------------------------
     * Misc. Methods
     * -------------------------
     */
    getVideoTag(divisionVideoData) {
      return {
        text: divisionVideoData.divisionTitle,
        color: this.divisionColorScheme[divisionVideoData.divisionId],
      };
    },
    displaySuccessMsg(msg) {
      this.$Notice.success({ title: msg });
    },
    displayErrorMsg(msg) {
      this.$Notice.error({ title: msg });
    },
    redirectToHomeWithErrorMsg(msg) {
      this.displayErrorMsg(msg);
      this.$router.push({ path: "/" });
    },
  },
  created() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.content-review-container {
  section {
    padding-top: 20px;
  }

  hr {
    opacity: 0.2;
  }

  .text-annoucement {
    font-size: 1rem;
    color: #ffffff;

    padding: 10px;
  }

  .text-small {
    font-size: 1.2rem;
  }

  .text-danger {
    color: #ee5b5b;
  }

  .my-video-list {
    padding-top: 5px;
  }

  .my-video-card-item {
    cursor: pointer;
  }

  .my-video-card-btn {
    text-align: center;
    padding: 10px;
  }

  .submission-area {
    margin: 20px;
    padding: 20px;
    background-color: #6d6d6d;
    text-align: center;
    text-align: -webkit-center;
    border-radius: 10px;
  }

  .spin-col {
    height: 150px;
    position: relative;
    padding: 10px;
  }

  .spin-icon-load {
    font-size: 1.5rem;
    color: #ffffff;
    animation: ani-spin 1s linear infinite;
  }

  .ivu-spin-fix {
    background-color: transparent;
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
