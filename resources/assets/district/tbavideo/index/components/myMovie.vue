owner_tba_total
<template>
  <article>
    <Row class="my-sokrates-info">
      <!-- Avatar -->
      <Col :lg="4" :md="4" :sm="4" :xs="24">
        <Img class="my-avatar" :src="urlAvatar"/>
      </Col>
      <!-- My Sokrates Info -->
      <Col :lg="20" :md="20" :sm="20" :xs="24">
        <!-- User -->
        <Row type="flex" justify="start">
          <Col :lg="6" :md="8" :sm="12" :xs="14">
            <h1 style="color: #fff;">{{ $store.state.user.name }}</h1>
            <h3>
              <Icon type="university"></Icon>
              <router-link :to="'/myChannel/'+ $store.state.user.group_channel_id" style="color: #ACD6FF;">
                {{ $store.state.user.group_channel_name }}
              </router-link>
            </h3>
          </Col>
        </Row>
        <!-- General Info -->
        <Row type="flex" justify="start">
          <Col :lg="6" :md="8" :sm="12" :xs="14">
            <h3>
              {{ $t('channelTotal') }}
              <span class="highlight-info">
                {{ exhibitInfo.channel.data.all_total }}
              </span>
            </h3>
          </Col>
          <Col :lg="6" :md="8" :sm="12" :xs="14">
            <h3>
              {{ $t('doubleGreenLightTotal') }}
              <span class="highlight-info">
                {{ exhibitInfo.channel.data.doubleGreenLight_total }}
              </span>
            </h3>
          </Col>
          <Col :lg="6" :md="8" :sm="12" :xs="14">
            <h3>
              {{ $t('clickTotal') }}
              <span class="highlight-info">
                {{ exhibitInfo.channel.data.hits_total }}
              </span>
            </h3>
          </Col>
          <Col :lg="6" :md="8" :sm="12" :xs="14">
            <h3>
              {{ $t('tbaMarkerCount') }}
              <span class="highlight-info">
                {{ exhibitInfo.channel.data.total_mark }}
              </span>
            </h3>
          </Col>
          <!--          <Col :lg="4" :md="6" :sm="12" :xs="12">-->
          <!--            <h3>-->
          <!--              {{ $t('publicTbaMarkerCount') }}-->
          <!--            </h3>-->
          <!--            <Icon type="android-bookmark" size="45"></Icon>-->
          <!--            <h1 style="display: inline; margin-left: 35px;color: #ACD6FF">-->
          <!--              0-->
          <!--            </h1>-->
          <!--          </Col>-->
          <!--          <Col :lg="4" :md="6" :sm="12" :xs="12">-->
          <!--            <h3>-->
          <!--              {{ $t('ownerTbaMarkerCount') }}-->
          <!--            </h3>-->
          <!--            <Icon type="android-bookmark" size="45"></Icon>-->
          <!--            <h1 style="display: inline; margin-left: 35px;color: #ACD6FF">-->
          <!--              {{ exhibitInfo.channel.data.owner_event_total }}-->
          <!--            </h1>-->
          <!--          </Col>-->
        </Row>
      </Col>
    </Row>
    <hr>
    <Row>
      <h1>{{ $t('channels.my') }} ({{ (exhibitInfo.channel.excs) ? exhibitInfo.channel.excs.length : null }})</h1>
      <div v-if="channelSwitch">
        <Row v-for="(items, k) in carousel.channel.excs" :key="k">
          <Col span="3" v-for="(item, j) in items" v-bind:key="j">
            <cpnt-thumb-channel v-bind:item="item" v-on:execute="exeChannel"></cpnt-thumb-channel>
          </Col>
        </Row>
      </div>
      <div v-else>
        <Row v-for="(items, k) in carousel.channel.excs" :key="k">
          <Col span="3" v-for="(item, j) in items" v-bind:key="j" v-if="k<=0">
            <cpnt-thumb-channel v-bind:item="item" v-on:execute="exeChannel"></cpnt-thumb-channel>
          </Col>
        </Row>

      </div>
      <div align="center">
        <Icon @click="channelSwitch = !channelSwitch" style="cursor:pointer" type="chevron-down"></Icon>
      </div>


      <!--      <div v-for="(items, i) in carousel.channel.excs" v-bind:key="i">-->
      <!--        <Col span="2" v-for="(item, j) in items" v-bind:key="j">-->
      <!--          <pre> {{ (j <= 6) ? 1 : 0 }}</pre>-->
      <!--          <cpnt-thumb-channel v-bind:item="item" v-on:execute="exeChannel"></cpnt-thumb-channel>-->
      <!--        </Col>-->
      <!--      </div>-->
    </Row>
    <!--    <div class="divider"><span>{{ $t('watch') }}</span></div>-->
    <hr>
    <h1>{{ $t('channels.my-movie') }} ( {{ (groupChannels.list) ? groupChannels.list.length : null }} )</h1>
    <!--    <Row>-->
    <!--      <Col v-bind:xs="24" v-bind:sm="12" v-bind:md="8" v-bind:lg="6" v-for="(v,i) in groupChannels.list"-->
    <!--           :key="i">-->
    <!--        <cpnt-thumb-content v-bind:item="v" v-on:execute="exeContent"></cpnt-thumb-content>-->
    <!--      </Col>-->
    <!--    </Row>-->
    <Row>
      <Col span="24">
        <div style="text-align: left;">
          <Icon type="android-apps" style="font-size: 30px;padding-right: 10px; cursor:pointer" @click="display= false"></Icon>
          <Icon type="android-menu" style="font-size: 30px; cursor:pointer" @click="display= true"></Icon>
        </div>
      </Col>
    </Row>
    <!--      <i-switch v-model="display" @click="display= !display"></i-switch>-->
    <Row v-if="!display" style="padding-top: 10px;" v-infinite-scroll="loadMore" infinite-scroll-throttle-delay="500" infinite-scroll-distance="10">
      <Col v-bind:xs="24" v-bind:sm="12" v-bind:md="8" v-bind:lg="6" v-for="(v,i) in groupChannels.list" :key="i">
        <cpnt-thumb-content v-if="!display" v-bind:item="v" v-on:execute="exeContent"></cpnt-thumb-content>
      </Col>
    </Row>

    <Row v-if="display" style="padding-top: 10px;" v-infinite-scroll="loadMore" infinite-scroll-throttle-delay="500" infinite-scroll-distance="10">
      <cpnt-thumb-table v-if="display" :item="groupChannels.list" :pager="pager" v-on:execute="exeContent"></cpnt-thumb-table>
    </Row>

    <Row v-if="pager.busy">
      <Col class="demo-spin-col" span="24">
        <Spin fix>
          <Icon type="load-c" size=30 class="demo-spin-icon-load"></Icon>
        </Spin>
      </Col>
    </Row>
  </article>
</template>

<script>
import _                from 'lodash';
import Vuex             from 'vuex';
import CpntThumbContent from '../../../app/components/thumb-content.vue';
import CpntThumbChannel from "../../../app/components/thumb-channel";
import CpntThumbTable   from '../../../app/components/thumb-table';

export default {
  // name: 'pending-channel.vue',
  components: {
    'cpnt-thumb-content': CpntThumbContent,
    'cpnt-thumb-channel': CpntThumbChannel,
    'cpnt-thumb-table'  : CpntThumbTable,
  },

  computed: _.merge(
      Vuex.mapState(['path']),
      Vuex.mapGetters(['urlAvatar'])
  ),

  data() {
    return {
      groupChannels: {
        list: [],
      },
      exhibitInfo  : {
        channel: {
          excs: null,
          data: {
            all_total             : null,
            doubleGreenLight_total: null,
            hits_total            : null,
            public_total          : null,
            owner_event_total     : null,
            owner_tba_total       : null
          },
        }

      },
      carousel     : {
        channel: {
          excs: null,
        }
      },
      list         : {
        items: [],
        total: 0,
      },
      // pager        : {
      //   per    : 12,
      //   total  : 1,
      //   current: 1,
      //   prev   : 1,
      //   next   : 1,
      // },
      pager        : {
        busy     : false,
        pageIndex: 1, // 請求頁數
        last_page: 0,
        total    : 0,
      },
      groupId      : 0,
      channelSwitch: false,
      display      : false,
      loadMoreUrl  : null
    };
  },
  watch  : {
    '$route': 'init',

  },
  methods: {
    init() {
      this.getGroupChannel();
      this.getExhibitInfo();
    },
    getGroupChannel() {
      let _this = this;
      let url = '/exhibition/tbavideo/get-my-movies';
      _this.loadMoreUrl = url;

      axios.get(url, {
        params: {
          // groupId: this.groupId,
        },
      }).then((data) => {

        let result = data.data.data;
        if (!data.status) {
          return;
        }

        _this.pager.last_page = result.list.last_page
        _this.pager.total = result.list.total
        _this.groupChannels.list = result.list.data;
        _this.pager.pageIndex = 1;

      }).catch((e) => {

        console.log(e);
      });
    },
    exeChannel(channel) {
      this.$router.push({
        path: `/myChannel/${channel.id}`,
      });
    },

    exeContent(content) {
      let groupIds = _.join(_.uniq(_.map(content.group_channels, 'group_id')), ',');
      let channelId = _.join(_.uniq(_.map(content.group_channels, 'id')), ',');
      this.$router.push({
        name  : 'content',
        params: {contentId: content.id},
        query : {
          groupIds : groupIds,
          channelId: channelId
        },
      });
    },
    getExhibitInfo() {
      axios
          .get("/user/tbavideo/get-exhibit-info", {
            params: {
              excsReqd: false,
            },
          })
          .then((data) => {
            data = data.data
            if (!data.status) {
              return
            }

            this.exhibitInfo = data.data
            // 屬性不存在，就給預設值
            this.exhibitInfo.channel.excs = _.forEach(this.exhibitInfo.channel.excs, function (v) {
              if (!v.hasOwnProperty('total_content')) {
                v.total_content = 0
              }
            });
            this.parseExhibitsToCarousels()
            // console.log(this.exhibitInfo,this.carousel.channel.excs)

          })
          .catch((e) => {
            console.log(e)
          })

    },

    parseExhibitsToCarousels() {

      // this.carousel.cms.tops = _.chunk(this.exhibitInfo.cms.tops, 3)
      // this.carousel.cms.hits = _.chunk(this.exhibitInfo.cms.hits, 4)
      // this.carousel.cms.news = _.chunk(this.exhibitInfo.cms.news, 4)
      this.carousel.channel.excs = _.chunk(_.orderBy(this.exhibitInfo.channel.excs, ['total_content'], ['desc']), 8)
      // this.carousel.channel.hits = _.chunk(this.exhibitInfo.channel.hits, 7)
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
          // channelId: channelId,
          // page     : _this.pager.pageIndex,
          // filter   : _this.setFilter
        },
      }).then((response) => {
        let data = response.data.data.list
        _.forEach(data.data, function (v) {
          _this.groupChannels.list.push(v)
        })
        this.pager.busy = false
      });
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

.ivu-row {
  padding: 10px 0px;
}

.my-avatar {
  width: 100%;
  max-width: 150px;
  height: 80%;

  display: inline-block;
  border-radius: 50%;

  padding: 10px
}

.my-sokrates-info {
  line-height: 2;
}

.highlight-info {
  color: #ACD6FF;
  display: inline;
  margin-left: 10px;
}

.specially:hover {
  color: #acd6ff;
  cursor: pointer;
}

.divider {
  margin-top: 32px;
  border-top: 2px solid #666;
  text-align: center;

  span {
    position: relative;
    top: -16px;
    padding: 0 20px;
    color: #fff;
    background: #1b1b1b;
    font-size: x-large;
  }

}
</style>
