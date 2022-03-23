<i18n>
{
    "en": {
        "noData": "No Data"
    },
    "tw": {
        "noData": "無資料"
    },
    "cn": {
        "noData": "无资料"
    }
}
</i18n>

<template>
  <div
    class="no-data-container"
    :style="{ 'min-height': containerHeight + 'px' }"
  >
    <div class="no-data-content">
      <div class="no-data-icon">
        <Icon type="alert"></Icon>
      </div>
      <div class="no-data-text">
        <p>{{ text }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import Vuex from "vuex";

export default {
  name: "NoData",
  props: {
    customText: {
      type: String,
      default: null,
    },
  },
  computed: {
    ...Vuex.mapState("tbaplayer", {
      windowWidth: (state) => state.windowWidth,
      windowHeight: (state) => state.windowHeight,
    }),
    text() {
      let text = this.$t("noData");
      if (this.customText) text = this.customText;
      return text;
    },
    containerHeight() {
      return this.windowHeight / 2;
    },
  },
};
</script>

<style lang="scss" scoped>
.no-data-container {
  background-color: #171a20;

  .no-data-content {
    padding: 20px;
    text-align: center;
    color: #fff;

    .no-data-icon {
      font-size: 50px;
      margin-bottom: 20px;
    }

    .no-data-text {
      font-size: 18px;
    }
  }
}
</style>