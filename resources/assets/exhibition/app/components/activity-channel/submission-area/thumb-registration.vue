<template>
  <div class="submission-area-registration-container">
    <Row type="flex" justify="center" align="middle" class="submission-area">
      <Card class="text-annoucement-container">
        <!-- Full -->
        <div
          v-if="
            registrationState.checkFullParticipants && registrationState.isFull
          "
        >
          <span class="text-annoucement">
            <Icon type="alert-circled" style="color: #fad859"></Icon>
            {{ $t("activityChannel.msg.fullParticipants") }}
          </span>
        </div>
        <!-- Already Registered -->
        <div v-else-if="registrationState.hasUserRegistered">
          <Icon
            type="android-checkmark-circle"
            style="color: #00ff7f; font-size: 3rem"
          ></Icon>
          <span class="text-annoucement">
            <p>{{ $t("activityChannel.msg.joined") }}</p>
            <p v-if="hiTeachLicense">
              {{ $t("activityChannel.msg.HiTeachLicense") }} :
              {{ hiTeachLicense }}
              <span
                class="censorship-icon"
                @click="toggleHiTeachLicenseCensorship"
              >
                <Icon type="eye" v-if="censorHiTeachLicense"></Icon>
                <Icon type="eye-disabled" v-else></Icon>
              </span>
            </p>
          </span>
        </div>
        <!-- Registration Btn -->
        <div
          v-else-if="
            registrationState.isAllowed &&
            !(
              registrationState.checkFullParticipants &&
              registrationState.isFull
            ) &&
            !registrationState.hasUserRegistered
          "
        >
          <Button
            type="primary"
            :disabled="!registrationState.isAllowed"
            :loading="isRegistering"
            @click="registerToGroup"
          >
            <span class="text-annoucement">
              {{ $t("activityChannel.msg.registration") }}
            </span>
          </Button>
        </div>
      </Card>
    </Row>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

export default {
  name: "registration",
  components: {},
  props: ["group", "eventState", "userIdList"],
  emits: ["update-user-id-list"],
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
      hiTeachLicense() {
        let license = "";
        let userGroups = this.$store.state.user.groups;
        let groupData = _.find(userGroups, ["id", this.groupId]);
        if (!_.has(groupData, "pivot")) return license;

        let groupPivotData = groupData.pivot;
        let groupPivotUserData = JSON.parse(groupPivotData.user_data);
        if (!_.has(groupPivotUserData, "license")) return license;

        // Get license
        license = this.censorHiTeachLicense
          ? "xxxxxxxx-xxxx-xxxx-xxxx"
          : groupPivotUserData.license;

        return license;
      },
      registrationState() {
        let isRegistrationAllowed = true;

        // Not allow if user has already registerd
        let hasUserRegistered = this.userIdList.includes(this.userId);
        if (hasUserRegistered) isRegistrationAllowed = false;

        // Not allow if participants are full
        let maxParticipants = this.eventState.data.maxParticipant;
        let checkFullParticipants = maxParticipants > 0 ? true : false;
        let isFull = this.userIdList.length >= maxParticipants;
        if (checkFullParticipants && isFull) isRegistrationAllowed = false;

        // Not allow if deadline has passed
        if (this.eventState.deadline.isOver) isRegistrationAllowed = false;

        return {
          hasUserRegistered: hasUserRegistered,
          isAllowed: isRegistrationAllowed,
          checkFullParticipants: checkFullParticipants,
          isFull: isFull,
        };
      },
    }
  ),
  watch: {},
  data() {
    return {
      censorHiTeachLicense: true,
      isRegistering: false,
    };
  },
  methods: {
    init() {},
    registerToGroup() {
      let _this = this;
      let url = "/group/" + _this.groupId + "/users";
      if (!_this.registrationState.isAllowed) return;

      _this.isRegistering = true;
      axios
        .post(url)
        .then((data) => {
          data = data.data;
          if (!data.status) throw null;
          _this.$Notice.success({
            title: this.$t("activityChannel.msg.success"),
          });
          _this.$emit("update-user-id-list");
        })
        .catch((e) => {
          console.log(e);
          _this.$Notice.error({
            title: _this.$t("activityChannel.msg.error"),
          });
        })
        .finally(() => {
          _this.isRegistering = false;
        });
    },
    toggleHiTeachLicenseCensorship() {
      this.censorHiTeachLicense = !this.censorHiTeachLicense;
    },
  },
  created() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.submission-area-registration-container {
  h1,
  h2 {
    color: #ffffff;
  }

  .submission-area {
    padding: 10px;
    background-color: #6d6d6d;
    text-align: center;
    text-align: -webkit-center;
    border-radius: 10px;
  }

  .text-annoucement-container {
    border: none;
    background-color: transparent;
    text-align: center;
  }

  .text-annoucement {
    font-size: 1rem;
    color: #ffffff;
  }

  .censorship-icon {
    cursor: pointer;
  }

  .submission-selection-block {
    padding: 3rem;
  }
}
</style>
