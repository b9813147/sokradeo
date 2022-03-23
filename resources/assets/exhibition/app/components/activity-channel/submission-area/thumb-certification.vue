<template>
  <div class="submission-area-certification-container">
    <Row type="flex" justify="center" align="middle" class="submission-area">
      <Card class="text-annoucement-container">
        <p>
          <Icon type="trophy" style="font-size: 3rem; color: #fad859"></Icon>
        </p>
        <p class="text-annoucement">
          {{ $t("activityChannel.msg.end") }}
        </p>
      </Card>
    </Row>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

export default {
  name: "certification",
  components: {},
  props: [],
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
.submission-area-certification-container {
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
