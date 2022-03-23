// Observation Class Websocket Service
// ------------------------------------------------------------

export default (() => {
  class Service {
    constructor(wsUrl) {
      this.wsUrl = wsUrl;
      this.ws = null;
    }

    getWsUrl() {
      return this.wsUrl;
    }

    getObsrvClassWs() {
      return this.ws;
    }

    init() {
      if (!this.wsUrl) return;

      this.ws = new WebSocket(this.wsUrl);

      this.ws.onopen = () => {
        console.log("Observation Class Websocket Service is open");
      };

      this.ws.onclose = () => {
        console.log("Observation Class Websocket Service is closed");
      };
    }

    isOpen() {
      return this.ws && this.ws.readyState === 1;
    }

    close() {
      if (this.isOpen()) {
        this.ws.close();
      }
    }

    sendMsg(message, verbose = false) {
      // this message is an object and will be stringified
      if (this.isOpen()) {
        if (verbose) {
          console.log(
            "Sending message to Observation Class Websocket Service: ",
            message
          );
        }
        return this.ws.send(JSON.stringify(message));
      }
    }
  }
  return Service;
})();
