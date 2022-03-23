/**
 * This is a test suite for time-formatter.js
 * Language: javascript
 * Path: __tests__\commons\time-formatter.test.js
 * Author: Nuttaphat Arunoprayoch (@nuttaphat)
 * Updated: 10-JAN-2022
 * Documenation: http://192.168.1.2:10000/legend/sokradeo/wiki/Docs%3A+Testing
 */

import TimeFormatter from "../../resources/commons/time-formatter";

const timeFormatter = TimeFormatter;

test("Test Time-Formatter instance", () => {
  expect(timeFormatter).toBeTruthy();
});

test("Test Time-Formatter time format setting", () => {
  expect(timeFormatter.timeFormat).toBeTruthy();
  expect(timeFormatter.timeFormat.hour).toBeTruthy();
  expect(timeFormatter.timeFormat.minute).toBeTruthy();
  expect(timeFormatter.timeFormat.second).toBeTruthy();
});

test("Test formatSecondsToHHMMSS method", () => {
  expect(timeFormatter.formatSecondsToHHMMSS(null)).toBeNull();
  expect(timeFormatter.formatSecondsToHHMMSS(0)).toBe("0:00");
  expect(timeFormatter.formatSecondsToHHMMSS(10)).toBe("0:10");
  expect(timeFormatter.formatSecondsToHHMMSS(60)).toBe("1:00");
  expect(timeFormatter.formatSecondsToHHMMSS(90)).toBe("1:30");
  expect(timeFormatter.formatSecondsToHHMMSS(600)).toBe("10:00");
  expect(timeFormatter.formatSecondsToHHMMSS(3600)).toBe("1:00:00");
  expect(timeFormatter.formatSecondsToHHMMSS(3690)).toBe("1:01:30");
});

test("Test formatSecondsToMMSS method", () => {
  expect(timeFormatter.formatSecondsToMMSS(null)).toBeNull();
  expect(timeFormatter.formatSecondsToMMSS(0)).toBe("00:00");
  expect(timeFormatter.formatSecondsToMMSS(10)).toBe("00:10");
  expect(timeFormatter.formatSecondsToMMSS(60)).toBe("01:00");
  expect(timeFormatter.formatSecondsToMMSS(90)).toBe("01:30");
  expect(timeFormatter.formatSecondsToMMSS(600)).toBe("10:00");
  expect(timeFormatter.formatSecondsToMMSS(3600)).toBe("60:00");
  expect(timeFormatter.formatSecondsToMMSS(3690)).toBe("61:30");
});
