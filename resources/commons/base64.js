// Base64 helpers
import { Base64 } from "js-base64";

export default (() => {
  class Base64Utils {
    constructor() {
      this.base64 = Base64;
    }

    encode(data) {
      return this.base64.encode(data);
    }

    decode(data) {
      return this.base64.decode(data);
    }

    encodeUrl(data) {
      return this.base64.encodeURI(data);
    }
  }
  return new Base64Utils();
})();
