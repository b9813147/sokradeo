<template>
  <article>
    <section class="channel-info" v-if="group !== null">
      <Row>
        <Col span="20">
          <h1 style="color: #fff;">{{ group.name }}</h1>
          <p style="color: #999; font-size: 1.5em">{{ group.description }}</p>
        </Col>
        <Col span="4">
          <Img :src="thumbnailUrl" alt="channel-logo" class="channel-logo" />
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
        <h6>{{ $t('dutyMessage') }} <b style="font-size: 15px;"> {{ userAuth.memberDuty }}</b></h6>
      </Col>
      <Col span="2" style="text-align: right">
        <Icon style="cursor: pointer" type="ios-gear-outline" size="30" @click="goToAdmin"
              v-if="setting"/>
      </Col>

    </Row>
    <Row style="padding-top: 10px;">
      <Col span="24">
        <Input
          v-model="setFilter.search"
          :placeholder="$t('searchInChannel')"
          style="width: 300px;"
          :class="[switchButton ? 'hidden':'show']"
          @on-change="debounceFilter"
        />
      </Col>
      <Col span="24">
        <!--        <i-switch size="small" @on-change="doubleGreen()"></i-switch>-->
      </Col>
      <!--切換篩選-->
      <Col span="16">
        <Icon type="search" size="30" @click="switchFilter()" :class="[switchButton ? 'show':'hidden']"></Icon>
        <Icon type="ios-color-filter-outline" size="30" @click="switchFilter()" :class="[switchButton ? 'hidden':'show']"></Icon>
      </Col>
    </Row>

    <!--  篩選  -->
    <section class="list">
      <div style="line-height: 20px; background-color: #333;margin-top: 20px;" :class="[switchButton ? 'show':'hidden']">
        <dl class="filter">
          <dt><span>{{ $t('rating') }} |</span></dt>
          <dd>
            <RadioGroup type="button" v-on:on-change="selectRating">
              <Radio class="ivu-radio-wrapper-checked" label="none"><span>{{ $t('base.all') }}</span></Radio>
              <Radio v-for="(v,k) in total.list.rating" v-bind:label="v.id" :key="k">
                                <span>{{ v.text }}
                                    <span style="color: #ACD6FF"> {{ v.value }}</span>
                                </span>
              </Radio>
            </RadioGroup>
          </dd>
        </dl>
        <dl class="filter">
          <dt><span>{{ $t('filters.subjectField') }} |</span></dt>
          <dd>
            <RadioGroup type="button" v-on:on-change="selectSubjectFields">
              <Radio class="ivu-radio-wrapper-checked" label="none"><span>{{ $t('base.all') }}</span></Radio>
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
            <RadioGroup type="button" v-on:on-change="selectGrades">
              <Radio label="none" class="ivu-radio-wrapper-checked">
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
        <!--                <dl class="filter">-->
        <!--                    <dt><span>{{ $t('filters.lectureType') }}</span></dt>-->
        <!--                    <dd>|-->
        <!--                        <RadioGroup type="button" v-on:on-change="selectLectureTypes">-->
        <!--                            <Radio label="none"><span>{{ $t('base.all') }}</span></Radio>-->
        <!--                            <Radio v-for="v in filter.lectureTypes.list" v-bind:key="v.type" v-bind:label="v.value">-->
        <!--                                <span>{{v.text}}</span>-->
        <!--                            </Radio>-->
        <!--                        </RadioGroup>-->
        <!--                    </dd>-->
        <!--                </dl>-->
        <!--                <dl class="filter">-->
        <!--                    <dt><span>{{ $t('filters.tbaFeature') }}</span></dt>-->
        <!--                    <dd>|-->
        <!--                        <RadioGroup type="button" v-on:on-change="selectTbaFeatures">-->
        <!--                            <Radio label="none"><span>{{ $t('base.all') }}</span></Radio>-->
        <!--                            <Radio v-for="v in filter.tbaFeatures.list" v-bind:key="v.type" v-bind:label="v.value">-->
        <!--                                <span>{{v.text}}</span>-->
        <!--                            </Radio>-->
        <!--                        </RadioGroup>-->
        <!--                    </dd>-->
        <!--                </dl>-->
        <!--                <dl class="filter">-->
        <!--                    <dt><span>{{ $t('filters.tbaFeature') }}</span></dt>-->
        <!--                    <dd>|-->
        <!--                        <RadioGroup type="button" v-on:on-change="selectTbaFeatures">-->
        <!--                            <Radio label="none"><span>{{ $t('base.all') }}</span></Radio>-->
        <!--                            <Radio v-for="v in filter.tbaFeatures.list" v-bind:key="v.type" v-bind:label="v.value">-->
        <!--                                <span>{{v.text}}</span></Radio>-->
        <!--                        </RadioGroup>-->
        <!--                    </dd>-->
        <!--                </dl>-->
      </div>


      <!--            <section class="channel-info" v-if="group !== null">-->
      <!--                <h1 style="color: #fff;">{{group.name}}</h1>-->
      <!--                <p style="color: #999; font-size: 1.5em">{{group.description}}</p>-->
      <!--            </section>-->
      <!--<strong style="font-size: 30px;"> {{ this.groupName }}</strong>-->
      <!--<sub style="color: #788397">-->
      <!--共計-->
      <!--      {{ this.groupChannels.list.length }}-->
      <!--</sub>-->
      <!--            <hr>-->
      <Row>
        <Col span="16">
          <h2>{{ $t('filterChannelTotal') }}: {{ pager.total }}</h2>
        </Col>
      </Row>

      <Row style="padding-top: 10px;" v-infinite-scroll="loadMore" infinite-scroll-throttle-delay="500" infinite-scroll-disabled="groupChannels.busy" infinite-scroll-distance="10">
        <Col v-bind:xs="24" v-bind:sm="12" v-bind:md="8" v-bind:lg="6" v-for="(v,i) in groupChannels.list" :key="i">
          <cpnt-thumb-content
            v-bind:item="v"
            v-bind:isMobile="isMobileBrowser"
            v-on:execute="exeContent"
          ></cpnt-thumb-content>
        </Col>
      </Row>
      <Row v-if="pager.busy">
        <Col class="demo-spin-col" span="24">
          <Spin fix>
            <Icon type="load-c" size=30 class="demo-spin-icon-load"></Icon>
          </Spin>
        </Col>
      </Row>
    </section>
  </article>
</template>

<script>
import _                from 'lodash';
import Vuex             from 'vuex';
import CpntThumbContent from '../../../app/components/thumb-content.vue';
import base64           from 'hi-base64'
import infiniteScroll   from 'vue-infinite-scroll'

export default {
  directives: {infiniteScroll},
  components: {
    'cpnt-thumb-content': CpntThumbContent,
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
      groupChannels: {
        list: [],
      },
      total        : {
        list: []
      },
      pager        : {
        busy: false,
        // itemPerLoad: 5, //  每次加載數
        pageIndex: 1, // 請求頁數
        last_page: 0,
        // per: 12,
        total: 0,
        // current: 1,
        // prev: 1,
        // next: 1,
      },
      group        : {
        id         : null,
        name       : null,
        description: null,
        thumbnail  : null
      },
      setting      : false,
      switchButton : true,
      filter       : {
        eduStages    : {list: [], selected: 'none'},
        grades       : {list: [], selected: 'none'},
        subjectFields: {list: [], selected: 'none'},
        lectureTypes : {list: [], selected: 'none'},
        tbaFeatures  : {list: [], selected: 'none'},
        years        : {list: [], selected: 'none'},
        rating       : {list: [], selected: 'none'},
      },
      debounce     : null,
      setFilter    : {
        eduStages    : null,
        grades       : null,
        subjectFields: null,
        lectureTypes : null,
        tbaFeatures  : null,
        years        : null,
        search       : null,
        rating       : null
      },
      userAuth     : {
        memberDuty: null
      },
      loadMoreUrl  : null
    };
  },

  watch: {
    '$route': 'init',
    'groupChannels.list'() {
      // this.list.total = this.groupChannels.list.length;
      // console.log(this.groupChannels.list.length)
    },
    // 'setFilter.eduStages'() {
    //   this.getFilter();
    // },
    'setFilter.grades'() {
      this.getFilter();
    },
    'setFilter.subjectFields'() {
      this.getFilter();
    },
    'setFilter.rating'() {
      this.getFilter();
    },
    // 'setFilter.lectureTypes'() {
    //   this.getFilter();
    // },
    // 'setFilter.tbaFeatures'() {
    //   this.getFilter();
    // },
    // 'setFilter.years'() {
    //   this.getFilter();
    // },
  },

  methods: {
    init() {
      this.getGroupName();
      this.getMyGroupChannel();
      this.checkMemberDuty();
      this.getGroupChannelCount();
      // this.getFilters();
    },

    exeContent(content) {
      let channelId = parseInt(this.$route.params.channelId)
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

      let channelId = this.$route.params.channelId;
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

      }).catch((e) => {

        console.log(e);
      });

    },
    getMyGroupChannel() {
      let _this = this;
      let channelId = this.$route.params.channelId
      let url = '/exhibition/tbavideo/get-my-group-channel';
      _this.loadMoreUrl = url;

      axios.get(url, {
        params: {
          channelId: channelId,
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
      let channelId = this.$route.params.channelId
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
        data.data.list.rating.sort(function (a, b) {
          return a.type < b.type ? 1 : -1;
        });

        this.total = data.data;

      }).catch((e) => {

        console.log(e);
      });
    },
    goToAdmin() {
      let url = `/getTicket`;

      let channel = base64.encode(`${this.$route.params.channelId}`);
      // console.log(this.$route.params.channelId,channel)
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
          return channel.id === Number(this.$route.params.channelId)
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
    /*getFilters() {
      let url = '/exhibition/tbavideo/get-filters';
      let channelId = this.$route.params.channelId;
      axios.get(url, {
        params: {
          channelId: channelId
        }
      }).then((response) => {
        // console.log(response.data.data.grades)
        // if (typeof response.data.data.eduStages !== 'undefined') {
        //   this.filter.eduStages.list = response.data.data.eduStages
        // }
        // if (typeof response.data.data.grades !== 'undefined') {
        //   this.filter.grades.list = response.data.data.grades
        // }
        // if (typeof response.data.data.subjectFields !== 'undefined') {
        //   this.filter.subjectFields.list = response.data.data.subjectFields
        // }
        // if (typeof response.data.data.lectureTypes !== 'undefined') {
        //   this.filter.lectureTypes.list = response.data.data.lectureTypes
        // }
        // if (typeof response.data.data.tbaFeatures !== 'undefined') {
        //   this.filter.tbaFeatures.list = response.data.data.tbaFeatures
        // }
        // if (typeof response.data.data.years !== 'undefined') {
        //   this.filter.years.list = response.data.data.years
        // }
      });
    },*/
    selectEduStages(v) {
      this.selectFilter({
        type : 'eduStages',
        value: v
      })
    },
    selectGrades(v) {
      this.selectFilter({
        type : 'grades',
        value: v
      })
    },
    selectSubjectFields(v) {
      this.setFilter.grades = null
      this.selectFilter({
        type : 'subjectFields',
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
    selectRating(v) {
      this.setFilter.subjectFields = null
      this.setFilter.grades = null
      this.selectFilter({
        type : 'rating',
        value: v
      })
    },

    selectFilter(data) {
      // if (data.type === 'eduStages') {
      //   this.setFilter.eduStages = data.value;
      // }
      if (data.type === 'grades') {
        this.setFilter.grades = data.value;
      }
      if (data.type === 'subjectFields') {
        this.setFilter.subjectFields = data.value;
      }
      if (data.type === 'rating') {
        this.setFilter.rating = data.value;
      }

      // if (data.type === 'lectureTypes') {
      //   this.setFilter.lectureTypes = data.value;
      // }
      // if (data.type === 'tbaFeatures') {
      //   this.setFilter.tbaFeatures = data.value;
      // }
      // if (data.type === 'years') {
      //   this.setFilter.years = data.value;
      // }
    },
    debounceFilter() {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getFilter();
      }, 600);
    },
    getFilter() {
      let _this = this;
      let channelId = this.$route.params.channelId;
      let url = '/exhibition/tbavideo/get-my-group-filter';
      _this.loadMoreUrl = url;
      // this.groupChannels.list = []
      axios.get(url, {
        params: {
          channelId: channelId,
          filter   : _this.setFilter
        },
      }).then((data) => {

        data = data.data;
        if (!data.status) {
          return;
        }

        _this.pager.last_page = data.data.list.last_page
        _this.pager.total = data.data.list.total
        _this.total.list.grade = data.data.grade
        _this.total.list.subject = data.data.subject
        _this.groupChannels.list = data.data.list.data;
        _this.pager.pageIndex = 1;
      })
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

    loadMore() {
      let _this = this
      let channelId = this.$route.params.channelId
      this.pager.busy = true

      _this.pager.pageIndex++;
      if (this.pager.pageIndex > this.pager.last_page) {
        this.pager.busy = false
        return false;
      }

      axios.get(_this.loadMoreUrl, {
        params: {
          channelId: channelId,
          page     : _this.pager.pageIndex,
          filter   : _this.setFilter
        },
      }).then((response) => {
        let data = response.data.data.list
        _.forEach(data.data, function (v) {
          _this.groupChannels.list.push(v)
        })
        this.pager.busy = false
      });
    },
    switchFilter() {
      let _this = this;
      _this.switchButton = !_this.switchButton;
      this.initFilterSelected();
    },
    doubleGreen() {

    }
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
  /*height: 100px;*/
  margin-bottom: 20px;
  position: relative;
  /*border: 1px solid #eee;*/
}
</style>
