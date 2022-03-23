<template>
  <article>
    <!-- Top -->
    <Carousel autoplay v-bind:autoplay-speed="4000">
      <CarouselItem v-for="(items, i) in carousel.cms.tops" v-bind:key="i">
        <Row>
          <Col span="8" class="thumb" style="height:100%;" v-for="(item, j) in items" v-bind:key="j">
            <img style="width: 100%; height: 100%; object-fit: cover;padding:0 20px"
                 v-bind:src="path.top+item.id+'/'+item.cms_thumb"
                 v-on:click="exeContentAsOpen(item)">

          </Col>
        </Row>
      </CarouselItem>
    </Carousel>
    <!-- 暫時註解 推薦影片  -->
    <div class="divider"><span>{{ $t('sets.contents.recommended') }}</span></div>
    <Carousel dots="none">
      <CarouselItem v-for="(items, i) in carousel.cms.recommend" v-bind:key="i">
        <Row>
          <Col span="6" v-for="(item, j) in items" v-bind:key="j">
            <cpnt-thumb-content v-bind:item="item.tba" v-on:execute="exeContent"></cpnt-thumb-content>
          </Col>
        </Row>
      </CarouselItem>
    </Carousel>
    <!-- 熱門點閱影片 -->
    <!--    <div class="divider"><span>{{ $t('sets.contents.popular') }}</span></div>
        <Carousel dots="none">
          <CarouselItem v-for="(items, i) in carousel.cms.hits" v-bind:key="i">
            <Row>
              <Col span="6" v-for="(item, j) in items" v-bind:key="j">
                <cpnt-thumb-content v-bind:item="item" v-on:execute="exeContent"></cpnt-thumb-content>
              </Col>
            </Row>
          </CarouselItem>
        </Carousel>-->
    <!-- 最新影片上架 -->
    <div class="divider"><span>{{ $t('sets.contents.newest') }}</span></div>
    <Carousel dots="none">
      <CarouselItem v-for="(items, i) in carousel.cms.news" v-bind:key="i">
        <Row>
          <Col span="6" v-for="(item, j) in items" v-bind:key="j">
            <cpnt-thumb-content v-bind:item="item" v-on:execute="exeContent"></cpnt-thumb-content>
          </Col>
        </Row>
      </CarouselItem>
    </Carousel>
    <!-- 精選頻道 -->
    <div class="divider"><span>{{ $t('sets.channels.excellent') }}</span></div>
    <!--<Carousel dots="none">-->
    <Row type="flex" align="middle">
      <Col span="24" v-if="exhibitInfo.channel.excs.length > 0">
        <div class="searchbar-container">
          <Input
              class="searchbar"
              v-model="search"
              icon="ios-search"
              placeholder="Search in Channels.."
              v-on:on-click="selectMatchItem(exhibitInfo.channel.excs, search)"
          >
          </Input>
        </div>
      </Col>

      <Col v-for="(item, i) in carousel.channel.excs" v-bind:key="i" span="4">
        <cpnt-thumb-channel v-bind:item="item" v-on:execute="exeChannel"></cpnt-thumb-channel>
      </Col>
    </Row>
    <div
        v-if="exhibitInfo.channel.excs.length > 0"
        style="text-align: center; padding-top: 10px"
    >
      <Page
          :total="pager.dataCount"
          :page-size="pager.pageSize"
          @on-change="changePage"
      ></Page>
    </div>
    <!--</Carousel>-->
    <!-- 熱門學院頻道
    <div class="divider"><span>{{ $t('sets.channels.popular') }}</span></div>
    <Carousel dots="none">
      <CarouselItem v-for="(items, i) in carousel.channel.hits" v-bind:key="i">
        <Row>
          <Col span="4" v-for="(item, j) in items" v-bind:key="j">
            <cpnt-thumb-channel v-bind:item="item" v-on:execute="exeChannel"></cpnt-thumb-channel>
          </Col>
        </Row>
      </CarouselItem>
    </Carousel>
      -->
  </article>
</template>

<script>
import _                from 'lodash'
import Vuex             from 'vuex'
import CpntThumbChannel from '../../../app/components/thumb-channel.vue'
import CpntThumbContent from '../../../app/components/thumb-content.vue'

export default {

  components: {
    'cpnt-thumb-channel': CpntThumbChannel,
    'cpnt-thumb-content': CpntThumbContent,
  },

  data() {
    return {
      exhibitInfo: {
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
      pager      : {
        pageSize : 60,
        dataCount: 0,
        current  : 1
      },
      historyData: [],
      search     : ""
    }
  },

  computed: _.merge(
      Vuex.mapState(['path']),
      Vuex.mapGetters(['logined']),
  ),


  methods: _.merge(
      Vuex.mapActions(['setKeyword', 'setChannel']),

      {
        init() {

          this.getExhibitInfo()

        },

        getExhibitInfo() {

          axios.get('/exhibition/tbavideo/get-exhibit-info', {
            params: {}
          })
              .then((data) => {
                data = data.data

                if (!data.status) {
                  return
                }
                this.exhibitInfo = data.data
                this.parseExhibitsToCarousels();
              })
              .catch((e) => {
                console.log(e)
              })

        },

        parseExhibitsToCarousels() {

          let localeId = 65
          switch (this.$i18n.locale) {
            case 'tw':
              localeId = 40
              break
            case 'cn':
              localeId = 37
              break
            case 'en':
              localeId = 65
              break
          }

          this.carousel.cms.tops = _.chunk(this.exhibitInfo.cms.tops, 3)
          this.carousel.cms.hits = _.chunk(this.exhibitInfo.cms.hits, 4)
          this.carousel.cms.news = _.chunk(this.exhibitInfo.cms.news, 4)

          let recommend = []
          this.exhibitInfo.cms.recommend.forEach(function (item) {
            if(item.locales_id === localeId) {
              recommend.push(item)
            }
          })

          this.carousel.cms.recommend = _.chunk(recommend, 4)
          // this.carousel.cms.recommend = _.chunk(this.exhibitInfo.cms.recommend, 4)
          this.carousel.channel.excs = _.chunk(this.exhibitInfo.channel.excs, this.pager.pageSize)[0]
          this.carousel.channel.hits = _.chunk(this.exhibitInfo.channel.hits, 7)

          this.pager.dataCount = this.exhibitInfo.channel.excs.length;
          this.historyData = _.chunk(this.exhibitInfo.channel.excs, 60);
        },

        exeContentAsOpen(content) {
          this.$router.push({
            name  : 'content',
            params: {contentId: content.id},
            query : {channelId: content.channel_id},
          })
          //window.open('tbavideo/watch-as-open?contentId=' + contentId)
        },

        exeContent(content) {
          // this.$emit('check-logined');
          // if (!this.logined) {
          //   return
          // }

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
          // this.$emit('check-logined');
          // if (!this.logined) {
          //   return
          // }
          // this.setKeyword('')
          // this.setChannel(channel)
          // this.$router.push({name: 'filtered'})

          this.$router.push({
            path: `/myChannel/${channel.id}`,
          });
        },

        changePage(index) {
          const currPage = index - 1
          let data = null;
          data = this.historyData[currPage];
          this.carousel.channel.excs = [];
          this.carousel.channel.excs = data;
        },

        /**
         * @param {Object} lists 所有資料
         * @param {string} keyWord 查詢的關鍵詞
         */
        selectMatchItem(lists, keyWord) {
          let resArr = [];
          lists.filter(item => {
            if (item.name.includes(keyWord)) {
              resArr.push(item);
            }
          })
          if (keyWord === '') {
            this.pager.current = 1;
            this.pager.dataCount = this.exhibitInfo.channel.excs.length;
            this.historyData = _.chunk(this.exhibitInfo.channel.excs, 60);
            return this.carousel.channel.excs = this.historyData[0];
          }
          this.historyData = _.chunk(resArr, 60);
          this.carousel.channel.excs = this.historyData[0];
          this.pager.dataCount = resArr.length;

        },

      }
  ),

  mounted() {

    this.setChannel(null)
    this.init()

  }

}
</script>

<style lang="scss" scoped>
.ivu-row {
  padding: 0 8px;

}

.carousel-film {
  height: 150px;
  background-image: url("/storage/frame_png-01.png");
  //background-repeat:no-repeat;
  background-size: 100% 260px;
  display: flex;
  align-items: center;
  justify-content: center;
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

.searchbar-container {
  padding: 0px 10px 10px 10px;
  text-align: left;

  .searchbar {
    width: 15%;
    cursor: pointer;
  }
}
</style>
