<template>
  <div class="media-player-container">
    <video
      id="media-player"
      class="media-player video-js vjs-default-skin vjs-big-play-centered"
      playsinline
      controls
      autoplay
      muted="muted"
      :src="mediaSrc"
    ></video>
  </div>
</template>

<script>
export default {
  name: "CpntMediaPlayer",
  props: ["mediaSrc"],
  data() {
    return {};
  },
};
</script>

<style scoped>
.media-player-container {
  display: flex;
  justify-content: center;

  width: auto;
  height: 215px;
}

.media-player {
  width: 80%;
  height: 100%;
}
</style>
