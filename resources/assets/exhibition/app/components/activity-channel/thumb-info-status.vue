<template>
  <div class="info-status-container">
    <!-- Activity Information -->
    <section v-if="group !== null">
      <Row>
        <Col span="20">
          <!-- Districts -->
          <div v-for="(v, k) in group.district" v-if="group.district">
            <a :href="'/district/' + v.districts.abbr">
              <h4 style="color: #acd6ff">{{ v.district_lang.name }}</h4>
            </a>
          </div>
          <!-- Activity Name -->
          <h1>{{ group.name }}</h1>

          <!-- Activity Description -->
          <Collapse
            class="channel-info-desc-container"
            v-model="displayActivityDesc"
            @click="toggleActivityDesc"
            accordion
          >
            <Panel name="desc">
              {{ $t("activityChannel.description") }}
              <div
                slot="content"
                class="channel-info-desc"
                v-html="group.description"
              ></div>
            </Panel>
          </Collapse>
        </Col>
        <Col span="4">
          <Img
            v-if="group.thumbnail"
            class="channel-info-img"
            v-bind:src="
              path.groupChannel +
                $route.params.channelId +
                '/' +
                group.thumbnail +
                '?' +
                Math.random()"
          ></Img>
          <Img
            v-else
            class="channel-info-img"
            src="/images/app/tw/teammodel/original-black-small.png"
          ></Img>
        </Col>
      </Row>
    </section>

    <!-- Activity Status -->
    <section class="channel-submission-status" v-if="eventType">
      <Row class="send-block">
        <!-- Submission Status Text -->
        <Col span="12">
          <h2 class="note">{{ $t("activityChannel.currentStatus") }}:</h2>
        </Col>
      </Row>

      <!-- Submission Status Indicators  -->
      <Row class="status-block">
        <Col
          span="4"
          v-for="(eventStageData, eventStageKey) in group.eventData.eventStage"
          :key="eventStageKey"
        >
          <Card class="status-card" v-if="isStageOrderActive(eventStageData)">
            <!-- Icon -->
            <Icon
              type="ios-circle-filled"
              style="color: #00ff7f"
              size="20"
              v-if="isEventCurrentlyActive(eventStageKey)"
            ></Icon>
            <Icon
              type="ios-circle-outline"
              style="color: #fad859"
              size="20"
              v-else
            ></Icon>
            <!-- Status Text -->
            <h4>{{ getActivityStatusName(eventStageKey) }}</h4>
          </Card>
        </Col>
      </Row>
    </section>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

export default {
  name: "info-status",
  components: {},
  props: ["group", "eventType", "activeEventStageKey"],
  computed: _.merge(
    Vuex.mapState(["path", "user"]),
    Vuex.mapGetters(["logined"])
  ),
  watch: {},
  data() {
    return {
      displayActivityDesc: "desc",
    };
  },
  methods: {
    toggleActivityDesc() {
      this.displayActivityDesc = this.displayActivityDesc ? "" : "desc";
    },
    isStageOrderActive(eventStageData) {
      // Stage Order is active when its value !== 0
      return parseInt(eventStageData.stageOrder);
    },
    isEventCurrentlyActive(eventStageKey) {
      return this.activeEventStageKey === eventStageKey;
    },
    getActivityStatusName(eventStageKey) {
      let activityStatusName = "";
      switch (this.eventType) {
        case "activity":
          let i18nActivityString = "activityChannel.activityStatus.activity";
          if (eventStageKey === "registration")
            activityStatusName = this.$t(
              i18nActivityString + ".registration.title"
            );
          else if (eventStageKey === "submission")
            activityStatusName = this.$t(
              i18nActivityString + ".submission.title"
            );
          else if (eventStageKey === "reviewing")
            activityStatusName = this.$t(
              i18nActivityString + ".reviewing.title"
            );
          else if (eventStageKey === "certification")
            activityStatusName = this.$t(
              i18nActivityString + ".certification.title"
            );
          break;
        case "training":
          let i18nTrainingString = "activityChannel.activityStatus.training";
          if (eventStageKey === "registration")
            activityStatusName = this.$t(
              i18nTrainingString + ".registration.title"
            );
          else if (eventStageKey === "submission")
            activityStatusName = this.$t(
              i18nTrainingString + ".submission.title"
            );
          else if (eventStageKey === "reviewing")
            activityStatusName = this.$t(
              i18nTrainingString + ".reviewing.title"
            );
          else if (eventStageKey === "certification")
            activityStatusName = this.$t(
              i18nTrainingString + ".certification.title"
            );
          break;
      }
      return this.capitalize(activityStatusName);
    },
    capitalize(text) {
      return text.replace(/^\w/, (c) => c.toUpperCase());
    },
  },
};
</script>

<style lang="scss">
.info-status-container {
  h1,
  h2 {
    color: #ffffff;
  }

  .channel-info-col {
    text-align: center;
  }

  .channel-info-desc-container {
    color: #ffffff !important;
    background-color: transparent !important;
    border: none !important;
  }

  .channel-info-desc {
    white-space: pre-wrap;
  }

  .channel-info-img {
    width: 50%;
    object-fit: contain;
    float: right;
  }

  .channel-info-main-text {
    display: inline;
    margin-left: 35px;
    color: #acd6ff;
  }

  .status-card {
    font-size: 18px;
    text-align: center;

    background-color: transparent;
    color: #ffffff;
  }

  .status-block {
    padding: 20px 0 20px 0;
  }
}

.ivu-collapse {
  .ivu-collapse-item {
    .ivu-collapse-header {
      color: #ffffff;
    }
    .ivu-collapse-content {
      color: #ffffff;
      background-color: transparent;
    }
  }
}
</style>