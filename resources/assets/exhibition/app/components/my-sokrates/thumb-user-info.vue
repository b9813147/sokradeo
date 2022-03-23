<template>
  <div class="user-info-container">
    <h1>{{ $store.state.user.name }}</h1>
    <h3 v-if="$store.state.user.group_channel_name">
      <Icon type="university"></Icon>
      <router-link
        :to="'/myChannel/' + $store.state.user.group_channel_id"
        class="available"
      >
        {{ $store.state.user.group_channel_name }}
      </router-link>
    </h3>
  </div>
</template>

<script>
import Vuex from "vuex";

export default {
  name: "user-info",
  computed: _.merge(
    Vuex.mapState(["path", "user"]),
    Vuex.mapGetters(["logined"])
  ),
};
</script>

<style lang="scss" scoped>
.user-info-container {
  h1 {
    color: #fff;
  }

  .available {
    color: #acd6ff;
    cursor: pointer;
  }
}
</style>
