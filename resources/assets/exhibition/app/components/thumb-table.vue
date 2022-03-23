<template>
  <div>
    <Row type="flex" justify="end" class="mb-20">
      <i-col>
        <div v-if="close">
          <!--          <pre>{{ item }}</pre>-->
          <i-button type="primary" size="large" @click="exportData" v-if="this.$router.history.current.params.channelId">
            <icon type="ios-download-outline"></icon>
            {{ $t('export') }}
          </i-button>
        </div>


      </i-col>
    </Row>
    <i-table width="10px"
             :columns="columns"
             :data="resources"
             ref="table">
    </i-table>
    <!--        <i-table width="10px"-->
    <!--                 :loading="isLoading"-->
    <!--                 :columns="columns"-->
    <!--                 :data="resources"-->
    <!--                 v-infinite-scroll="loadMore" infinite-scroll-throttle-delay="500" infinite-scroll-disabled="groupChannels.busy" infinite-scroll-distance="10"-->
    <!--                 ref="table">-->
    <!--        </i-table>-->
    <!--    <div v-infinite-scroll="loadMore" infinite-scroll-throttle-delay="500" infinite-scroll-disabled="groupChannels.busy" infinite-scroll-distance="10"></div>-->
  </div>
</template>

<script>
import _              from 'lodash'
import Vuex           from 'vuex'
import infiniteScroll from 'vue-infinite-scroll'

export default {
  name : "thumb-table",
  props: ['item', 'filter', 'pager'],
  // props: ['item'],
  // directives: {infiniteScroll},
  computed: _.merge(
      Vuex.mapState(['path'])
  ),
  data() {
    return {
      columns  : [],
      resources: [],
      close    : true,
    }
  },
  watch  : {
    item(v) {
      this.processingData(v)
    }
  },
  methods: {
    init() {
      let _this = this;
      if (this.$store.state.abbr) {
        _this.close = false;
      }
      _this.defaultColumn();
      _this.processingData(_this.item)
    },

    defaultColumn() {
      this.columns = [
        {
          title    : this.$t('key'),
          key      : 'key',
          sortable : true,
          className: 'demo-table-info-column',
          maxWidth : 80,
          align    : 'center',
        },
        {
          title    : this.$t('global'),
          key      : 'status',
          sortable : true,
          className: 'demo-table-info-column',
          maxWidth : 90,
          align    : 'center',
          // render: (h, item) => {
          //   if (_.find(item.row.group_channels).pivot.content_public === 1) {
          //     return h('div', [
          //       h('Icon', {
          //         props: {
          //           type: 'ios-world-outline'
          //         },
          //         style: {
          //           color: 'rgb(100, 250, 1)'
          //         },
          //       }),
          //       h('strong', {
          //         style: {
          //           color: '#4F4F4F'
          //         },
          //       }, 'V')
          //     ]);
          //   }
          //   return h('div', [
          //     h('Icon', {
          //       props: {
          //         type: 'ios-world-outline'
          //       },
          //       style: {
          //         color: 'rgb(111,112,109)'
          //       },
          //     }),
          //     h('strong', {
          //       style: {
          //         color: '#4F4F4F'
          //       },
          //     }, 'X'),
          //   ]);
          // }
        },
        {
          title    : this.$t('doubleGreenLight'),
          key      : 'doubleGreen',
          sortable : true,
          className: 'demo-table-info-column',
          maxWidth : 90,
          align    : 'center',
          // render: (h, item) => {
          //   if (item.row.p >= 70 && item.row.t >= 70) {
          //     return h('div', [
          //       h('Icon', {
          //         props: {
          //           type: 'ios-circle-filled'
          //         },
          //         style: {
          //           color: 'rgb(100, 250, 1)'
          //         },
          //       }),
          //       h('strong', {
          //         style: {
          //           color: '#4F4F4F'
          //         },
          //       }, 'V')
          //     ]);
          //   }
          //   return h('div', [
          //     h('Icon', {
          //       props: {
          //         type: 'ios-circle-filled'
          //       },
          //       style: {
          //         color: 'rgb(111,112,109)'
          //       },
          //     }),
          //     h('strong', {
          //       style: {
          //         color: '#4F4F4F'
          //       },
          //     }, 'X')
          //   ]);
          // }
        },
        {
          title    : this.$t('image'),
          key      : 'thumbnail',
          className: 'demo-table-info-column',
          maxWidth : 70,
          align    : 'center',
          render   : (h, item) => {
            return h('div', [
              h('img', {
                attrs: {
                  src: item.row.thumbnail
                },
                style: {
                  width : '70px',
                  height: '40px',
                  cursor: 'pointer',
                },
                on   : {
                  click: () => {
                    this.$emit('execute', item.row)
                  }
                },
              }, '點擊'),
            ]);
          }
        },
        {
          title    : this.$t('lessonName'),
          key      : 'channel_name',
          className: 'demo-table-info-column',
          ellipsis : true,
          align    : 'left'
        },
        {
          title    : this.$t('lecture_date'),
          key      : 'lecture_date',
          sortable : true,
          className: 'demo-table-info-column',
          maxWidth : 110,
          align    : 'center'
        },
        {
          title    : this.$t('teacher'),
          key      : 'teacher',
          sortable : true,
          className: 'demo-table-info-column',
          maxWidth : 80,
          align    : 'center'
        },
        {
          title    : this.$t('rating'),
          key      : 'rating',
          sortable : true,
          className: 'demo-table-info-column',
          maxWidth : 110,
          align    : 'center'
        },
        {
          title    : this.$t('subject'),
          key      : 'subject',
          className: 'demo-table-info-column',
          maxWidth : 80,
          align    : 'center'
        },
        {
          title    : this.$t('grade'),
          key      : 'grade',
          sortable : true,
          className: 'demo-table-info-column',
          maxWidth : 80,
          align    : 'center'

        },
        {
          title    : this.$t('annexes.lessonPlan'),
          key      : 'lessonPlan',
          className: 'demo-table-info-column',
          maxWidth : 90,
          align    : 'center'
        },
        {
          title    : this.$t('annexes.material'),
          key      : 'material',
          className: 'demo-table-info-column',
          maxWidth : 90,
          align    : 'center'
        },
        {
          title    : 'T',
          key      : 't',
          sortable : true,
          className: 'demo-table-info-column',
          maxWidth : 70,
          align    : 'center',
        },
        {
          title    : 'P',
          key      : 'p',
          sortable : true,
          className: 'demo-table-info-column',
          maxWidth : 70,
          align    : 'center',
        },
        {
          title    : 'C',
          key      : 'c',
          sortable : true,
          className: 'demo-table-info-column',
          maxWidth : 70,
          align    : 'center',
        },
        {
          title    : this.$t('observation.observation_17'),
          key      : 'event_total',
          sortable : true,
          className: 'demo-table-info-column',
          align    : 'center',
          maxWidth : 110
        },
      ];
    },

    processingData(v) {
      this.resources = v.map((v, k) => {
            let lesson = _.filter(v.tba_annexs, function (v) {
              return v.type === 'LessonPlan'
            })
            let material = _.filter(v.tba_annexs, function (v) {
              return v.type === 'Material'
            })
            return {
              id            : v.id,
              key           : k + 1,
              status        : (v.group_channels.length > 0) ? _.find(v.group_channels).pivot.content_public === 1 ? 'V' : '' : '',
              doubleGreen   : v.tba_statistics.length > 0 ? _.find(v.tba_statistics).T >= 70 && _.find(v.tba_statistics).P >= 70 ? 'V' : '' : '',
              thumbnail     : this.path.tba + v.id + '/' + v.thumbnail,
              channel_name  : v.name,
              lecture_date  : v.lecture_date,
              teacher       : v.teacher,
              rating        : (v.group_channel_content !== null) ? v.group_channel_content.group_rating_fields.length > 0 ? _.find(v.group_channel_content.group_rating_fields).name : 'x' : 'x',
              subject       : (v.group_channel_content !== null) ? v.group_channel_content.group_subject_fields ? v.group_channel_content.group_subject_fields.subject : this.$t('annexes.other') : this.$t('annexes.other'),
              grade         : (v.group_channel_content !== null) ? v.group_channel_content.grades_id : this.$t('annexes.other'),
              lessonPlan    : lesson.length > 0 ? 'V' : '',
              material      : material.length > 0 ? 'V' : '',
              t             : v.tba_statistics.length > 0 ? _.find(v.tba_statistics).T : 0,
              p             : v.tba_statistics.length > 0 ? _.find(v.tba_statistics).P : 0,
              c             : v.tba_statistics.length > 0 ? _.find(v.tba_statistics).C > 0 ? _.find(v.tba_statistics).C : '' : '',
              event_total   : v.tba_evaluate_events.length > 0 ? _.find(v.tba_evaluate_events).total : 0,
              group_channels: v.group_channels,
            }

          }
      );
    },

    exportData() {
      let url = `/export/lesson`;
      axios.post(url, {
        params: {
          columns  : this.columns,
          user_info: {
            id       : this.$store.state.user.id,
            channelId: this.$router.history.current.params.channelId,
            filter   : this.filter
          },
        }
      }).then(response => {
        // console.log(response);
        window.open(url, '_blank');
      });
    },
    loadMore() {
      let _this = this
      let channelId = this.$route.params.channelId
      let abbr = this.$store.state.abbr
      let url = '/exhibition/tbavideo/get-my-group-channel';


      _this.isLoading = true
      // console.log(1, url, channelId, this.pager.pageIndex > this.pager.last_page);
      _this.pager.pageIndex++;
      if (this.pager.pageIndex > this.pager.last_page) {
        _this.isLoading = false
        return false;
      }

      if (abbr != null) {
        url = '/exhibition/tbavideo/get-district-channel';
        axios.get(url, {
          params: {
            abbr  : abbr,
            page  : _this.pager.pageIndex,
            filter: _this.setFilter
          },
        }).then((response) => {
          let data = response.data.data.list
          setTimeout(() => {
            _.forEach(data.data, function (v) {
              _this.item.push(v)
              _this.isLoading = false
            })
          }, 2000);

        });
        return;
      }

      axios.get(url, {
        params: {
          channelId: channelId,
          page     : _this.pager.pageIndex,
          filter   : _this.setFilter
        },
      }).then((response) => {
        let data = response.data.data.list
        setTimeout(() => {
          _.forEach(data.data, function (v) {
            _this.item.push(v)
            _this.isLoading = false
          })
        }, 2000);
      });
    },
  },

  mounted() {
    this.init();
  }
}
</script>

<style lang="scss">
.ivu-table th.demo-table-info-column {
  background-color: #4F4F4F;
  //color: #f0ad4e;
  color: #fff;
}

.ivu-table th.column-width {
  width: 10%;
}

.ivu-table td.demo-table-info-column {
  background-color: #4F4F4F;
  //color: #f0ad4e;
  color: #fff;
}

.pic {
  //width:60px;
  //height: 70px;
  overflow: hidden;

  img {
    transform: scale(1, 1);
    transition: all 1s ease-out;

    &:hover {
      transform: scale(3, 3);
    }
  }

}


.ivu-table-row-hover {
  //background-color: #000000;
  color: #f0ad4e;
}

.mb-20 {
  margin-bottom: 20px;
}
</style>
