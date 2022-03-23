// Content Service
// --------------------------------------------------------------

export default (() => {
  class Service {
    constructor() {
      this.apiUrl = window.location.origin;
      this.apiTbaUrl = `${this.apiUrl}/tbas`;
    }

    getApiUrl() {
      return this.apiUrl;
    }

    getApiTbaUrl() {
      return this.apiTbaUrl;
    }

    /**
     * Update Tba TimePoints (comments, evaluate events)
     * @param {int} tbaId - TBA ID
     * @param {int} timePoint - TimePoint (in seconds)
     * @param {string} mode - Mode of operation (inc: increment or dec: decrement)
     * @returns {Promise}
     */
    async updateTbaTimePoints(tbaId, timePoint, mode) {
      let url = `${this.apiTbaUrl}/timepoints/${tbaId}`;
      let data = {
        timePoint: timePoint,
        mode: mode,
      };
      return await axios.put(url, data);
    }

    /**
     * Delete content
     * @param {int} channelId - Channel ID
     * @param {int} contentId - Content ID (Tba Id)
     * @returns {Promise}
     */
    async deleteContent(channelId, contentId) {
      let url = `${this.apiTbaUrl}/channels/${channelId}/content/${contentId}`;
      let data = {channelId: channelId};
      return await axios.delete(url, data);
    }
  }

  return new Service();
})();
