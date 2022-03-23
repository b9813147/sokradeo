<template>
  <article>
    <!-- Top -->
    <!--	<Carousel autoplay v-bind:autoplay-speed="4000">-->
    <section class="channel-info">
      <Row>
        <Col span="20">
          <h1 style="color: #fff;">{{ exhibitInfo.district.name }}</h1>
        </Col>
        <Col span="4">
          <Img :src="thumbnailUrl" alt="channel-logo" class="channel-logo" />
        </Col>
      </Row>
    </section>
    <Row style="padding-bottom: 10px;">
      <Col span="5">
        <div>
          <h3>
            {{ $t('publicChannel') }}
          </h3>
          <Icon type="ios-world-outline" size="45"></Icon>
          <h1 style="display: inline; margin-left: 35px;color: #ACD6FF">
            {{ exhibitInfo.district.public_total }}
          </h1>
        </div>
      </Col>
      <Col span="5">
        <div>
          <h3>
            {{ $t('channelTotal') }}
          </h3>
          <Icon type="ios-film-outline" size="45"></Icon>
          <h1 style="display: inline; margin-left: 35px;color: #ACD6FF">
            {{ exhibitInfo.district.all_total }}
          </h1>
        </div>
      </Col>
      <Col span="5">
        <div>
          <h3>
            {{ $t('doubleGreenLightTotal') }}
          </h3>
          <Icon type="ios-circle-filled" size="45"></Icon>
          <!--                    <Icon type="ios-film-outline" size="45"></Icon>-->
          <h1 style="display: inline; margin-left: 35px; color: #ACD6FF;">
            {{ exhibitInfo.district.doubleGreenLight_total }}
          </h1>
        </div>
      </Col>
      <Col span="5">
        <div>
          <h3>
            {{ $t('clickTotal') }}
          </h3>
          <Icon type="eye" size="45"></Icon>
          <h1 style="display: inline; margin-left: 35px;color: #ACD6FF">
            {{ exhibitInfo.district.hits_total }}
          </h1>
        </div>
      </Col>

      <Col span="4" style="text-align: right">
        <Icon style="cursor: pointer" type="ios-gear-outline" size="30" @click="goToAdmin"
              v-if="setting"/>
      </Col>
    </Row>
    <!--        <div class="divider"><span>{{ $t('sets.channels.excellent') }}</span></div>-->

    <!-- Channel list -->
    <Row type="flex" align="middle">
      <Col
        span="2"
        v-for="(item, k) in carousel.channel.excs"
        :key="k"
      >
        <!-- The "execute" event has to be used with v-on -->
        <cpnt-thumb-channel
          :item="item"
          v-on:execute="exeChannel"
        >
        </cpnt-thumb-channel>
      </Col>
    </Row>

    <cpnt-thumb-filter :item="exhibitInfo"></cpnt-thumb-filter>
  </article>
</template>

<script>
import _                from 'lodash'
import Vuex             from 'vuex'
import CpntThumbChannel from '../../../app/components/thumb-channel.vue'
import CpntThumbContent from '../../../app/components/thumb-content.vue'
import CpntThumbFilter  from '../../../../exhibition/app/components/thumb-filter.vue'
import base64           from "hi-base64";

export default {

  components: {
    'cpnt-thumb-channel': CpntThumbChannel,
    'cpnt-thumb-content': CpntThumbContent,
    'cpnt-thumb-filter' : CpntThumbFilter,
  },

  data() {
    return {
      exhibitInfo: {
        cms     : {
          tops: [],
          hits: [],
          news: [],
        },
        channel : {
          excs: [],
          hits: [],
        },
        district: {
          id            : null,
          name          : null,
          description   : null,
          public_total  : null,
          hits_total    : null,
          all_total     : null,
          subject_fields: {
            language         : null,
            mathematics      : null,
            socialHumanities : null,
            scienceTechnology: null,
            arts             : null,
            physical         : null,
            comprehensive    : null,
            technology       : null,
            other            : null
          },
          grade_fields  : []
        },
      },
      fields     : {
        subjectFields: [],
        gradeFields  : [],
      },
      carousel   : {
        cms    : {
          tops: [],
          hits: [],
          news: [],
        },
        channel: {
          excs: [],
          hits: [],
        },
      },
      setting    : false
    }
  },

  computed: _.merge(
      Vuex.mapState(['path']),
      Vuex.mapGetters(['logined']),
      {
        thumbnailUrl() {
          let url = "/images/app/tw/teammodel/original-black-small.png";
          if (this.exhibitInfo.district.thumbnail)
            url =
                this.path.district +
                this.exhibitInfo.district.id +
                "/" +
                this.exhibitInfo.district.thumbnail +
                "?" +
                Math.random();
          return url;
      },
    }
  ),

  methods: _.merge(
      Vuex.mapActions(['setKeyword', 'setChannel']),

      {
        init() {
          this.getExhibitInfo()
          this.getDistrictField()
          this.districtAuth();
        },
        goToAdmin() {
          let url = `/getTicket`;

          let districtId = base64.encode(`${this.exhibitInfo.district.id}`);

          axios.get(url).then((response) => {
            window.open(`${process.env.MIX_APP_ADMIN_URL}district?district=${districtId}&ticket=${response.data}`, 'admin')
          });
        },
        getDistrictField() {
          axios.get('/district/tbavideo/get-fields', {
            params: {
              abbr: this.$store.state.abbr
            }
          })
              .then((data) => {
                data = data.data
                if (!data.status) {
                  return
                }
                this.fields = data.data
              })
              .catch((e) => {
                console.log(e)
              })
        },
        getExhibitInfo() {

          axios.get('/district/tbavideo/get-exhibit-info', {
            params: {
              abbr: this.$store.state.abbr
            }
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
          this.carousel.channel.excs = _.orderBy(
            this.exhibitInfo.channel.excs,
            ["list_top", "total_content"],
            ["desc", "desc"]
          );
          // this.carousel.channel.hits = _.chunk(this.exhibitInfo.channel.hits, 7)
        },

        exeContentAsOpen(contentId) {
          this.$router.push({
            name  : 'content',
            params: {contentId: contentId},
          })
          //window.open('tbavideo/watch-as-open?contentId=' + contentId)
        },

        exeContent(content) {
          let groupIds = _.join(_.uniq(_.map(content.group_channels, 'group_id')), ',');
          let channelId = _.join(_.uniq(_.map(content.group_channels, 'id')), ',');
          this.$router.push({
            name  : 'content',
            params: {contentId: content.id},
            query : {groupIds: groupIds, channelId: channelId},
          });

          //let groupIds = _.join(_.uniq(_.map(content.group_channels, 'group_id')), ',')
          //window.open('tbavideo/watch?contentId=' + content.id + '&groupIds=' + groupIds)
        },

        exeChannel(channel) {
          this.$router.push({
            path: `/myChannel/${channel.id}`,
          });
        },

        districtAuth() {
          let districts = null;
          if (this.$store.state.user && this.$store.state.user.district_user.length > 0) {
            _.filter(this.$store.state.user.district_user, (district_user => {
              districts = _.filter(district_user.district, (district => {
                if (district.abbr === this.$store.state.abbr) {
                  return district.id
                }
              }));
              _.filter(districts, (district => {
                if (district.id === district_user.districts_id) {
                  if (district_user.member_duty === 'Admin') {
                    this.setting = true;
                  }
                }
              }));
            }));
          }

        }
      }
  ),

  mounted() {

    this.setChannel(null)
    this.init()

  }

}
</script>

<style lang="scss" scoped>
.channel-info {
  .channel-logo {
    width: 50%;
    object-fit: contain;
    float: right;
    border-radius: 50%;
  }
}

.ivu-row {
  padding: 0 8px;
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

dl {
  display: table;
  color: #fff;
  font-size: 1.2em;

  dt,
  dd {
    display: table-cell;
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
  margin: 0px 0px;

  .ivu-radio-group-item {
    margin: 0 10px;
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
</style>
