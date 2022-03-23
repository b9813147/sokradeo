export default (() => {
  class TimeFormatter {
    constructor() {
      this.timeFormat = {
        hour: "HH",
        minute: "mm",
        second: "ss",
      };
    }

    formatSecondsToHHMMSS(seconds) {
      // Format seconds to hh:mm:ss
      if (seconds === null) return null;
      let h = ~~(seconds / 3600);
      let m = ~~((seconds % 3600) / 60);
      let s = Math.round(seconds % 60);
      return [h, m > 9 ? m : h ? "0" + m : m || "0", s > 9 ? s : "0" + s]
        .filter(Boolean)
        .join(":");
    }

    formatSecondsToMMSS(seconds) {
      // Format seconds to mm:ss
      if (seconds === null) return null;
      let m = ~~(seconds / 60);
      let s = parseInt(seconds - m * 60);
      while (m.toString().length < 2) m = "0" + m;
      while (s.toString().length <= 1) s = "0" + s;
      return m + ":" + s;
    }
  }

  return new TimeFormatter();
})();
