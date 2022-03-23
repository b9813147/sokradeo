// Authentication APi service
// --------------------------------------------------------------

export default (() => {
  class Service {
    constructor() {
      this.apiUrl = window.location.origin + "/auth/";
    }

    getApiUrl() {
      return this.apiUrl;
    }

    // --------------------------------------------------------------
    // Login
    //     - getSokAppLoginUrl
    //     - getIES5LoginUrl
    // --------------------------------------------------------------

    async getSokAppLoginUrl() {
      let url = this.apiUrl + "user/login-to-sokapp/";
      return await axios.get(url).then((response) => response.data);
    }

    async getIES5LoginUrl() {
      let url = this.apiUrl + "user/login-to-ies5/";
      return await axios.get(url).then((response) => response.data);
    }
  }

  return new Service();
})();
