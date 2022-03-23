<template>
  <div class="submission-area-others-container">
    <Row type="flex" justify="center" align="middle" class="submission-area">
      <Card class="text-annoucement-container">
        <p>
          <Icon type="close" style="font-size: 2rem; color: #ee5b5b"></Icon>
        </p>
        <p class="text-annoucement">
          <span v-if="eventState.deadline.isOver">
            {{ $t("activityChannel.msg.isOver") }}
          </span>
          <span v-else>
            {{ $t("activityChannel.msg.notAllowed") }}
          </span>
        </p>
      </Card>
    </Row>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

export default {
  name: "others",
  components: {},
  props: ["eventState"],
  emits: [],
  computed: _.merge(
    Vuex.mapState(["path", "user"]),
    Vuex.mapGetters(["logined"])
  ),
  watch: {},
  data() {
    return {};
  },
  methods: {
    init() {},
  },
  created() {
    this.init();
  },
};
</script>

<style lang="scss" scoped>
.submission-area-others-container {
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
}
</style>