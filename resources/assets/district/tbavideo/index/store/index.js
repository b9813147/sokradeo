import Vue from 'vue'
import Vuex from 'vuex'

import BrowserUtil from "../../../../../commons/browser.js";

Vue.use(Vuex)

export default new Vuex.Store({

  state: {
    windowWidth: window.innerWidth,
    windowHeight: window.innerHeight,
    isMobileBrowser: BrowserUtil.isMobileBrowser(),
    url: {},
    path: {
      groupChannel: '/storage/group_channel/',
      tba: '/storage/tba/',
      user: '/storage/user/',
      group: '/storage/group/',
      district: '/storage/district/',
    },
    searchTime: 0,
    keyword: '',
    channel: null,
    filter: {
      eduStages: {list: [], selected: 'none'},
      grades: {list: [], selected: 'none'},
      subjectFields: {list: [], selected: 'none'},
      lectureTypes: {list: [], selected: 'none'},
      tbaFeatures: {list: [], selected: 'none'},
      years: {list: [], selected: 'none'},
    },
    sider: {
      right: {
        collapsed: true,
      },
    },
    user: Globals.user,
    abbr: Globals.abbr,
    extList: {
      'img': ['jpg', 'jpeg', 'png'],
      'audio': ['mp3', 'wav'],
      'video': ['mp4', 'webm', 'mov', 'm4a'],
    },
  },

  getters: {

    logined: (state) => {
      return state.user !== null
    },

    urlAvatar: (state) => {
      return (state.user.thumbnail === null) ? null : (state.path.user + state.user.id + '/' + state.user.thumbnail)
    },

    mediaExtList: (state) => {
      return [].concat(state.extList.audio).concat(state.extList.video);
    },

    obsrvClassAllowed: (state) => {
      // Check current user's bb licenses
      // Obsrv Class is allowed if a user has a valid bb license
      // Validation:
      //  1) id == 2
      //  2) pivot.code == "CLASSES"
      //  3) pivot.status == 1
      let allowed = false;
      if (!state.user.bb_licenses.length > 0) return allowed;
      let bbData = _.find(state.user.bb_licenses, {
        id: 2,
        code: "CLASSES",
      });
      if (bbData && bbData.pivot && bbData.pivot.status === 1) {
        allowed = true;
      }
      return allowed;
    },

  },

  mutations: {

    setWindowSize(state, data) {
      state.windowWidth = data.width;
      state.windowHeight = data.height;
    },

    setIsMobileBrowser(state, data) {
      state.isMobileBrowser = data;
    },

    setSearchTime(state, data) {

      state.searchTime = data
    },

    setKeyword(state, data) {

      state.keyword = data
    },

    setChannel(state, data) {

      state.channel = data
    },

    setFilterList(state, data) {

      if (typeof data.eduStages !== 'undefined') {
        state.filter.eduStages.list = data.eduStages
      }
      if (typeof data.grades !== 'undefined') {
        state.filter.grades.list = data.grades
      }
      if (typeof data.subjectFields !== 'undefined') {
        state.filter.subjectFields.list = data.subjectFields
      }
      if (typeof data.lectureTypes !== 'undefined') {
        state.filter.lectureTypes.list = data.lectureTypes
      }
      if (typeof data.tbaFeatures !== 'undefined') {
        state.filter.tbaFeatures.list = data.tbaFeatures
      }
      if (typeof data.years !== 'undefined') {
        state.filter.years.list = data.years
      }
    },

    setFilterSelected(state, data) {

      if (typeof data.eduStages !== 'undefined') {
        state.filter.eduStages.selected = data.eduStages
      }
      if (typeof data.grades !== 'undefined') {
        state.filter.grades.selected = data.grades
      }
      if (typeof data.subjectFields !== 'undefined') {
        state.filter.subjectFields.selected = data.subjectFields
      }
      if (typeof data.lectureTypes !== 'undefined') {
        state.filter.lectureTypes.selected = data.lectureTypes
      }
      if (typeof data.tbaFeatures !== 'undefined') {
        state.filter.tbaFeatures.selected = data.tbaFeatures
      }
      if (typeof data.years !== 'undefined') {
        state.filter.years.selected = data.years
      }
    },

    selectFilter(state, data) {

      state.filter[data.type].selected = data.value
      state.searchTime = Math.floor(Date.now() / 1000)
    },
  },

  actions: {

    updateWindowSize(ctx, data) {
      ctx.commit('setWindowSize', data);
    },

    updateIsMobileBrowser(ctx) {
      let isMobileBrowser = BrowserUtil.isMobileBrowser();
      ctx.commit('setIsMobileBrowser', isMobileBrowser);
    },

    updateSearchTime(ctx) {

      ctx.commit('setSearchTime', Math.floor(Date.now() / 1000))
    },

    setKeyword(ctx, data) {

      ctx.commit('setKeyword', data)
    },

    setChannel(ctx, data) {

      ctx.commit('setChannel', data)
    },

    initFilterList(ctx) {

      axios.get('/exhibition/tbavideo/get-filters', {
        params: {}
      })
        .then((data) => {
          data = data.data
          if (!data.status) {
            return
          }
          ctx.commit('setFilterList', data.data)
        })
        .catch((e) => {
          console.log(e)
        })

    },

    initFilterSelected(ctx) {

      let data = {
        eduStages: 'none',
        grades: 'none',
        subjectFields: 'none',
        lectureTypes: 'none',
        tbaFeatures: 'none',
        years: 'none',
      }
      ctx.commit('setFilterSelected', data)
    },

    selectFilter(ctx, data) {

      ctx.commit('selectFilter', data)
    },

  },

})
