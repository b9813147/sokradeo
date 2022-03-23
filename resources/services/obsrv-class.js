export default (() => {
  class Service {
    constructor() {
      this.apiUrl = window.location.origin + "/observation-classes/";
    }

    getApiUrl() {
      return this.apiUrl;
    }

    async resumeObsrvClass() {
      return await axios.get(this.apiUrl).then((response) => response.data);
    }

    async createObsrvClass(data) {
      return await axios
        .post(this.apiUrl, data)
        .then((response) => response.data);
    }

    async startObsrvClass(id) {
      return await axios
        .put(this.apiUrl + id, {
          action: "start",
        })
        .then((response) => response.data);
    }

    async endObsrvClass(id) {
      return await axios
        .put(this.apiUrl + id, {
          action: "end",
        })
        .then((response) => response.data);
    }

    async addExtraTime(id, extraSeconds) {
      return await axios
        .put(this.apiUrl + id, {
          action: "et",
          extraSeconds: extraSeconds,
        })
        .then((response) => response.data);
    }
  }
  return new Service();
})();
