<template>
  <article>
    <Row style="padding-top: 10px">
      <Col span="24">
        <i-input
          v-model="setFilter.search"
          :placeholder="$t('searchInChannel')"
          :class="[switchButton ? 'hidden' : 'show']"
          @on-change="debounceFilter"
          style="width: 300px"
        ></i-input>
      </Col>
      <!--切換篩選-->
      <Col span="16">
        <Icon
          type="search"
          size="30"
          @click="switchFilter()"
          :class="[switchButton ? 'show' : 'hidden']"
        ></Icon>
        <Icon
          type="funnel"
          size="30"
          @click="switchFilter()"
          :class="[switchButton ? 'hidden' : 'show']"
        ></Icon>
      </Col>
    </Row>

    <!--  篩選  -->
    <section class="list">
      <div
        :class="[switchButton ? 'show' : 'hidden']"
        style="line-height: 20px; background-color: #333; margin-top: 20px"
      >
        <dl class="filter">
          <dt>
            <span>{{ $t("rating") }} |</span>
          </dt>
          <dd>
            <RadioGroup
              type="button"
              v-on:on-change="selectRating"
              v-model="selectedRadioFilter.rating"
            >
              <Radio
                :class="[!setFilter.rating ? 'ivu-radio-wrapper-checked' : '']"
                label="none"
                ><span>{{ $t("base.all") }}</span></Radio
              >
              <Radio
                v-for="(v, k) in total.list.rating"
                v-bind:label="v.id"
                :key="k"
              >
                <span>
                  {{ v.text }}
                  <span style="color: #acd6ff"> {{ v.value }}</span>
                </span>
              </Radio>
            </RadioGroup>
          </dd>
        </dl>
        <dl class="filter">
          <dt>
            <span>{{ $t("subject") }} |</span>
          </dt>
          <dd>
            <RadioGroup
              type="button"
              v-on:on-change="selectdistrictSubjectFields"
              v-model="selectedRadioFilter.districtSubjectFields"
            >
              <Radio
                :class="[
                  !setFilter.districtSubjectFields
                    ? 'ivu-radio-wrapper-checked'
                    : '',
                ]"
                label="none"
                ><span>{{ $t("base.all") }}</span></Radio
              >
              <Radio
                v-for="(v, k) in total.list.subject"
                v-bind:label="v.id"
                :key="k"
              >
                <span>
                  {{ v.text }}
                  <span style="color: #acd6ff"> {{ v.value }}</span>
                </span>
              </Radio>
            </RadioGroup>
          </dd>
        </dl>
        <dl class="filter">
          <dt>
            <span>{{ $t("filters.grade") }} |</span>
          </dt>
          <dd>
            <RadioGroup
              type="button"
              v-on:on-change="selectGrades"
              v-model="selectedRadioFilter.grade"
            >
              <Radio
                :class="[!setFilter.grades ? 'ivu-radio-wrapper-checked' : '']"
                label="none"
              >
                <span>{{ $t("base.all") }}</span>
              </Radio>
              <Radio
                v-for="(v, k) in total.list.grade"
                v-bind:label="v.id"
                :key="k"
              >
                <span>
                  {{ v.text }}
                  <span style="color: #acd6ff"> {{ v.value }}</span>
                </span>
              </Radio>
            </RadioGroup>
          </dd>
        </dl>
      </div>

      <Row>
        <Col span="16">
          <h2>{{ $t("filterChannelTotal") }}: {{ pager.total }}</h2>
        </Col>
        <Col span="24">
          <div style="text-align: left">
            <Icon
              type="android-apps"
              style="font-size: 30px; padding-right: 10px; cursor: pointer"
              @click="display = false"
            ></Icon>
            <Icon
              type="android-menu"
              style="font-size: 30px; cursor: pointer"
              @click="display = true"
            ></Icon>
          </div>
        </Col>
      </Row>
      <!--      <i-switch v-model="display" @click="display= !display"></i-switch>-->
      <Row v-if="!display" style="padding-top: 10px">
        <Col
          v-bind:xs="24"
          v-bind:sm="12"
          v-bind:md="8"
          v-bind:lg="6"
          v-for="(v, i) in groupChannels.list"
          :key="i"
        >
          <cpnt-thumb-content
            v-if="!display"
            v-bind:item="v"
            :filter="setFilter"
            v-on:execute="exeContent"
          ></cpnt-thumb-content>
          <!--          <pre>{{ v }}</pre>-->
        </Col>
      </Row>

      <Row v-if="display" style="padding-top: 10px">
        <cpnt-thumb-table
          v-if="display"
          :item="groupChannels.list"
          :filter="setFilter"
          :pager="pager"
          v-on:execute="exeContent"
        ></cpnt-thumb-table>
      </Row>

      <Row v-if="pager.busy">
        <Col class="demo-spin-col" span="24">
          <Spin fix>
            <Icon type="load-c" size="30" class="demo-spin-icon-load"></Icon>
          </Spin>
        </Col>
      </Row>
      <Row>
        <Col class="demo-spin-col" span="24">
          <Page
            style="padding-top: 30px; text-align: center"
            :page-size="pager.size"
            :total="pager.total"
            :current="pager.pageIndex"
            @on-change="loadMore"
          ></Page>
        </Col>
      </Row>
    </section>
  </article>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";
import CpntThumbContent from "../../../exhibition/app/components/thumb-content.vue";
import CpntThumbTable from "../../../exhibition/app/components/thumb-table";
import base64 from "hi-base64";
import infiniteScroll from "vue-infinite-scroll";

export default {
  props: ["item"],
  name: "thumb-filter",
  directives: { infiniteScroll },
  components: {
    "cpnt-thumb-content": CpntThumbContent,
    "cpnt-thumb-table": CpntThumbTable,
  },

  computed: _.merge(Vuex.mapState(["path", "user"])),
  data() {
    return {
      groupChannels: {
        list: [],
      },
      total: {
        list: [],
      },
      pager: {
        busy: false,
        pageIndex: 1, // 請求頁數
        last_page: 0,
        total: 0,
        size: 100,
      },
      group: {
        id: null,
        name: null,
        description: null,
        thumbnail: null,
        abbr: null,
        district: null,
      },
      setting: false,
      switchButton: true,
      filter: {
        eduStages: { list: [], selected: "none" },
        grades: { list: [], selected: "none" },
        districtSubjectFields: { list: [], selected: "none" },
        lectureTypes: { list: [], selected: "none" },
        tbaFeatures: { list: [], selected: "none" },
        years: { list: [], selected: "none" },
        rating: { list: [], selected: "none" },
      },
      debounce: null,
      setFilter: {
        eduStages: null,
        grades: null,
        districtSubjectFields: null,
        lectureTypes: null,
        tbaFeatures: null,
        years: null,
        search: null,
        rating: null,
      },
      selectedRadioFilter: {
        rating: null,
        districtSubjectFields: null,
        grade: null,
      },
      userAuth: {
        memberDuty: null,
      },
      loadMoreUrl: null,
      display: false,
    };
  },

  watch: {
    $route: "init",
    "groupChannels.list"() {
      // this.list.total = this.groupChannels.list.length;
      // console.log(this.groupChannels.list.length)
    },
    "setFilter.grades"() {
      this.getFilter();
    },
    "setFilter.districtSubjectFields"() {
      this.getFilter();
    },
    "setFilter.rating"() {
      this.getFilter();
    },
  },

  methods: {
    init() {
      // this.getGroupName();
      this.getMyGroupChannel();
      // this.checkMemberDuty();
      this.getGroupChannelCount();
      // this.getFilters();
    },

    exeContent(content) {
      let channelId = _.join(_.uniq(_.map(content.group_channels, "id")), ",");
      let groupIds = _.join(
        _.uniq(_.map(content.group_channels, "group_id")),
        ","
      );
      this.$router.push({
        name: "content",
        params: { contentId: content.id },
        query: {
          groupIds: groupIds,
          channelId: channelId,
        },
      });
    },
    getGroupName() {
      if (!document.cookie) {
        location.reload();
      }
      this.$emit("check-logined", true, false, false, false);

      let channelId = this.$route.params.channelId;
      let url = "/exhibition/tbavideo/get-channel-info";

      axios
        .get(url, {
          params: {
            channelId: channelId,
          },
        })
        .then((data) => {
          data = data.data;

          if (!data.status) {
            return;
          }
          this.group.name = data.data.name;
          this.group.description = data.data.description;
          this.group.thumbnail = data.data.thumbnail;
          this.group.abbr = _.find(data.data.district_group).districts.abbr
            ? _.find(data.data.district_group).districts.abbr
            : null;
          this.group.district = _.find(
            _.find(data.data.district_group).district_lang
          ).name
            ? _.find(_.find(data.data.district_group).district_lang).name
            : null;
        })
        .catch((e) => {
          console.log(e);
        });
    },
    getMyGroupChannel() {
      let _this = this;
      let abbr = this.$store.state.abbr;
      let url = "/exhibition/tbavideo/get-district-channel";
      _this.loadMoreUrl = url;

      axios
        .get(url, {
          params: {
            abbr: abbr,
            size: _this.pager.size,
          },
        })
        .then((data) => {
          data = data.data;

          if (!data.status) {
            return;
          }

          _this.pager.last_page = data.data.list.last_page;
          _this.pager.total = data.data.list.total;
          _this.groupChannels.list = data.data.list.data;

          _this.pager.pageIndex = 1;
        })
        .catch((e) => {
          console.log(e);
        });
    },
    getGroupChannelCount() {
      let abbr = this.$store.state.abbr;
      let url = "/exhibition/tbavideo/get-district-count";

      axios
        .get(url, {
          params: {
            abbr: abbr,
          },
        })
        .then((data) => {
          data = data.data;
          if (!data.status) {
            return;
          }
          data.data.list.rating.sort(function (a, b) {
            return a.type < b.type ? 1 : -1;
          });

          this.total = data.data;
        })
        .catch((e) => {
          console.log(e);
        });
    },
    goToAdmin() {
      let url = `/getTicket`;

      let channel = base64.encode(`${this.$route.params.channelId}`);
      // console.log(this.$route.params.channelId,channel)
      axios.get(url).then((response) => {
        window.open(
          `${process.env.MIX_APP_ADMIN_URL}?channel=${channel}&ticket=${response.data}`,
          "admin"
        );
      });
    },
    checkMemberDuty() {
      let groups = this.$store.state.user.groups;
      this.userAuth.memberDuty = null;
      this.setting = false;
      // 用group ID 去反推 channel ID
      groups.forEach((g, k) => {
        let channel = g.channels.filter((channel, k) => {
          return channel.id === Number(this.$route.params.channelId);
        });
        channel.forEach((v, k) => {
          if (v.group_id === g.id) {
            switch (g.pivot.member_duty) {
              case "Admin":
                this.userAuth.memberDuty = this.$t("admin");
                this.setting = true;
                break;
              case "Expert":
                this.userAuth.memberDuty = this.$t("expert");
                break;
              case "General":
                this.userAuth.memberDuty = this.$t("general");
                break;
            }
          }
        });
      });
      this.userAuth.memberDuty === null
        ? (this.userAuth.memberDuty = this.$t("guest"))
        : "";
    },
    selectEduStages(v) {
      this.selectFilter({
        type: "eduStages",
        value: v,
      });
    },
    selectGrades(v) {
      this.selectedRadioFilter.grade = v;
      this.selectFilter({
        type: "grades",
        value: v,
      });
    },
    selectdistrictSubjectFields(v) {
      this.selectedRadioFilter.districtSubjectFields = v;
      this.resetGrades();
      this.selectFilter({
        type: "districtSubjectFields",
        value: v,
      });
    },
    selectLectureTypes(v) {
      this.selectFilter({
        type: "lectureTypes",
        value: v,
      });
    },
    selectTbaFeatures(v) {
      this.selectFilter({
        type: "tbaFeatures",
        value: v,
      });
    },
    selectYears(v) {
      this.selectFilter({
        type: "years",
        value: v,
      });
    },
    selectRating(v) {
      this.selectedRadioFilter.rating = v;
      this.resetDistrictSubjectFields();
      this.resetGrades();
      this.selectFilter({
        type: "rating",
        value: v,
      });
    },
    selectFilter(data) {
      let type = data.type;
      switch (type) {
        case "rating":
          this.setFilter.rating = data.value;
          break;
        case "districtSubjectFields":
          this.setFilter.districtSubjectFields = data.value;
          break;
        case "grades":
          this.setFilter.grades = data.value;
          break;
      }
    },
    resetDistrictSubjectFields() {
      this.setFilter.districtSubjectFields = null;
      this.filter.districtSubjectFields.selected = null;
      this.selectedRadioFilter.districtSubjectFields = null;
    },
    resetGrades() {
      this.setFilter.grades = null;
      this.filter.grades.selected = null;
      this.selectedRadioFilter.grade = null;
    },
    debounceFilter() {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getFilter();
      }, 600);
    },
    getFilter() {
      let _this = this;
      let abbr = this.$store.state.abbr;
      let url = "/exhibition/tbavideo/get-district-filter";
      _this.loadMoreUrl = url;

      axios
        .get(url, {
          params: {
            abbr: abbr,
            filter: _this.setFilter,
            size: _this.pager.size,
          },
        })
        .then((data) => {
          data = data.data;
          if (!data.status) {
            return;
          }

          _this.pager.last_page = data.data.list.last_page;
          _this.pager.total = data.data.list.total;
          _this.total.list.grade = data.data.grade;
          _this.total.list.subject = data.data.subject;
          _this.groupChannels.list = data.data.list.data;
          _this.pager.pageIndex = 1;
        });
    },
    initFilterSelected() {
      let data = {
        eduStages: null,
        grades: null,
        districtSubjectFields: null,
        lectureTypes: null,
        tbaFeatures: null,
        years: null,
        rating: null,
      };
      this.setFilter = data;
    },

    loadMore(p) {
      let _this = this;
      let abbr = this.$store.state.abbr;
      this.pager.busy = true;

      _this.pager.pageIndex = p;
      if (this.pager.pageIndex > this.pager.last_page) {
        this.pager.busy = false;
        return false;
      }

      axios
        .get(_this.loadMoreUrl, {
          params: {
            abbr: abbr,
            page: _this.pager.pageIndex,
            size: _this.pager.size,
            filter: _this.setFilter,
          },
        })
        .then((response) => {
          let data = response.data.data.list;
          // _.forEach(data.data, function (v) {
          _this.groupChannels.list = data.data;
          // })
          this.pager.busy = false;
        });
    },
    switchFilter() {
      let _this = this;
      _this.switchButton = !_this.switchButton;
      this.initFilterSelected();
    },
  },
  mounted() {
    this.init();
    // console.log(this.item);
    // console.log(this.$store.state.abbr)
    // this.init();
  },
};
</script>

<style lang="scss" scoped>
p {
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.channel-info {
  padding-top: 20px;
  padding-bottom: 32px;
}

dl {
  display: table;
  color: #fff;
  font-size: 1.2em;

  dt,
  dd {
    display: table-cell;
    vertical-align: top;
  }

  dt {
    width: 120px;
    text-align: right;

    span {
      padding-right: 15px;
    }
  }

  dd a {
    display: inline-block;
    padding: 0px 4px;
    color: #fff;
  }

  dd a.active {
    color: #00d9d9;
    background-color: #000;
    border-radius: 5px;
  }
}

.filter {
  font-size: 16px;
  margin: 0px 0px;

  .ivu-radio-group-item {
    margin: 0 -7px;
    color: #fff;
    background-color: transparent;
    border-color: transparent;
    border-radius: 4px;
  }

  .ivu-radio-group-item.ivu-radio-wrapper-checked {
    color: #32c2f2;
    background-color: #000;
  }

  .ivu-radio-group-item::before {
    background: transparent;
  }
}

.demo-spin-icon-load {
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

.hidden {
  cursor: pointer;
  display: none;
}

.show {
  cursor: pointer;
  display: block;
}

.demo-spin-col {
  /*height: 100px;*/
  margin-bottom: 20px;
  position: relative;
  /*border: 1px solid #eee;*/
}
</style>
