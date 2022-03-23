<i18n>
{
    "en": {
        "chart": {
            "title": "Comments"
        }
    },
    "tw": {
        "chart": {
            "title": "標記"
        }
    },
    "cn": {
        "chart": {
            "title": "标记"
        }
    }
}
</i18n>
<template>
  <div class="chart-tba-comment-container">
    <!-- Tba Comment Chart -->
    <section style="width: 100%; height: 100%">
      <div :id="id" style="width: 100%; height: 50px"></div>
    </section>
  </div>
</template>

<script>
import _ from "lodash";
import CpntChartLinerangeComment from "./chart-linerange-comment.vue";

export default {
  mixins: [CpntChartLinerangeComment],
  props: ["id"],
};
</script>

<style lang="scss" scoped>
.chart-tba-comment-container {
  position: relative;
  left: 40px;
  top: -10px;
}
</style>