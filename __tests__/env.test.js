/**
 * This is a test suite for .env file to test the environment variables (MIX)
 * Language: javascript
 * Path: __tests__\env.test.js
 * Author: Nuttaphat Arunoprayoch (@nuttaphat)
 * Updated: 04-JAN-2022
 * Documenation: http://192.168.1.2:10000/legend/sokradeo/wiki/Docs%3A+Testing
 */

test("test MIX_APP_ADMIN_URL", () => {
  expect(process.env.MIX_APP_ADMIN_URL).toBeTruthy();
});

test("test MIX_PUSHER_APP_KEY", () => {
  expect(process.env.MIX_PUSHER_APP_KEY).toBeTruthy();
});

test("test MIX_PUSHER_APP_CLUSTER", () => {
  expect(process.env.MIX_PUSHER_APP_CLUSTER).toBeTruthy();
});

test("test MIX_APP_EXHIBITION_URL to contain 'exhibition'", () => {
  expect(process.env.MIX_APP_EXHIBITION_URL).toBeTruthy();
  expect(process.env.MIX_APP_EXHIBITION_URL).toMatch(/exhibition/);
});

test("test MIX_APP_URL", () => {
  expect(process.env.MIX_APP_URL).toBeTruthy();
});

test("test MIX_SOKAPP_URL to contain 'sokapp5' and 'teammodel'", () => {
  expect(process.env.MIX_SOKAPP_URL).toMatch(/sokapp5/);
  expect(process.env.MIX_SOKAPP_URL).toMatch(/teammodel/);
});

test("test MIX_IES5_URL to contain 'teammodel'", () => {
  expect(process.env.MIX_IES5_URL).toMatch(/teammodel/);
});

test("test MIX_WS_URL to container 'ws'", () => {
  expect(process.env.MIX_WS_URL).toMatch(/ws/);
  expect(process.env.MIX_WS_URL).toMatch(/:/);
});

test("test MIX_WS_PORT", () => {
  expect(process.env.MIX_WS_PORT).toBeTruthy();
});
