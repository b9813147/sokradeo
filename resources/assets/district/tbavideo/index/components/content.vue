<template>
  <article class="content">
    <section v-if="info === null" style="text-align: center;">
      <!--            <h2>{{$t('messages.no_content')}}</h2>-->
    </section>
    <section v-else>
      <Row v-bind:gutter="16">
        <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8" class="thumb">
          <img style="width: 100%; height: 256px; object-fit: cover;"
               v-bind:src="path.tba+info.tba.id+'/'+info.tba.thumbnail+'?'+Math.random()">
          </img>
        </Col>
        <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
          <h2>{{ info.tba.name }}</h2>
          <div v-if="info.tba.playlisted === 1">
            <Icon type="ios-list-outline"></Icon>
            <span>{{ info.tba.tba_playlist_tracks.length }}</span>
          </div>
          <div style="position: relative; color: #32C2F2;" v-if="channel.info !== null">
            <router-link :to="'/myChannel/'+ channel.info.data.id" style="color: #32C2F2;">
              {{ channel.info.data.name }}
            </router-link>
          </div>
          <div>
            <span>{{ $t('teacher') }}</span>:
            <span v-if="info.tba.teacher === null">{{ info.tba.user.name }}</span>
            <span v-else>{{ info.tba.teacher }}</span>
          </div>
          <div>
            <span>{{ $t('subjectField') }}</span>:
            <span v-if="info.tba.subject_field === null">{{ $t('annexes.other') }}</span>
            <span v-else>{{ info.tba.subject_field.text }}</span>
          </div>
          <div>
            <span>{{ $t('subject') }}</span>:
            <span v-if="info.tba.subject === null">{{ $t('undefined') }}</span>
            <span v-else>{{ info.tba.subject }}</span>
          </div>
          <!--                    <div>-->
          <!--                        <span>{{$t('eduStage')}}</span>:-->
          <!--                        <span v-if="info.tba.educational_stage === null">{{$t('none')}}</span>-->
          <!--                        <span v-else>{{info.tba.educational_stage.text}}</span>-->
          <!--                    </div>-->
          <div>
            <span>{{ $t('grade') }}</span>:
            <span v-if="info.tba.grade === null">{{ $t('annexes.other') }}</span>
            <span v-else>{{ info.tba.grade }}</span>
          </div>
          <div>
            <span>{{ $t('lecture_type') }}</span>:
            <span v-if="info.tba.lecture_type === null">{{ $t('none') }}</span>
            <span v-else>{{ info.tba.lecture_type }}</span>
          </div>
          <div>
            <span>{{ $t('lecture_date') }}</span>:
            <span v-if="info.tba.lecture_date === null">{{ $t('none') }}</span>
            <span v-else>{{ info.tba.lecture_date }}</span>
          </div>
          <div>
            <span>{{ $t('locale') }}</span>:
            <span v-if="info.tba.locale === null">{{ $t('none') }}</span>
            <span v-else>{{ info.tba.locale.text }}</span>
          </div>
          <div>
            <p>{{ $t('sokrates') }}</p>
            <Icon :title="$t('notReady')" type="ios-locked-outline" style="color:yellow;font-size: 95px;"
                  v-if="info.tba.content_status === 2"></Icon>
            <Row class="annexes" v-bind:gutter="16">
              <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button type="primary" icon="ios-eye" long v-on:click="exeContent">{{ $t('watch') }}
                </Button>
              </Col>
              <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8" v-if="report">
                <Button class="resrc" long @click="modal = true">{{ $t('report') }}
                </Button>
              </Col>
              <Col v-for="(v, i) in annexes.hiTeachNote" v-bind:key="'hiteachnote'+i"
                   v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button class="resrc" long v-on:click="exeAnnex(v.id)">{{ $t('annexes.hiTeachNote') }}
                </Button>
              </Col>
              <Col v-for="(v, i) in annexes.lessonPlan" v-bind:key="'lessonplan'+i"
                   v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button class="resrc" long v-on:click="exeAnnex(v.id)">{{ $t('annexes.lessonPlan') }}
                </Button>
              </Col>
              <Col v-for="(v, i) in annexes.material" v-bind:key="'material'+i"
                   v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button class="resrc" long v-on:click="exeAnnex(v.id)">{{ $t('annexes.material') }}
                </Button>
              </Col>
              <Col v-for="(v, i) in annexes.other" v-bind:key="'other'+i"
                   v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button class="resrc" long v-on:click="exeAnnex(v.id)">{{ $t('annexes.other') }}</Button>
              </Col>
              <Col v-for="(v, i) in annexes.link" v-bind:key="'link'+i"
                   v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
                <Button class="resrc" long v-on:click="exeAnnex(v.id, true)">{{ $t('annexes.link') }}
                </Button>
              </Col>
            </Row>
          </div>
          <!-- 暫時註解
          <div style="margin: 8px 0;">
              <Button type="primary" icon="ios-eye" v-on:click="exeContent">{{$t('watch')}}</Button>
          </div>
          -->
        </Col>
        <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
          <span v-html="info.tba.description"></span>
        </Col>
      </Row>
      <!-- 暫時註解
      <Row v-bind:gutter="16">
          <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8">
              <div class="annexes">
                  <Button v-for="(v, i) in annexes.hiTeachNote" v-bind:key="'hiteachnote'+i" v-on:click="exeAnnex(v.id)"      >{{$t('annexes.hiTeachNote')}}</Button>
                  <Button v-for="(v, i) in annexes.lessonPlan"  v-bind:key="'lessonplan' +i" v-on:click="exeAnnex(v.id)"      >{{$t('annexes.lessonPlan' )}}</Button>
                  <Button v-for="(v, i) in annexes.material"    v-bind:key="'material'   +i" v-on:click="exeAnnex(v.id)"      >{{$t('annexes.material'   ) + ':' + v.name}}</Button>
                  <Button v-for="(v, i) in annexes.other"       v-bind:key="'other'      +i" v-on:click="exeAnnex(v.id)"      >{{$t('annexes.other'      ) + ':' + v.name}}</Button>
                  <Button v-for="(v, i) in annexes.link"        v-bind:key="'link'       +i" v-on:click="exeAnnex(v.id, true)">{{$t('annexes.link'       ) + ':' + v.name}}</Button>
              </div>
          </Col>
          <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8"></Col>
          <Col v-bind:xs="24" v-bind:sm="8" v-bind:md="8" v-bind:lg="8"></Col>
      </Row>
      -->
      <Modal
          v-model="modal"
          :footer-hide=true
          width="1024"
      >
        <img style="width: 100%; height: 100%; object-fit: cover;"
             :src="path.tba + info.tba.id + '/report.png'"
             alt="Report"
        >
      </Modal>
    </section>
  </article>
</template>

<script>
import _    from 'lodash';
import Vuex from 'vuex';

export default {

  data() {
    return {
      groupIds: null,
      info    : null,
      annexes : {
        hiTeachNote: [],
        lessonPlan : [],
        material   : [],
        link       : [],
        other      : [],
      },
      channel : {
        info: [],
      },
      modal   : false,
      report  : false
    };
  },

  computed: _.merge(
      Vuex.mapState(['path', 'user']),
      Vuex.mapGetters(['logined']),
  ),

  watch: {
    '$route'(to, from) {
      this.init();
    },
  },

  methods: {

    init() {
      // console.log(this.$route.query.groupIds.split(','))
      // this.groupIds = typeof this.$route.query.groupIds === 'undefined' ? null  : this.$route.query.groupIds.split(',')
      this.groupIds = typeof this.$route.query.groupIds === 'undefined'
                      ? null
                      : this.$route.query.groupIds.split(',');
      // console.log(_.isNull(this.groupIds) ? null : _.join(this.groupIds, ','))
      this.checkPolicy(this.$route.params.contentId);
      this.getContentInfo(this.$route.params.contentId);
      this.getChannelName();
      this.isReport();
    },

    checkPolicy(contentId) {

      axios.get('/exhibition/tbavideo/check-policy', {
        params: {
          contentId: contentId,
        },
      }).then((data) => {
        data = data.data;
        if (!data.status) {
          this.$emit('check-logined', true, false, false, false);
          return;
        }
        if (!data.data) {
          this.$emit('check-logined', true, false, false, false);
        }
      }).catch((e) => {
        console.log(e);
      });

    },

    getContentInfo(contentId) {

      this.info = null;
      this.annexes.hiTeachNote = [];
      this.annexes.lessonPlan = [];
      this.annexes.material = [];
      this.annexes.link = [];
      this.annexes.other = [];

      axios.get('/exhibition/tbavideo/get-content-info', {
        params: {
          contentId: contentId,
          groupIds : _.isNull(this.groupIds)
                     ? null
                     : _.join(this.groupIds, ','),
        },
      }).then((data) => {
        data = data.data;
        if (!data.status) {
          return;
        }
        this.info = {
          tba   : data.data.tba,
          videos: data.data.videos,
        };
        this.annexes.hiTeachNote = data.data.annexes.hiTeachNote;
        this.annexes.lessonPlan = data.data.annexes.lessonPlan;
        this.annexes.material = data.data.annexes.material;
        this.annexes.link = data.data.annexes.link;
        this.annexes.other = data.data.annexes.other;
      }).catch((e) => {
        console.log(e);
      });

    },

    exeContent() {
      if (this.logined) {
        if (!document.cookie) {
          location.reload();
          return;
        }
        // user group info
        let userInfo = this.user.groups;
        // channel group ID
        let groupIds = this.groupIds;
        //channel ID
        let channelId = this.$route.query.channelId;
        // channel public
        let channelPublic = (this.channel.info)
                            ? this.channel.info.data.public
                            : null;

        let myGroupAuth = false;
        let myGroupUrl = '';
        if (groupIds) {
          userInfo.forEach((u) => {
            if (myGroupAuth) {
              return
            }
            groupIds.forEach((g) => {
              if (u.pivot.group_id === parseInt(g)) {
                if (u.pivot.member_duty === 'Expert' || u.pivot.member_duty === 'Admin' || channelPublic === 1) {
                  myGroupAuth = true;
                  myGroupUrl = !this.logined
                               ? `/tbavideo/watch-as-open?contentId=${this.info.tba.id}&channelId=${channelId}`
                               : `/group/${u.pivot.group_id}/watch/channel/${channelId}/tbavideo?contentId=${this.info.tba.id}&groupIds=${u.pivot.group_id}&channelId=${channelId}`;
                }
              }
            });
          });

          if (myGroupAuth) {
            return window.open(myGroupUrl);
          }
        }


        let url = !this.logined
                  ? `/exhibition/tbavideo/watch-as-open?contentId=${this.info.tba.id}`
            //暫時註解
                  : '/exhibition/tbavideo/watch?contentId=' + this.info.tba.id + (_.isNull(this.groupIds)
                                                                                  ? ''
                                                                                  : '&groupIds=' + _.join(this.groupIds, ',')) + '&channelId=' + channelId;
        window.open(url);
      } else {
        let url = !this.logined
                  ? `/exhibition/tbavideo/watch-as-open?contentId=${this.info.tba.id}`
            //暫時註解
                  : '/exhibition/tbavideo/watch?contentId=' + this.info.tba.id + (_.isNull(this.groupIds)
                                                                                  ? ''
                                                                                  : '&groupIds=' + _.join(this.groupIds, ',')) + '&channelId=' + channelId;
        window.open(url);
      }
    },

    exeAnnex(annexId, blank = false) {
      let url = '/exhibition/tbavideo/exe-content-annex?annexId=' + annexId + (_.isNull(this.groupIds)
                                                                               ? ''
                                                                               : '&groupIds=' + _.join(this.groupIds, ','));
      if (blank) {
        window.open(url);
      } else {
        window.location.href = url;
      }
    },
    getChannelName() {

      let channelId = this.$route.query.channelId;
      let url = `/exhibition/tbavideo/get-channel-info/`;
      if (channelId) {
        axios.get(url, {
          params: {channelId: channelId},
        }).then((data) => {
          this.channel.info = data.data;

        }).catch((e) => {
          this.channel.info = null;
        });
      }
      this.channel.info = null;
    },

    isReport() {

      let _this = this;
      let url = `/exists/${this.$route.params.contentId}`;

      axios.get(url).then((response) => {
        this.report = response.data.status;
      }).catch((error) => {
        console.log(error);
      });
    }

  },

  mounted() {

    this.init();

  },

};
</script>

<style lang="scss" scoped>
.content {
  h2 {
    color: #fff;
  }

  .annexes {
    button {
      margin: 2px;
    }

    button.resrc {
      color: #bcbcbc;
      background-color: #333;
    }
  }
}

</style>
