<template>
  <div class="thumb" style="padding: 0 10px;height: 438px;">
    <img 
      :class="videoImgSrcClass"
      :src="path.tba+item.id+'/'+item.thumbnail+'?'+Math.random()"
      :click="handle"
    ></img>
    <div style="position: relative; color: #32C2F2;">
      <p>
<!--        {{ item.group_channels[0].name }}-->
        <Tooltip v-if="item.group_channels.length > 1" v-bind:content="textChannels" placement="right">
          <Icon type="plus" class="icon-border-radius"></Icon>
        </Tooltip>
      </p>
      <div style="position: absolute; right: 0; top: 0; color: #fff;">
        <Icon v-if="item.playlisted === 1" type="ios-list-outline"></Icon>
        <!--判斷是否待審-->
        <Icon :title="$t('notReady')" type="alert-circled" style="color:yellow " v-if="item.group_channels[0].pivot.content_status === 2"></Icon>
        <Icon type="ios-world-outline" style="color:rgb(100, 250, 1)" v-if="item.group_channels[0].pivot.content_public === 1"></Icon>
        <span v-if="item.playlisted === 1"> {{ item.tba_playlist_tracks.length }}</span>
        <!--                <Icon type="ios-heart-outline"></Icon>-->
        <span v-if="item.hasOwnProperty('tba_statistics') && item.tba_statistics.length !=0">
          <Icon type="ios-circle-filled" style="color:rgb(100, 250, 1)" v-if="item.tba_statistics[0].T >=70 && item.tba_statistics[0].P >=70"></Icon>
        </span>
        <Icon type="eye"></Icon>
        <span>{{ item.hits }}</span>

      </div>
    </div>
<!--    <p style="color: #fff; margin-top: 0.5em;">{{ item.name }}</p>-->
<!--    <p style="color: #999; margin-bottom: 0.5em; font-size: 0.875em;">{{ item.description }}</p>-->
    <div style="color: #64FA01; font-size: 0.875em;">
      <span>{{ $t('teacher') }}: {{ item.teacher === null ? item.user.name : item.teacher }}</span>
      <span style="float: right">{{ item.lecture_date }}</span>
    </div>
  </div>
</template>

<script>
import _ from 'lodash'
import Vuex from 'vuex'

export default {

  props: {
    item: {
      type: Object,
      required: true
    },
    isMobile: {
      type: Boolean,
      default: false,
    },
  },

  computed: _.merge(
      Vuex.mapState(['path']),
      {
        videoImgSrcClass() {
          return this.isMobile ? "video-img-src-mobile" : "video-img-src";
        },
      }
  ),

  data() {
    return {
      textChannels: _.join(_.map(this.item.group_channels, 'name'), ','),
    }
  },

  methods: {

    handle() {
      this.$emit('execute', this.item)
    },

    parseDateTimeToDate(datetime) {
      let date = new Date(datetime)
      return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate()
    },
  },

}
</script>

<style lang="scss" scoped>
p {
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  width: 70%;
}

.icon-border-radius {
  padding: 2px;
  border-style: solid;
  border-width: 1px;
  border-radius: 2px;
}

.video-img-src {
  width: 100%;
  object-fit: contain;
}

.video-img-src-mobile {
  width: 100%;
  height: 150px;
  max-height: fit-content;
  object-fit: cover;
}
</style>
