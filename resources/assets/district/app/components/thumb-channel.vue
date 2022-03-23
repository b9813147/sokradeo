<template>
  <div class="thumb-channel-container" @click="handle">
    <!-- Logo -->
    <Img
      :src="thumbnailUrl"
      :alt="'thumb-channel-logo' + '?' + item.id"
      class="thumb-channel-logo"
    />
    <!-- Description -->
    <div class="thumb-channel-desc">
      <p>{{ item.name }}</p>
      <div>
        <Icon type="arrow-right-b"></Icon>
        <span>
          {{ item.total_content }} /
          {{ item.total_content_all ? item.total_content_all : 0 }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Vuex from "vuex";

export default {
  props: ["item"],
  computed: _.merge(Vuex.mapState(["path"]), {
    thumbnailUrl() {
      let url = "/images/app/tw/teammodel/original-black-small.png";
      if (this.item.thumbnail)
        url =
          this.path.groupChannel +
          this.item.id +
          "/" +
          this.item.thumbnail +
          "?" +
          Math.random();
      return url;
    },
  }),
  methods: {
    handle() {
      this.$emit("execute", this.item);
    },
  },
};
</script>

<style lang="scss" scoped>
.thumb-channel-container {
  padding: 0 10px;
  text-align: center;
  cursor: pointer;

  .thumb-channel-logo {
    width: 90%;
    object-fit: contain;
    border-radius: 50%;
  }

  .thumb-channel-desc {
    color: #ffffff;
    text-align: left;

    p {
      color: #32c2f2;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
    }
  }
}
</style>
