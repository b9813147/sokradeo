export default (() => {
  class Service {
    constructor() {
      this.apiUrl = window.location.origin + "/member/";
    }

    getApiUrl() {
      return this.apiUrl;
    }

    async update(id, data) {
      return await axios.put(this.apiUrl + id, data);
    }
  }
  return new Service();
})();
