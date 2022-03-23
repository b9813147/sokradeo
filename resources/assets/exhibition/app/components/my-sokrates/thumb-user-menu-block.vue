<template>
  <div class="user-menu-block-container">
    <div :class="{ available: isExecutable }" @click="exeUserMenu">
      <h3 v-if="userMenu.name">
        {{ userMenu.name }}
        <span class="highlight-info">
          {{ userMenu.count }}
        </span>
      </h3>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

export default {
  name: "user-menu-block",
  props: {
    userMenu: {
      type: Object,
      required: true,
      default: () => {
        return {
          name: "",
          count: 0,
          exeFunc: null,
        };
      },
    },
  },
  emits: ["exe-user-menu"],
  computed: _.merge(
    Vuex.mapState(["path", "user"]),
    Vuex.mapGetters(["logined"]),
    {
      isExecutable() {
        return _.isFunction(this.userMenu.exeFunc);
      },
    }
  ),
  methods: {
    exeUserMenu() {
      if (!this.isExecutable) return;
      this.$emit("exe-user-menu", this.userMenu);
    },
  },
};
</script>

<style lang="scss" scoped>
.user-menu-block-container {
  .available {
    color: #acd6ff;
    cursor: pointer;
  }
  .highlight-info {
    color: #acd6ff;
    display: inline;
    margin-left: 10px;
  }
}
</style>
