<script>
import _ from "lodash";
import Vuex from "vuex";

export default {
  template: "<canvas :id='id' class='base-chart-container'></canvas>",
  props: ["id"],
  emits: ["reset-comment-detail"],
  data() {
    return {
      commentTimeValueList: [],
      commentChart: null,
      commentChartOption: {
        title: {
          show: "true",
          text: this.$i18n.t("chart.title"),
          textStyle: {
            color: "#fff",
            fontStyle: "normal",
            fontWeight: "normal",
            fontSize: 12,
          },
          top: "10",
          left: "1%",
          textAlign: "left",
        },
        grid: [
          {
            top: "10px",
            right: "10px",
            left: "80px",
            height: "25px",
          },
          {
            top: "35px",
            left: "80px",
            right: "10px",
            bottom: "5px",
            height: "25px",
          },
        ],
        tooltip: {
          trigger: "item",
          position: function (pos, params, dom, rect, size) {
            let obj = [0, pos[1] - 10 - size.contentSize[1]];
            if (size.viewSize[0] - pos[0] > size.contentSize[0]) {
              obj[0] = pos[0] + 10;
            } else if (pos[0] > size.contentSize[0]) {
              obj[0] = pos[0] - 10 - size.contentSize[0];
            } else {
              obj[["left", "right"][+(pos[0] < size.viewSize[0] / 2)]] = 5;
            }
            return obj;
          },
          formatter: (params) => this.getLabelFromCommentChartItem(params),
        },
        xAxis: {
          min: 0,
          max: 0,
          type: "value",
          interval: 300,
          axisLine: {
            show: true,
          },
          splitLine: {
            show: true,
            lineStyle: {
              color: "#7A8297",
              type: "solid",
              width: 1,
            },
          },
          axisTick: {
            show: false,
          },
          axisPointer: {
            show: false,
            type: "none",
          },
          axisLabel: {
            show: false,
            formatter: (time) => this.toMinutes(time),
          },
        },
        yAxis: {
          type: "category",
          data: [0], // One line
          axisLabel: {
            show: false,
          },
        },
        seriesObjTemplate: {
          data: [],
          type: "line",
          smooth: true,
          symbolSize: 6,
          itemStyle: {
            normal: { color: "rgb(235, 217, 82)" },
          },
          areaStyle: {
            normal: { color: "rgba(0, 163, 131, 0.2)" },
          },
          markLine: {
            silent: true,
            lineStyle: {
              normal: {
                type: "solid",
                color: "red",
              },
            },
            symbol: "none",
            animation: false,
            label: {
              show: false,
            },
            data: [
              {
                xAxis: 0,
              },
            ],
          },
        },
      },
    };
  },
  computed: _.merge(
    Vuex.mapState("tbaplayer", {
      mode: (state) => state.mode,
      preTbaId: (state) => state.preTbaId,
      tba: (state) => state.tba,
      flushTime: (state) => state.flushTime,
      fragChecked: (state) => state.fragChecked,
      sectMap: (state) => state.sectMap,
      eventRange: (state) => state.eventRange,
      tbaTime: (state) => state.tbaTime,
      comments: (state) => state.comments,
      commentRange: (state) => state.commentRange,
    }),
    {
      commentChartOptionalData() {
        return {
          min: Math.floor(this.eventRange.min / 300) * 300,
          max: Math.ceil(this.eventRange.max / 300) * 300,
        };
      },
    }
  ),
  watch: {
    comments() {
      this.createOrUpdateChart();
    },
    tbaTime(v) {
      if (!this.commentChart || v < 1) return;
      let option = this.commentChart.getOption();
      option.series[0].markLine.data[0].xAxis = v;
      this.commentChart.setOption(option, true);
    },
  },
  methods: _.merge(
    Vuex.mapActions("tbaplayer", [
      "setTrackInfo",
      "setPaused",
      "shiftTbaVideoMap",
      "setBlockR",
    ]),
    {
      createOrUpdateChart() {
        // Create dataset
        this.commentTimeValueList = _.map(this.comments, "time");

        // Init echart
        let curDom = document.getElementById(this.id);
        this.commentChart = echarts.init(curDom);

        // Create option object
        let seriesObj = this.commentChartOption.seriesObjTemplate;
        seriesObj.data = this.commentTimeValueList;

        // Update xAxis data
        let axisOption = this.commentChartOption.xAxis;
        axisOption.min = this.commentChartOptionalData.min;
        axisOption.max = this.commentChartOptionalData.max;

        let option = {
          title: this.commentChartOption.title,
          grid: this.commentChartOption.grid,
          tooltip: this.commentChartOption.tooltip,
          xAxis: axisOption,
          yAxis: this.commentChartOption.yAxis,
          series: [seriesObj],
        };
        this.commentChart.setOption(option);

        // Set up events
        this.commentChart.on("click", (params) =>
          this.goToTrackFromCommentChartItem(params)
        );
      },
      toMinutes(seconds) {
        return parseInt(seconds / 60);
      },
      toHHMMSS(seconds) {
        let hr = Math.floor(seconds / 3600);
        let min = Math.floor((seconds - hr * 3600) / 60);
        let sec = Math.floor(seconds - hr * 3600 - min * 60);
        return (
          (hr == 0 ? "" : (hr < 10 ? "0" : "") + hr + ":") +
          (min < 10 ? "0" : "") +
          min +
          ":" +
          (sec < 10 ? "0" : "") +
          sec
        );
      },
      getLabelFromCommentIndex(index) {
        let commentData = this.comments[index];
        if (!commentData) return "";
        let label =
          "[" +
          this.toHHMMSS(commentData.time) +
          "] " +
          commentData.name +
          ": " +
          commentData.text;
        return label;
      },
      getLabelFromCommentChartItem(params) {
        let dataIndex = params.dataIndex;
        return this.getLabelFromCommentIndex(dataIndex);
      },
      goToTrackFromCommentChartItem(params) {
        // When a comment is clicked, go to the track
        // The video must be UNPAUSED
        // That's because there are methods that are called when tbaTime changes
        let dataIndex = params.dataIndex;
        let commentData = this.comments[dataIndex];
        let trackInfo = this.getTrackInfoObj(commentData);
        this.setTrackInfo(trackInfo);
        this.setPaused(false);
        this.$emit("reset-comment-detail");
      },
      getTrackInfoObj(commentData) {
        // As same as "parseTbaTimeToTrackInfo" from chart-linerange-echart.vue
        if (!commentData) return null;
        let time = commentData.time;
        let valid = false;
        let track = 0;
        let tVideo = 0;
        for (let [i, map] of this.sectMap.entries()) {
          if (time < map.range.min || time > map.range.max) {
            continue;
          }
          track = i;
          for (let sect of map.sects) {
            if (time < sect.tba_start || time > sect.tba_end) {
              continue;
            }
            tVideo = time + sect.video_offset - sect.tba_start;
            valid = true;
            break;
          }
          break;
        }
        return valid ? { track: track, time: tVideo } : null;
      },
    }
  ),
  mounted() {
    this.createOrUpdateChart();
  },
};
</script>

<style lang="scss" scoped>
.base-chart-container {
  width: 100%;
  height: 100%;
}
</style>
