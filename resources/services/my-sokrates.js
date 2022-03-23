import axios from "axios";

export default (() => {
  class Service {
    constructor() {
      this.apiUrl = window.location.origin + "/exhibition/";
      this.apiTbaVideoUrl = this.apiUrl + "tbavideo/";
    }

    /**
     * Get API url
     * @returns {String}
     */
    getApiUrl() {
      return this.apiUrl;
    }

    /**
     * Get TbaVideo API url
     * @returns {String}
     */
    getTbaVideoApiUrl() {
      return this.apiTbaVideoUrl;
    }

    /**
     * Get a list of latest videos
     * @param {int} limit
     * @returns {Promise}
     */
    async getLatestVideoList(limit = 50) {
      let url = this.apiTbaVideoUrl + "get-latest-movies";
      return await axios.get(url, {
        params: {
          limit: limit,
        },
      });
    }
  }

  return new Service();
})();
