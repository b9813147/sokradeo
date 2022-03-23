<template>
  <Card class="default-channel-prompt card flat">
    <div>
      <img
        class="default-channel-prompt-img"
        :src="image('app', 'teammodel/logo.png', true)"
        alt="teammodel-logo"
      />
      <br />
      <p class="default-channel-prompt-title">
        {{ $t("defaultChannelPrompt.msg") }}
      </p>
      <br />
      <i-button
        class="default-channel-prompt-btn-sm"
        type="ghost"
        @click="closeDefaultChannelPrompt"
      >
        {{ $t("defaultChannelPrompt.btnNo") }}
      </i-button>
      <i-button
        class="default-channel-prompt-btn-lg"
        type="success"
        :loading="loading"
        @click="setUserDefaultChannel"
      >
        {{ $t("defaultChannelPrompt.btnYes") }}
      </i-button>
    </div>
  </Card>
</template>

<script>
import HelperUrlPublic from "../../../app/helpers/url/public.vue";
import UserService from "../../../../services/user";

export default {
  mixins: [HelperUrlPublic],
  props: {
    onCancel: {
      type: Function,
      required: true,
    },
  },
  data() {
    return {
      userService: UserService,
      loading: false,
    };
  },
  methods: {
    async setUserDefaultChannel() {
      this.loading = true;
      try {
        let isSuccessful = await this.userService.setUserDefaultChannel();
        if (!isSuccessful) throw new Error(this.$t("messages.error"));
        this.$Message.success(this.$t("messages.success"));
        // Reload page in 1 second
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } catch (error) {
        this.$Message.error(error);
      } finally {
        this.loading = false;
        this.closeDefaultChannelPrompt();
      }
    },
    closeDefaultChannelPrompt() {
      this.onCancel();
    },
  },
};
</script>

<style lang="scss" scoped>
.default-channel-prompt {
  text-align: center;

  .default-channel-prompt-img {
    width: 128px;
  }

  .default-channel-prompt-title {
    font-size: 20px;
  }

  .default-channel-prompt-btn-sm {
    width: 80px;
  }
  .default-channel-prompt-btn-lg {
    width: 120px;
  }
}
</style>
