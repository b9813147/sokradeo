<template>
  <article>
    <section class="channel-info" v-if="group !== null">
      <Row>
        <Col span="20">
          <h1 style="color: #fff;">{{ group.name }}</h1>
          <div v-for="(v, k) in group.district" v-if="group.district">
            <a :href="'/district/' +  v.districts.abbr">
              <h5 style="color: #ACD6FF;">{{ v.district_lang.name }}</h5>
            </a>
          </div>
        </Col>
        <Col span="4">
          <Img :src="thumbnailUrl" alt="channel-logo" class="channel-logo"/>
        </Col>
      </Row>
    </section>

    <Row>
      <Col span="5" style="text-align: center">
        <div>
          <h3>
            {{ $t('publicChannel') }}
          </h3>
          <Icon type="ios-world-outline" size="45"></Icon>
          <h1 style="display: inline; margin-left: 35px; color: #ACD6FF;">
            {{ total.list.public_total }}
          </h1>
        </div>
      </Col>
      <Col span="5" style="text-align: center">
        <div>
          <h3>
            {{ $t('channelTotal') }}
          </h3>
          <Icon type="ios-film-outline" size="45"></Icon>
          <h1 style="display: inline; margin-left: 35px; color: #ACD6FF;">
            {{ total.list.all_total }}
          </h1>
        </div>
      </Col>
      <Col span="5" style="text-align: center">
        <div>
          <h3>
            {{ $t('doubleGreenLightTotal') }}
          </h3>
          <Icon type="ios-circle-filled" size="45"></Icon>
          <h1 style="display: inline; margin-left: 35px; color: #ACD6FF;">
            {{ total.list.doubleGreenLight_total }}
          </h1>
        </div>
      </Col>
      <Col span="5" style="text-align: center">
        <div>
          <h3>
            {{ $t('clickTotal') }}
          </h3>
          <Icon type="eye" size="45"></Icon>
          <h1 style="display: inline; margin-left: 35px; color: #ACD6FF;">
            {{ total.list.hits_total }}
          </h1>
        </div>
      </Col>
      <Col span="2" style="text-align: right">
        <h6>{{ $t('dutyMessage') }}</h6>
        <h6><b style="font-size: 15px;"> {{ userAuth.memberDuty }}</b></h6>
      </Col>
      <Col span="2" style="text-align: right">
        <Icon style="cursor: pointer" type="ios-gear-outline" size="30" @click="goToAdmin"
              v-if="setting"/>
      </Col>
    </Row>
    <Row style="padding-top: 10px;">
      <!--切換篩選-->
      <Col span="16">
        <Icon type="search" size="30" @click="switchFilter()" :class="[switchButton ? 'show':'hidden']"></Icon>
        <Icon type="funnel" size="30" @click="switchFilter()" :class="[switchButton ? 'hidden':'show']"></Icon>
      </Col>
    </Row>

    <!--  篩選  -->
    <section class="list">
      <!-- Search Filter -->
      <div
          :class="[switchButton ? 'hidden':'show']"
          style="width: 300px; padding: 10px 0"
      >
        <Input
            v-model="setFilter.search"
            :placeholder="$t('searchInChannel')"
            @on-change="debounceFilter"
            clearable
        />
      </div>
      <!-- Radio Filters -->
      <div :class="[switchButton ? 'show':'hidden']" style="line-height: 20px; background-color: #333;margin-top: 20px;">
        <dl class="filter">
          <dt><span>{{ $t('rating') }} |</span></dt>
          <dd>
            <RadioGroup type="button" v-on:on-change="selectRating" v-model="selectedRadioFilter.rating">
              <Radio :class="[!setFilter.rating ? 'ivu-radio-wrapper-checked' : '']" label="none">
                <span>{{ $t('base.all') }}</span>
              </Radio>
              <Radio v-for="(v,k) in total.list.rating" v-bind:label="v.id" :key="k">
                <span>{{ v.text }}
                    <span style="color: #ACD6FF"> {{ v.value }}</span>
                </span>
              </Radio>
            </RadioGroup>
          </dd>
        </dl>
        <dl class="filter">
          <dt><span>{{ $t('subject') }} |</span></dt>
          <dd>
            <RadioGroup type="button" v-on:on-change="selectSubjectFields" v-model="selectedRadioFilter.subjectFields">
              <Radio :class="[!setFilter.subjectFields ? 'ivu-radio-wrapper-checked' : '']" label="none">
                <span>{{ $t('base.all') }}</span>
              </Radio>
              <Radio v-for="(v,k) in total.list.subject" v-bind:label="v.id" :key="k">
                <span>{{ v.text }}
                    <span style="color: #ACD6FF"> {{ v.value }}</span>
                </span>
              </Radio>
            </RadioGroup>
          </dd>
        </dl>
        <dl class="filter">
          <dt><span>{{ $t('filters.grade') }} |</span></dt>
          <dd>
            <RadioGroup type="button" v-on:on-change="selectGrades" v-model="selectedRadioFilter.grade">
              <Radio :class="[!setFilter.grades ? 'ivu-radio-wrapper-checked' : '']" label="none">
                <span>{{ $t('base.all') }}</span>
              </Radio>
              <Radio v-for="(v,k) in total.list.grade" v-bind:label="v.id" :key="k">
                <span>{{ v.text }}
                    <span style="color: #ACD6FF"> {{ v.value }}</span>
                </span>
              </Radio>
            </RadioGroup>
          </dd>
        </dl>
      </div>
      <Row>
        <Col span="16">
          <h2>{{ $t('filterChannelTotal') }}: {{ pager.total }}</h2>
        </Col>
        <Col span="24">
          <div style="text-align: left;" v-if="userAuth.memberDuty === $t('admin')">
            <Icon type="android-apps" style="font-size: 30px;padding-right: 10px; cursor:pointer" @click="display= false"></Icon>
            <Icon type="android-menu" style="font-size: 30px; cursor:pointer" @click="display= true"></Icon>
          </div>
        </Col>
      </Row>
      <!--      <i-switch v-model="display" @click="display= !display"></i-switch>-->
      <Row v-if="!display && !pager.busy" style="padding-top: 10px;">
        <Col v-bind:xs="24" v-bind:sm="12" v-bind:md="8" v-bind:lg="6" v-for="(v,i) in groupChannels.list" :key="i">
          <cpnt-thumb-content
              v-if="!display"
              :item="v"
              :filter="setFilter"
              :isMobile="isMobileBrowser"
              v-on:execute="exeContent"
          >
          </cpnt-thumb-content>
        </Col>
      </Row>

      <Row v-if="display && !pager.busy" style="padding-top: 10px;">
        <cpnt-thumb-table v-if="display" :item="groupChannels.list" :filter="setFilter" :pager="pager" v-on:execute="exeContent"></cpnt-thumb-table>
      </Row>

      <Row v-if="pager.busy">
        <Col class="demo-spin-col" span="24">
          <Spin fix>
            <Icon type="load-c" size=30 class="demo-spin-icon-load"></Icon>
          </Spin>
        </Col>
      </Row>
      <Row>
        <Col class="demo-spin-col" span="24">
          <Page style="padding-top: 30px; text-align: center" :page-size="pager.size" :total="pager.total" :current="pager.pageIndex" @on-change="loadMore"></Page>
        </Col>
      </Row>
    </section>
  </article>
</template>

<script>
import _                from 'lodash';
import Vuex             from 'vuex';
import CpntThumbContent from '../../../app/components/thumb-content.vue';
import CpntThumbTable   from '../../../app/components/thumb-table';
import base64           from 'hi-base64'

export default {
  components: {
    'cpnt-thumb-content': CpntThumbContent,
    'cpnt-thumb-table'  : CpntThumbTable,
  },

  computed: _.merge(
      Vuex.mapState(['path', 'user', 'isMobileBrowser']),
      {
        channelId() {
          return parseInt(this.$route.params.channelId);
        },
        thumbnailUrl() {
          let url = "/images/app/tw/teammodel/original-black-small.png";
          if (this.group.thumbnail)
            url =
                this.path.groupChannel +
                this.channelId +
                "/" +
                this.group.thumbnail +
                "?" +
                Math.random();
          return url;
        },
      }
  ),

  data() {
    return {
      groupChannels      : {
        list: [],
      },
      total              : {
        list: []
      },
      pager              : {
        busy     : false,
        pageIndex: 1, // 請求頁數
        last_page: 0,
        total    : 0,
        size     : 100,
      },
      group              : {
        id         : null,
        name       : null,
        description: null,
        thumbnail  : null,
        abbr       : null,
        district   : null
      },
      setting            : false,
      switchButton       : true,
      filter             : {
        eduStages    : {list: [], selected: 'none'},
        grades       : {list: [], selected: 'none'},
        subjectFields: {list: [], selected: 'none'},
        lectureTypes : {list: [], selected: 'none'},
        tbaFeatures  : {list: [], selected: 'none'},
        years        : {list: [], selected: 'none'},
        rating       : {list: [], selected: 'none'},
      },
      debounce           : null,
      setFilter          : {
        eduStages    : null,
        grades       : null,
        subjectFields: null,
        lectureTypes : null,
        tbaFeatures  : null,
        years        : null,
        search       : null,
        rating       : null
      },
      selectedRadioFilter: {
        rating       : null,
        subjectFields: null,
        grade        : null,
      },
      userAuth           : {
        memberDuty: null
      },
      loadMoreUrl        : null,
      display            : false
    };
  },

  watch: {
    '$route': 'init',
    'groupChannels.list'() {
    },
    'setFilter.grades'() {
      this.getFilter();
    },
    'setFilter.subjectFields'() {
      this.getFilter();
    },
    'setFilter.rating'() {
      this.getFilter();
    },
  },

  methods: {
    init() {
      this.getGroupName();
      this.getMyGroupChannel();
      this.checkMemberDuty();
      this.getGroupChannelCount();
    },
    exeContent(content) {
      let channelId = this.channelId
      let result = _.find(content.group_channels, ['id', channelId]);

      this.$router.push({
        name  : 'content',
        params: {contentId: content.id},
        query : {
          groupIds : _.toString(result.group_id),
          channelId: _.toString(result.id)
        },
      });
    },
    getGroupName() {
      if (!document.cookie) {
        location.reload();
      }
      this.$emit('check-logined', true, false, false, false);

      let channelId = this.channelId;
      let url = '/exhibition/tbavideo/get-channel-info';

      axios.get(url, {
        params: {
          channelId: channelId,
        },
      }).then((data) => {
        data = data.data;

        if (!data.status) {
          return;
        }
        this.group.name = data.data.name;
        this.group.description = data.data.description;
        this.group.thumbnail = data.data.thumbnail;
        this.group.district = data.data.district_group;

      }).catch((e) => {

        console.log(e);
      });

    },
    getMyGroupChannel() {
      let _this = this;
      let channelId = this.channelId
      let url = '/exhibition/tbavideo/get-my-group-channel';
      _this.loadMoreUrl = url;

      axios.get(url, {
        params: {
          channelId: channelId,
          size     : _this.pager.size,
        },
      }).then((data) => {

        data = data.data;

        if (!data.status) {

          return;
        }
        _this.pager.last_page = data.data.list.last_page
        _this.pager.total = data.data.list.total
        _this.groupChannels.list = data.data.list.data;
        _this.pager.pageIndex = 1;

      }).catch((e) => {

        console.log(e);
      });
    },
    getGroupChannelCount() {
      let channelId = this.channelId
      let url = '/exhibition/tbavideo/get-my-group-count';

      axios.get(url, {
        params: {
          channelId: channelId,
        },
      }).then((data) => {

        data = data.data;
        if (!data.status) {

          return;
        }

        this.total = data.data;

      }).catch((e) => {

        console.log(e);
      });
    },
    goToAdmin() {
      let url = `/getTicket`;

      let channel = base64.encode(`${this.channelId}`);

      axios.get(url).then((response) => {
        window.open(`${process.env.MIX_APP_ADMIN_URL}?channel=${channel}&ticket=${response.data}`, 'admin')
      });
    },
    checkMemberDuty() {
      let groups = this.$store.state.user.groups;
      this.userAuth.memberDuty = null
      this.setting = false;
      // 用group ID 去反推 channel ID
      groups.forEach((g, k) => {
        let channel = g.channels.filter((channel, k) => {
          return channel.id === Number(this.channelId)
        });
        channel.forEach((v, k) => {
          if (v.group_id === g.id) {
            switch (g.pivot.member_duty) {
              case 'Admin':
                this.userAuth.memberDuty = this.$t('admin')
                this.setting = true
                break
              case 'Expert':
                this.userAuth.memberDuty = this.$t('expert');
                break
              case 'General':
                this.userAuth.memberDuty = this.$t('general')
                break
            }
          }
        });
      });
      (this.userAuth.memberDuty === null) ? this.userAuth.memberDuty = this.$t('guest') : ''
    },
    selectRating(v) {
      this.selectedRadioFilter.rating = v;
      this.resetSubjectFields();
      this.resetGrades();
      this.selectFilter({
        type : 'rating',
        value: v
      })
    },
    selectSubjectFields(v) {
      this.selectedRadioFilter.subjectField = v;
      this.resetGrades();
      this.selectFilter({
        type : 'subjectFields',
        value: v
      })
    },
    selectGrades(v) {
      this.selectedRadioFilter.grade = v;
      this.selectFilter({
        type : 'grades',
        value: v
      })
    },
    selectEduStages(v) {
      this.selectFilter({
        type : 'eduStages',
        value: v
      })
    },
    selectLectureTypes(v) {
      this.selectFilter({
        type : 'lectureTypes',
        value: v
      })
    },
    selectTbaFeatures(v) {
      this.selectFilter({
        type : 'tbaFeatures',
        value: v
      })
    },
    selectYears(v) {
      this.selectFilter({
        type : 'years',
        value: v
      })
    },
    selectFilter(data) {
      let type = data.type;
      switch (type) {
        case "rating":
          this.setFilter.rating = data.value;
          break;
        case "subjectFields":
          this.setFilter.subjectFields = data.value;
          break;
        case "grades":
          this.setFilter.grades = data.value;
          break;
      }
    },
    resetAllRadioFilters() {
      this.selectedRadioFilter.rating = null;
      this.selectedRadioFilter.subjectFields = null;
      this.selectedRadioFilter.grade = null;
    },
    resetSubjectFields() {
      this.setFilter.subjectFields = null;
      this.filter.subjectFields.selected = null;
      this.selectedRadioFilter.subjectFields = null;
    },
    resetGrades() {
      this.setFilter.grades = null;
      this.filter.grades.selected = null;
      this.selectedRadioFilter.grade = null;
    },
    debounceFilter() {
      // This method will delay the execution of the search method
      // in order to grab search input as a whole and avoid repeated requests
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getFilter();
      }, 600);
    },
    getFilter() {
      let _this = this;
      let channelId = this.channelId;
      let url = "/exhibition/tbavideo/get-my-group-filter";
      _this.loadMoreUrl = url;
      axios
          .get(url, {
            params: {
              channelId: channelId,
              size     : _this.pager.size,
              filter   : _this.setFilter,
            },
          })
          .then((data) => {
            data = data.data;
            if (!data.status) return;

            // Update Data
            _this.total.list.grade = data.data.grade;
            _this.total.list.subject = data.data.subject;
            _this.groupChannels.list = data.data.list.data;

            // Setup pagination
            _this.pager.last_page = data.data.list.last_page;
            _this.pager.total = data.data.list.total;
            _this.pager.pageIndex = 1;
          });
    },
    initFilterSelected() {
      let data = {
        eduStages    : null,
        grades       : null,
        subjectFields: null,
        lectureTypes : null,
        tbaFeatures  : null,
        years        : null,
        rating       : null,
      };
      this.setFilter = data;
    },
    loadMore(p) {
      // console.log(p)
      let _this = this
      let channelId = this.channelId
      this.pager.busy = true

      _this.pager.pageIndex = p;
      if (this.pager.pageIndex > this.pager.last_page) {
        this.pager.busy = false
        return false;
      }

      axios.get(_this.loadMoreUrl, {
        params: {
          channelId: channelId,
          page     : _this.pager.pageIndex,
          size     : _this.pager.size,
          filter   : _this.setFilter
        },
      }).then((response) => {
        let data = response.data.data.list
        // _.forEach(data.data, function (v) {
        _this.groupChannels.list = data.data
        // })
        this.pager.busy = false
      });
    },
    switchFilter() {
      let _this = this;
      _this.switchButton = !_this.switchButton;
      this.resetAllRadioFilters();
      this.initFilterSelected();
    },
  },
  mounted() {
    this.init();
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

  .channel-logo {
    width: 50%;
    object-fit: contain;
    float: right;
    border-radius: 50%;
  }
}

dl {
  display: table;
  color: #fff;
  font-size: 1.2em;

  dt,
  dd {
    display: table-cell;
    vertical-align: top
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
    color: #32C2F2;
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
  margin-bottom: 20px;
  position: relative;
}
</style>
