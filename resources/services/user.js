export default (() => {
  class Service {
    constructor() {
      this.apiUrl = window.location.origin + "/user/";
      this.apiTbaVideoUrl = this.apiUrl + "tbavideo/";
    }

    getApiUrl() {
      return this.apiUrl;
    }

    getTbaVideoApiUrl() {
      return this.apiTbaVideoUrl;
    }

    async setUserDefaultChannel() {
      let res = false;
      await axios
        .put(this.apiTbaVideoUrl + "set-default-channel")
        .then((data) => {
          data = data.data;
          if (!data.status) throw "set user default channel failed";
          res = data.status;
        })
        .catch((e) => {
          console.log(e);
        });
      return res;
    }
  }
  return new Service();
})();
