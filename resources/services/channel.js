export default (() => {
  class Service {
    constructor() {
      this.baseUrl = window.location.origin;
    }

    getBaseUrl() {
      return this.baseUrl;
    }

    async getChannels() {
      let url = "/user/tbavideo/get-exhibit-info";
      return await axios.get(url, {
        params: {
          dataReqd: false,
          totalReqd: false,
        },
      });
    }

    async getSubmissionChoices(channelId) {
      if (!channelId) return;
      let url = "/exhibition/tbavideo/get-submission-choices";
      return await axios.get(url, {
        params: {
          channel_id: channelId,
        },
      });
    }
  }
  return new Service();
})();
